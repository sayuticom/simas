<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->string('kategori')->default('jamaah_aktif')->after('no_hp');
            $table->string('status')->default('pending')->change();
        });

        $legacyCategories = [
            'Jamaah Tetap' => 'jamaah_tetap',
            'Pengurus' => 'pengurus',
            'Relawan' => 'jamaah_aktif',
            'Jamaah Umum' => 'jamaah_aktif',
        ];

        foreach ($legacyCategories as $legacyStatus => $category) {
            DB::table('jamaahs')
                ->where('status', $legacyStatus)
                ->update([
                    'kategori' => $category,
                    'status' => 'verified',
                ]);
        }
    }

    public function down(): void
    {
        $legacyStatuses = [
            'jamaah_tetap' => 'Jamaah Tetap',
            'pengurus' => 'Pengurus',
            'jamaah_aktif' => 'Jamaah Umum',
            'donatur' => 'Jamaah Umum',
            'muzakki' => 'Jamaah Umum',
            'mustahik' => 'Jamaah Umum',
            'remaja_masjid' => 'Jamaah Umum',
        ];

        foreach ($legacyStatuses as $category => $legacyStatus) {
            DB::table('jamaahs')
                ->where('kategori', $category)
                ->update(['status' => $legacyStatus]);
        }

        DB::table('jamaahs')
            ->whereNotIn('status', ['Jamaah Tetap', 'Jamaah Umum', 'Relawan', 'Pengurus'])
            ->update(['status' => 'Jamaah Umum']);

        Schema::table('jamaahs', function (Blueprint $table) {
            $table->enum('status', ['Jamaah Tetap', 'Jamaah Umum', 'Relawan', 'Pengurus'])
                ->default('Jamaah Umum')
                ->change();
            $table->dropColumn('kategori');
        });
    }
};
