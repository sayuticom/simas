<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::SUPER_SUPERUSER,
                'label' => 'Super Superuser',
                'description' => 'Otoritas tertinggi untuk membuat dan mengelola akun superuser',
            ],
            [
                'name' => Role::SUPERUSER,
                'label' => 'Superuser',
                'description' => 'Mengelola seluruh sistem dan semua masjid',
            ],
            [
                'name' => Role::ADMIN_MASJID,
                'label' => 'Admin Masjid',
                'description' => 'Mengelola satu masjid',
            ],
            [
                'name' => Role::KETUA_DKM,
                'label' => 'Ketua DKM',
                'description' => 'Melihat dan mengawasi data masjid',
            ],
            [
                'name' => Role::BENDAHARA,
                'label' => 'Bendahara',
                'description' => 'Mengelola kas, ZIS, wakaf, dan laporan keuangan',
            ],
            [
                'name' => Role::SEKRETARIS,
                'label' => 'Sekretaris',
                'description' => 'Membantu administrasi dan dokumentasi',
            ],
            [
                'name' => Role::OPERATOR,
                'label' => 'Operator',
                'description' => 'Input data jamaah, kegiatan, inventaris, dan administrasi',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['label' => $role['label'], 'description' => $role['description']]
            );
        }
    }
}
