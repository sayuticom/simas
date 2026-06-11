<?php

namespace Tests\Feature;

use App\Models\Mosque;
use App\Models\MosqueProfile;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperuserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_sees_dkm_names_for_each_mosque_from_profile_and_role_fallback(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.test',
            'password' => Hash::make('password'),
        ]);
        $superuser->assignRole(Role::SUPERUSER);

        $mosque = Mosque::create(['name' => 'Masjid Al-Hidayah']);
        MosqueProfile::create([
            'mosque_id' => $mosque->id,
            'nama_ketua_dkm' => 'Ahmad Ketua',
            'nama_bendahara' => 'Siti Bendahara',
        ]);

        $secretary = User::create([
            'name' => 'Fajar Sekretaris',
            'email' => 'secretary@example.test',
            'password' => Hash::make('password'),
        ]);
        $secretary->assignRole(Role::SEKRETARIS, $mosque->id);

        $response = $this->actingAs($superuser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Daftar Masjid dan Pengelola');
        $response->assertSeeText('Masjid Al-Hidayah');
        $response->assertSeeText('Ahmad Ketua');
        $response->assertSeeText('Siti Bendahara');
        $response->assertSeeText('Fajar Sekretaris');
    }

    public function test_superuser_sees_selected_mosque_information_after_switching_mosque(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = User::create([
            'name' => 'Super Admin',
            'email' => 'super-selected@example.test',
            'password' => Hash::make('password'),
        ]);
        $superuser->assignRole(Role::SUPERUSER);

        $mosque = Mosque::create([
            'name' => 'Masjid Pilihan',
            'address' => 'Jl. Utama No. 1',
            'phone' => '021123456',
        ]);

        $response = $this->actingAs($superuser)->post(route('mosque.switch'), [
            'mosque_id' => $mosque->id,
        ]);

        $response->assertRedirect();

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Informasi Masjid');
        $response->assertSeeText('Masjid Pilihan');
        $response->assertSeeText('Jl. Utama No. 1');
        $response->assertSeeText('021123456');
        $response->assertSeeText('Tampilkan Semua Masjid');
        $response->assertDontSeeText('Daftar Masjid dan Pengelola');
    }

    public function test_superuser_can_return_to_all_mosques_list_from_selected_mosque(): void
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Aktif']);
        $superuser = User::create([
            'name' => 'Super Admin',
            'email' => 'super-all-list@example.test',
            'password' => Hash::make('password'),
            'active_mosque_id' => $mosque->id,
        ]);
        $superuser->assignRole(Role::SUPERUSER);

        $response = $this->actingAs($superuser)->post(route('mosque.all'));

        $response->assertRedirect(route('dashboard'));
        $this->assertNull($superuser->fresh()->active_mosque_id);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Daftar Masjid dan Pengelola');
        $response->assertSeeText('Masjid Aktif');
    }

    public function test_superuser_can_select_another_mosque_from_all_mosques(): void
    {
        $this->seed(RoleSeeder::class);

        $superuser = User::create([
            'name' => 'Super Admin',
            'email' => 'super-select-list@example.test',
            'password' => Hash::make('password'),
        ]);
        $superuser->assignRole(Role::SUPERUSER);

        Mosque::create(['name' => 'Masjid Yang Tersedia']);

        $response = $this->actingAs($superuser)->get(route('mosque.select'));

        $response->assertRedirect(route('dashboard', ['choose_mosque' => 1]));

        $response = $this->get(route('dashboard', ['choose_mosque' => 1]));

        $response->assertOk();
        $response->assertSeeText('Masjid Yang Tersedia');
    }
}
