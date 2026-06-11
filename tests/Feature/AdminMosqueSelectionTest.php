<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMosqueSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_one_managed_mosque_goes_directly_to_its_information(): void
    {
        $this->seed(RoleSeeder::class);

        $managedMosque = Mosque::create(['name' => 'Masjid Kelolaan Admin']);
        $otherRoleMosque = Mosque::create(['name' => 'Masjid Role Tambahan']);
        $admin = $this->createAdmin($managedMosque, $otherRoleMosque);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Informasi Masjid');
        $response->assertSeeText('Masjid Kelolaan Admin');
        $response->assertDontSeeText('Masjid Role Tambahan');
        $this->assertSame($managedMosque->id, $admin->fresh()->active_mosque_id);
    }

    public function test_admin_with_multiple_managed_mosques_sees_dashboard_table_before_choosing(): void
    {
        $this->seed(RoleSeeder::class);

        $firstMosque = Mosque::create(['name' => 'Masjid Kelolaan Pertama']);
        $secondMosque = Mosque::create(['name' => 'Masjid Kelolaan Kedua']);
        $admin = User::create([
            'name' => 'Admin Multi',
            'email' => 'admin-multi@example.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $firstMosque->id);
        $admin->assignRole(Role::ADMIN_MASJID, $secondMosque->id);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Daftar Masjid dan Pengelola');
        $response->assertSeeText('Masjid Kelolaan Pertama');
        $response->assertSeeText('Masjid Kelolaan Kedua');
        $response->assertDontSeeText('Informasi Masjid');
    }

    public function test_login_with_multiple_managed_mosques_resets_previous_selection_to_show_table(): void
    {
        $this->seed(RoleSeeder::class);

        $firstMosque = Mosque::create(['name' => 'Masjid Login Pertama']);
        $secondMosque = Mosque::create(['name' => 'Masjid Login Kedua']);
        $admin = User::create([
            'name' => 'Admin Login Multi',
            'email' => 'admin-login-multi@example.test',
            'password' => Hash::make('password'),
            'active_mosque_id' => $firstMosque->id,
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $firstMosque->id);
        $admin->assignRole(Role::ADMIN_MASJID, $secondMosque->id);

        $response = $this->post(route('login.process'), [
            'email' => 'admin-login-multi@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertNull($admin->fresh()->active_mosque_id);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Daftar Masjid dan Pengelola');
        $response->assertSeeText('Masjid Login Pertama');
        $response->assertSeeText('Masjid Login Kedua');
    }

    public function test_selecting_a_mosque_from_table_opens_its_information(): void
    {
        $this->seed(RoleSeeder::class);

        $firstMosque = Mosque::create(['name' => 'Masjid Untuk Dipilih']);
        $secondMosque = Mosque::create(['name' => 'Masjid Lainnya']);
        $admin = User::create([
            'name' => 'Admin Pemilih',
            'email' => 'admin-pemilih@example.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $firstMosque->id);
        $admin->assignRole(Role::ADMIN_MASJID, $secondMosque->id);

        $response = $this->actingAs($admin)->post(route('mosque.switch'), [
            'mosque_id' => $firstMosque->id,
        ]);

        $response->assertRedirect(route('dashboard'));

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Informasi Masjid');
        $response->assertSeeText('Masjid Untuk Dipilih');
        $response->assertSeeText('Tampilkan Daftar Masjid');
        $response->assertDontSeeText('Daftar Masjid dan Pengelola');
    }

    public function test_legacy_mosque_selection_page_redirects_to_dashboard_table(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Kelolaan']);
        $admin = User::create([
            'name' => 'Admin Pilihan',
            'email' => 'admin-pilihan@example.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $mosque->id);

        $response = $this->actingAs($admin)->get(route('mosque.select'));

        $response->assertRedirect(route('dashboard', ['choose_mosque' => 1]));
    }

    public function test_admin_masjid_cannot_switch_to_mosque_where_they_are_not_admin(): void
    {
        $this->seed(RoleSeeder::class);

        $managedMosque = Mosque::create(['name' => 'Masjid Kelolaan Admin']);
        $otherRoleMosque = Mosque::create(['name' => 'Masjid Role Tambahan']);
        $admin = $this->createAdmin($managedMosque, $otherRoleMosque);

        $response = $this->actingAs($admin)->post(route('mosque.switch'), [
            'mosque_id' => $otherRoleMosque->id,
        ]);

        $response->assertSessionHas('error', 'Gagal berganti masjid.');
        $this->assertNull($admin->fresh()->active_mosque_id);

        $response = $this->post(route('mosque.switch'), [
            'mosque_id' => $managedMosque->id,
        ]);

        $response->assertSessionHas('success', 'Berhasil berganti masjid.');
        $this->assertSame($managedMosque->id, $admin->fresh()->active_mosque_id);
    }

    private function createAdmin(Mosque $managedMosque, Mosque $otherRoleMosque): User
    {
        $admin = User::create([
            'name' => 'Admin Terbatas',
            'email' => 'admin-limited@example.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(Role::ADMIN_MASJID, $managedMosque->id);
        $admin->assignRole(Role::BENDAHARA, $otherRoleMosque->id);

        return $admin;
    }
}
