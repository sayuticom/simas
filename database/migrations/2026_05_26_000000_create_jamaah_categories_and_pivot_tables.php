<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CATEGORIES = [
        'jamaah_tetap' => 'Jamaah Tetap',
        'jamaah_aktif' => 'Jamaah Aktif',
        'pengurus' => 'Pengurus',
        'donatur' => 'Donatur',
        'muzakki' => 'Muzakki',
        'mustahik' => 'Mustahik',
        'remaja_masjid' => 'Remaja Masjid',
    ];

    public function up(): void
    {
        Schema::create('jamaah_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('jamaah_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jamaah_id')->constrained('jamaahs')->cascadeOnDelete();
            $table->foreignId('jamaah_category_id')->constrained('jamaah_categories')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['jamaah_id', 'jamaah_category_id']);
        });

        $now = now();
        foreach (self::CATEGORIES as $name => $label) {
            DB::table('jamaah_categories')->insert([
                'name' => $name,
                'label' => $label,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $categoryIds = DB::table('jamaah_categories')->pluck('id', 'name');

        DB::table('jamaahs')
            ->whereNotNull('kategori')
            ->orderBy('id')
            ->chunkById(100, function ($jamaahs) use ($categoryIds, $now): void {
                $pivotRows = [];

                foreach ($jamaahs as $jamaah) {
                    $categoryId = $categoryIds[$jamaah->kategori] ?? null;

                    if ($categoryId) {
                        $pivotRows[] = [
                            'jamaah_id' => $jamaah->id,
                            'jamaah_category_id' => $categoryId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($pivotRows) {
                    DB::table('jamaah_category')->insertOrIgnore($pivotRows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_category');
        Schema::dropIfExists('jamaah_categories');
    }
};
