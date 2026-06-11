<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\UserInvitation;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserInvitationRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_open_valid_invitation_registration_form(): void
    {
        $invitation = $this->invitation();

        $response = $this->get(route('invitations.register', ['token' => $invitation->token]));

        $response->assertOk();
        $response->assertSeeText('Pendaftaran Akun SIMAS');
        $response->assertSeeText($invitation->phone);
        $response->assertSeeText('Operator');
    }

    public function test_submitting_invitation_stores_pending_data_without_creating_user(): void
    {
        $invitation = $this->invitation();

        $response = $this->post(route('invitations.submit', ['token' => $invitation->token]), [
            'name' => 'Calon Operator',
            'email' => 'candidate@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('invitations.register', ['token' => $invitation->token]));

        $invitation->refresh();

        $this->assertSame(UserInvitation::STATUS_SUBMITTED, $invitation->status);
        $this->assertSame('candidate@example.test', $invitation->email);
        $this->assertTrue(Hash::check('password123', $invitation->password_hash));
        $this->assertNotNull($invitation->submitted_at);
        $this->assertDatabaseMissing('users', ['email' => 'candidate@example.test']);

        $this->get(route('invitations.register', ['token' => $invitation->token]))
            ->assertOk()
            ->assertSeeText('Pendaftaran Berhasil Dikirim')
            ->assertDontSeeText('Kirim Pendaftaran');
    }

    public function test_expired_invitation_is_marked_expired_and_cannot_show_form(): void
    {
        $invitation = $this->invitation(['expires_at' => now()->subMinute()]);

        $response = $this->get(route('invitations.register', ['token' => $invitation->token]));

        $response->assertOk();
        $response->assertSeeText('Link undangan telah kedaluwarsa.');
        $this->assertSame(UserInvitation::STATUS_EXPIRED, $invitation->fresh()->status);
    }

    public function test_email_from_submitted_invitation_cannot_be_submitted_again(): void
    {
        $submitted = $this->invitation([
            'token' => 'submitted-token',
            'email' => 'waiting@example.test',
            'status' => UserInvitation::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
        $this->assertSame(UserInvitation::STATUS_SUBMITTED, $submitted->status);

        $response = $this->post(route('invitations.submit', ['token' => $this->invitation()->token]), [
            'name' => 'Duplikat',
            'email' => 'waiting@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    private function invitation(array $attributes = []): UserInvitation
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::firstOrCreate(['name' => 'Masjid Undangan']);
        $role = Role::where('name', Role::OPERATOR)->firstOrFail();

        return UserInvitation::create(array_merge([
            'token' => fake()->unique()->regexify('[A-Za-z0-9]{64}'),
            'phone' => '628123456789',
            'mosque_id' => $mosque->id,
            'role_id' => $role->id,
            'status' => UserInvitation::STATUS_DRAFT,
            'expires_at' => now()->addDays(3),
        ], $attributes));
    }
}
