<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_can_view_user_access_list(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Nurul Iman']);
        $superuser = $this->createUser('Super Admin', 'super-users@example.test');
        $superuser->assignRole(Role::SUPERUSER);

        $operator = $this->createUser('Operator Masjid', 'operator@example.test');
        $operator->assignRole(Role::OPERATOR, $mosque->id);

        $response = $this->actingAs($superuser)->get(route('users.index'));

        $response->assertOk();
        $response->assertSeeText('User & Hak Akses');
        $response->assertSeeText('Super Admin');
        $response->assertSeeText('Akses Global');
        $response->assertSeeText('Operator Masjid');
        $response->assertSeeText('Masjid Nurul Iman');
        $response->assertSeeText('Edit');
    }

    public function test_admin_masjid_cannot_view_user_access_list(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Terbatas']);
        $admin = $this->createUser('Admin Masjid', 'admin-users@example.test');
        $admin->assignRole(Role::ADMIN_MASJID, $mosque->id);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertForbidden();
    }

    private function createUser(string $name, string $email): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);
    }
}
