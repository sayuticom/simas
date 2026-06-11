<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_can_open_create_user_page(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        Mosque::create(['name' => 'Masjid Form']);

        $response = $this->actingAs($superuser)->get(route('users.create'));

        $response->assertOk();
        $response->assertSeeText('Tambah User Baru');
        $response->assertSeeText('Masjid Form');
        $response->assertSeeText('Sekretaris');
    }

    public function test_superuser_can_create_regular_user_with_multiple_mosque_accesses(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        $firstMosque = Mosque::create(['name' => 'Masjid Pertama']);
        $secondMosque = Mosque::create(['name' => 'Masjid Kedua']);
        $operatorRole = Role::where('name', Role::OPERATOR)->firstOrFail();
        $treasurerRole = Role::where('name', Role::BENDAHARA)->firstOrFail();

        $response = $this->actingAs($superuser)->post(route('users.store'), [
            'name' => 'User Dua Masjid',
            'email' => 'multi-access@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accesses' => [
                ['mosque_id' => $firstMosque->id, 'role_id' => $operatorRole->id],
                ['mosque_id' => $secondMosque->id, 'role_id' => $treasurerRole->id],
            ],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User baru berhasil ditambahkan.');

        $user = User::where('email', 'multi-access@example.test')->firstOrFail();
        $this->assertTrue($user->hasRoleInMosque(Role::OPERATOR, $firstMosque->id));
        $this->assertTrue($user->hasRoleInMosque(Role::BENDAHARA, $secondMosque->id));
    }

    public function test_super_superuser_can_create_global_superuser_without_mosque_access(): void
    {
        $this->seed(RoleSeeder::class);

        $response = $this->actingAs($this->superSuperuser())->post(route('users.store'), [
            'name' => 'Superuser Baru',
            'email' => 'new-superuser@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_superuser' => '1',
        ]);

        $response->assertRedirect(route('users.index'));

        $user = User::where('email', 'new-superuser@example.test')->firstOrFail();
        $this->assertTrue($user->isSuperuser());
        $this->assertNull($user->roles()->where('roles.name', Role::SUPERUSER)->firstOrFail()->pivot->mosque_id);
    }

    public function test_superuser_cannot_create_global_superuser(): void
    {
        $this->seed(RoleSeeder::class);

        $response = $this->actingAs($this->superuser())->post(route('users.store'), [
            'name' => 'Superuser Ditolak',
            'email' => 'forbidden-superuser@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_superuser' => '1',
        ]);

        $response->assertSessionHasErrors('is_superuser');
        $this->assertDatabaseMissing('users', ['email' => 'forbidden-superuser@example.test']);
    }

    public function test_regular_user_requires_mosque_access_and_cannot_receive_superuser_role(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = $this->superuser();
        $mosque = Mosque::create(['name' => 'Masjid Aman']);
        $superuserRole = Role::where('name', Role::SUPERUSER)->firstOrFail();

        $response = $this->actingAs($superuser)->post(route('users.store'), [
            'name' => 'Tanpa Akses',
            'email' => 'without-access@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('accesses');

        $response = $this->post(route('users.store'), [
            'name' => 'Role Terlarang',
            'email' => 'forbidden-role@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accesses' => [
                ['mosque_id' => $mosque->id, 'role_id' => $superuserRole->id],
            ],
        ]);
        $response->assertSessionHasErrors('accesses.0.role_id');
        $this->assertDatabaseMissing('users', ['email' => 'forbidden-role@example.test']);
    }

    public function test_admin_masjid_cannot_create_user(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Admin']);
        $admin = User::create([
            'name' => 'Admin Masjid',
            'email' => 'admin-create@example.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $mosque->id);

        $this->actingAs($admin)->get(route('users.create'))->assertForbidden();
        $this->post(route('users.store'), [
            'name' => 'Tidak Diizinkan',
            'email' => 'not-allowed@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accesses' => [['mosque_id' => $mosque->id, 'role_id' => Role::where('name', Role::OPERATOR)->value('id')]],
        ])->assertForbidden();
    }

    private function superuser(): User
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);
        $user->assignRole(Role::SUPERUSER);

        return $user;
    }

    private function superSuperuser(): User
    {
        $user = User::create([
            'name' => 'Pemilik Sistem',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);
        $user->assignRole(Role::SUPER_SUPERUSER);

        return $user;
    }
}
