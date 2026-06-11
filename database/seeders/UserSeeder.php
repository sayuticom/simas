<?php

namespace Database\Seeders;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Masjid Dummy
        $mosque1 = Mosque::firstOrCreate(
            ['name' => 'Masjid Nurul Iman'],
            ['address' => 'Jl. Merdeka No. 1']
        );

        $mosque2 = Mosque::firstOrCreate(
            ['name' => 'Masjid Al-Ikhlas'],
            ['address' => 'Jl. Keadilan No. 10']
        );

        // 2. Buat pemilik otoritas tertinggi sistem
        $superuser = User::updateOrCreate(
            ['email' => 'superuser@simas.local'],
            [
                'name' => 'Global Admin',
                'password' => Hash::make('password'),
                'active_mosque_id' => null,
            ]
        );
        // Sync role (kosongkan dulu agar tidak duplikat saat seeder dijalankan ulang)
        $superuser->roles()->detach();
        $superuser->assignRole(Role::SUPER_SUPERUSER, null);

        // 3. Buat Admin Masjid 1
        $admin1 = User::updateOrCreate(
            ['email' => 'admin.mosque1@simas.local'],
            [
                'name' => 'Admin Mosque 1',
                'password' => Hash::make('password'),
                'active_mosque_id' => $mosque1->id,
            ]
        );
        $admin1->roles()->detach();
        $admin1->assignRole(Role::ADMIN_MASJID, $mosque1->id);

        // 4. Buat Bendahara Masjid 1
        $bendahara1 = User::updateOrCreate(
            ['email' => 'bendahara.mosque1@simas.local'],
            [
                'name' => 'Bendahara Mosque 1',
                'password' => Hash::make('password'),
                'active_mosque_id' => $mosque1->id,
            ]
        );
        $bendahara1->roles()->detach();
        $bendahara1->assignRole(Role::BENDAHARA, $mosque1->id);

        // 5. Buat Admin Masjid 2
        $admin2 = User::updateOrCreate(
            ['email' => 'admin.mosque2@simas.local'],
            [
                'name' => 'Admin Mosque 2',
                'password' => Hash::make('password'),
                'active_mosque_id' => $mosque2->id,
            ]
        );
        $admin2->roles()->detach();
        $admin2->assignRole(Role::ADMIN_MASJID, $mosque2->id);

        $this->command->info('User demo berhasil dibuat!');
    }
}
