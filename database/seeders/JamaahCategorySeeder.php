<?php

namespace Database\Seeders;

use App\Models\JamaahCategory;
use Illuminate\Database\Seeder;

class JamaahCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'jamaah_tetap' => 'Jamaah Tetap',
            'jamaah_aktif' => 'Jamaah Aktif',
            'pengurus' => 'Pengurus',
            'donatur' => 'Donatur',
            'muzakki' => 'Muzakki',
            'mustahik' => 'Mustahik',
            'remaja_masjid' => 'Remaja Masjid',
            'jamaah_tamu' => 'Jamaah Tamu',
        ];

        foreach ($categories as $name => $label) {
            JamaahCategory::updateOrCreate(
                ['name' => $name],
                ['label' => $label]
            );
        }
    }
}
