<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_can_open_edit_page_with_existing_access(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Edit']);
        $operator = Role::where('name', Role::OPERATOR)->firstOrFail();
        $user = $this->createUser('User Edit', 'user-edit@example.test');
        $user->assignRole(Role::OPERATOR, $mosque->id);

        $response = $this->actingAs($this->superuser())->get(route('users.edit', $user));

        $response->assertOk();
        $response->assertSeeText('Edit User');
        $response->assertSee('value="'.$mosque->id.'" selected', false);
        $response->assertSee('value="'.$operator->id.'" selected', false);
    }

    public function test_superuser_can_update_user_access_and_reset_password(): void
    {
        $this->seed(RoleSeeder::class);

        $oldMosque = Mosque::create(['name' => 'Masjid Lama']);
        $newMosque = Mosque::create(['name' => 'Masjid Baru']);
        $newRole = Role::where('name', Role::SEKRETARIS)->firstOrFail();
        $user = $this->createUser('Nama Lama', 'old@example.test', $oldMosque->id);
        $user->assignRole(Role::OPERATOR, $oldMosque->id);

        $response = $this->actingAs($this->superuser())->put(route('users.update', $user), [
            'name' => 'Nama Baru',
            'email' => 'new@example.test',
            'reset_password' => '1',
            'password' => 'password456',
            'password_confirmation' => 'password456',
            'accesses' => [
                ['mosque_id' => $newMosque->id, 'role_id' => $newRole->id],
            ],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'Data user berhasil diperbarui.');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('new@example.test', $user->email);
        $this->assertTrue(Hash::check('password456', $user->password));
        $this->assertTrue($user->hasRoleInMosque(Role::SEKRETARIS, $newMosque->id));
        $this->assertFalse($user->hasRoleInMosque(Role::OPERATOR, $oldMosque->id));
        $this->assertNull($user->active_mosque_id);
    }

    public function test_super_superuser_can_promote_user_to_global_superuser_without_changing_password(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Awal']);
        $user = $this->createUser('Calon Superuser', 'promote@example.test');
        $passwordBefore = $user->password;
        $user->assignRole(Role::OPERATOR, $mosque->id);

        $response = $this->actingAs($this->superSuperuser())->put(route('users.update', $user), [
            'name' => 'Calon Superuser',
            'email' => 'promote@example.test',
            'is_superuser' => '1',
        ]);

        $response->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertTrue($user->isSuperuser());
        $this->assertSame($passwordBefore, $user->password);
        $this->assertCount(1, $user->roles);
    }

    public function test_superuser_cannot_promote_user_to_global_superuser(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Biasa']);
        $user = $this->createUser('Target Promosi', 'reject-promotion@example.test');
        $user->assignRole(Role::OPERATOR, $mosque->id);

        $response = $this->actingAs($this->superuser())->put(route('users.update', $user), [
            'name' => 'Target Promosi',
            'email' => 'reject-promotion@example.test',
            'is_superuser' => '1',
        ]);

        $response->assertSessionHasErrors('is_superuser');
        $this->assertFalse($user->fresh()->isSuperuser());
        $this->assertTrue($user->fresh()->hasRoleInMosque(Role::OPERATOR, $mosque->id));
    }

    public function test_regular_user_cannot_be_updated_without_any_mosque_access(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Wajib']);
        $user = $this->createUser('User Akses', 'required-access@example.test');
        $user->assignRole(Role::OPERATOR, $mosque->id);

        $response = $this->actingAs($this->superuser())->put(route('users.update', $user), [
            'name' => 'User Akses',
            'email' => 'required-access@example.test',
            'accesses' => [],
        ]);

        $response->assertSessionHasErrors('accesses');
        $this->assertTrue($user->fresh()->hasRoleInMosque(Role::OPERATOR, $mosque->id));
    }

    public function test_superuser_role_cannot_be_assigned_as_mosque_access_during_update(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Aman']);
        $operator = Role::where('name', Role::OPERATOR)->firstOrFail();
        $superuserRole = Role::where('name', Role::SUPERUSER)->firstOrFail();
        $user = $this->createUser('User Role', 'protected-role@example.test');
        $user->roles()->attach($operator->id, ['mosque_id' => $mosque->id]);

        $response = $this->actingAs($this->superuser())->put(route('users.update', $user), [
            'name' => 'User Role',
            'email' => 'protected-role@example.test',
            'accesses' => [
                ['mosque_id' => $mosque->id, 'role_id' => $superuserRole->id],
            ],
        ]);

        $response->assertSessionHasErrors('accesses.0.role_id');
        $this->assertTrue($user->fresh()->hasRoleInMosque(Role::OPERATOR, $mosque->id));
        $this->assertFalse($user->fresh()->isSuperuser());
    }

    public function test_admin_masjid_cannot_edit_or_update_user(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Admin']);
        $admin = $this->createUser('Admin', 'admin-edit@example.test');
        $admin->assignRole(Role::ADMIN_MASJID, $mosque->id);
        $user = $this->createUser('Target', 'target@example.test');

        $this->actingAs($admin)->get(route('users.edit', $user))->assertForbidden();
        $this->put(route('users.update', $user), [
            'name' => 'Target Diubah',
            'email' => 'target-updated@example.test',
            'accesses' => [
                ['mosque_id' => $mosque->id, 'role_id' => Role::where('name', Role::OPERATOR)->value('id')],
            ],
        ])->assertForbidden();
    }

    private function superuser(): User
    {
        $user = $this->createUser('Super Admin', fake()->unique()->safeEmail());
        $user->assignRole(Role::SUPERUSER);

        return $user;
    }

    private function superSuperuser(): User
    {
        $user = $this->createUser('Pemilik Sistem', fake()->unique()->safeEmail());
        $user->assignRole(Role::SUPER_SUPERUSER);

        return $user;
    }

    private function createUser(string $name, string $email, ?int $activeMosqueId = null): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'active_mosque_id' => $activeMosqueId,
        ]);
    }
}
