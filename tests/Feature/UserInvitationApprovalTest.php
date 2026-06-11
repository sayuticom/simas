<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserInvitationApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_can_approve_submitted_mosque_invitation(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        $mosque = Mosque::create(['name' => 'Masjid Approval']);
        $role = Role::where('name', Role::OPERATOR)->firstOrFail();
        $invitation = $this->submittedInvitation($role, $mosque);

        $response = $this->actingAs($superuser)
            ->post(route('user-invitations.approve', $invitation));

        $response->assertRedirect(route('user-invitations.index'));
        $response->assertSessionHas('success');

        $user = User::where('email', $invitation->email)->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertSame($mosque->id, $user->active_mosque_id);
        $this->assertTrue($user->hasRoleInMosque(Role::OPERATOR, $mosque->id));

        $invitation->refresh();
        $this->assertSame(UserInvitation::STATUS_APPROVED, $invitation->status);
        $this->assertSame($superuser->id, $invitation->approved_by);
        $this->assertNotNull($invitation->approved_at);
    }

    public function test_super_superuser_can_approve_global_superuser_invitation(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superSuperuser();
        $role = Role::where('name', Role::SUPERUSER)->firstOrFail();
        $invitation = $this->submittedInvitation($role, null, ['email' => 'global@example.test']);

        $this->actingAs($superuser)
            ->post(route('user-invitations.approve', $invitation))
            ->assertRedirect(route('user-invitations.index'));

        $user = User::where('email', 'global@example.test')->firstOrFail();
        $this->assertNull($user->active_mosque_id);
        $this->assertTrue($user->isSuperuser());
        $this->assertNull($user->roles()->where('roles.name', Role::SUPERUSER)->firstOrFail()->pivot->mosque_id);
    }

    public function test_superuser_cannot_create_or_approve_global_superuser_invitation(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        $response = $this->actingAs($superuser)->post(route('user-invitations.store'), [
            'phone' => '08123456789',
            'name' => 'Global Ditolak',
            'is_superuser' => '1',
        ]);

        $response->assertSessionHasErrors('is_superuser');

        $role = Role::where('name', Role::SUPERUSER)->firstOrFail();
        $invitation = $this->submittedInvitation($role, null, ['email' => 'forbidden-global@example.test']);

        $this->post(route('user-invitations.approve', $invitation))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['email' => 'forbidden-global@example.test']);
        $this->assertSame(UserInvitation::STATUS_SUBMITTED, $invitation->fresh()->status);
    }

    public function test_expired_submitted_invitation_is_not_approved(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        $mosque = Mosque::create(['name' => 'Masjid Expired']);
        $role = Role::where('name', Role::OPERATOR)->firstOrFail();
        $invitation = $this->submittedInvitation($role, $mosque, [
            'email' => 'expired@example.test',
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($superuser)
            ->post(route('user-invitations.approve', $invitation));

        $response->assertSessionHas('error');
        $this->assertSame(UserInvitation::STATUS_EXPIRED, $invitation->fresh()->status);
        $this->assertDatabaseMissing('users', ['email' => 'expired@example.test']);
    }

    public function test_superuser_can_cancel_draft_or_submitted_invitation_without_creating_user(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        $mosque = Mosque::create(['name' => 'Masjid Cancel']);
        $role = Role::where('name', Role::OPERATOR)->firstOrFail();

        foreach ([UserInvitation::STATUS_DRAFT, UserInvitation::STATUS_SUBMITTED] as $status) {
            $invitation = $this->submittedInvitation($role, $mosque, [
                'token' => $status.'-cancel-token',
                'email' => $status.'@example.test',
                'status' => $status,
            ]);

            $this->actingAs($superuser)
                ->post(route('user-invitations.cancel', $invitation))
                ->assertSessionHas('success');

            $this->assertSame(UserInvitation::STATUS_CANCELLED, $invitation->fresh()->status);
            $this->assertDatabaseMissing('users', ['email' => $invitation->email]);
        }
    }

    public function test_non_superuser_cannot_approve_or_cancel_invitation(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Admin']);
        $role = Role::where('name', Role::OPERATOR)->firstOrFail();
        $invitation = $this->submittedInvitation($role, $mosque);
        $admin = User::create([
            'name' => 'Admin Masjid',
            'email' => 'admin@example.test',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $mosque->id);

        $this->actingAs($admin)
            ->post(route('user-invitations.approve', $invitation))
            ->assertForbidden();
        $this->post(route('user-invitations.cancel', $invitation))
            ->assertForbidden();
    }

    private function submittedInvitation(Role $role, ?Mosque $mosque, array $attributes = []): UserInvitation
    {
        return UserInvitation::create(array_merge([
            'token' => fake()->unique()->regexify('[A-Za-z0-9]{64}'),
            'phone' => '628123456789',
            'name' => 'Calon User',
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make('password123'),
            'mosque_id' => $mosque?->id,
            'role_id' => $role->id,
            'status' => UserInvitation::STATUS_SUBMITTED,
            'expires_at' => now()->addDays(3),
            'submitted_at' => now(),
        ], $attributes));
    }

    private function superuser(): User
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.test',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole(Role::SUPERUSER);

        return $user;
    }

    private function superSuperuser(): User
    {
        $user = User::create([
            'name' => 'Pemilik Sistem',
            'email' => 'owner@example.test',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole(Role::SUPER_SUPERUSER);

        return $user;
    }
}
