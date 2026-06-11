<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_CATEGORIES = [
        ['name' => 'Zakat Fitrah', 'type' => 'zakat', 'usage_type' => 'khusus_mustahik', 'allow_operational_transfer' => false],
        ['name' => 'Zakat Maal', 'type' => 'zakat', 'usage_type' => 'khusus_mustahik', 'allow_operational_transfer' => false],
        ['name' => 'Zakat Profesi', 'type' => 'zakat', 'usage_type' => 'khusus_mustahik', 'allow_operational_transfer' => false],
        ['name' => 'Zakat Pertanian', 'type' => 'zakat', 'usage_type' => 'khusus_mustahik', 'allow_operational_transfer' => false],
        ['name' => 'Infak Jumat', 'type' => 'infak', 'usage_type' => 'bebas_operasional', 'allow_operational_transfer' => true],
        ['name' => 'Infak Subuh', 'type' => 'infak', 'usage_type' => 'bebas_operasional', 'allow_operational_transfer' => true],
        ['name' => 'Infak Kotak Amal', 'type' => 'infak', 'usage_type' => 'bebas_operasional', 'allow_operational_transfer' => true],
        ['name' => 'Infak Operasional Masjid', 'type' => 'infak', 'usage_type' => 'bebas_operasional', 'allow_operational_transfer' => true],
        ['name' => 'Infak Kajian', 'type' => 'infak', 'usage_type' => 'khusus_program', 'allow_operational_transfer' => false],
        ['name' => 'Infak Pembangunan', 'type' => 'infak', 'usage_type' => 'khusus_program', 'allow_operational_transfer' => false],
        ['name' => 'Infak Via QRIS', 'type' => 'infak', 'usage_type' => 'bebas_operasional', 'allow_operational_transfer' => true],
        ['name' => 'Sedekah Umum', 'type' => 'sedekah', 'usage_type' => 'bebas_operasional', 'allow_operational_transfer' => true],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('zis_categories') || ! Schema::hasTable('mosques')) {
            return;
        }

        $defaultNames = collect(self::DEFAULT_CATEGORIES)->pluck('name')->all();
        $now = now();

        DB::table('mosques')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($mosques) use ($defaultNames, $now) {
                foreach ($mosques as $mosque) {
                    foreach (self::DEFAULT_CATEGORIES as $category) {
                        $existingCategoryId = DB::table('zis_categories')
                            ->where('mosque_id', $mosque->id)
                            ->where('name', $category['name'])
                            ->value('id');

                        if ($existingCategoryId) {
                            DB::table('zis_categories')
                                ->where('id', $existingCategoryId)
                                ->update([
                                    'type' => $category['type'],
                                    'usage_type' => $category['usage_type'],
                                    'allow_operational_transfer' => $category['allow_operational_transfer'],
                                    'is_active' => true,
                                    'updated_at' => $now,
                                ]);

                            continue;
                        }

                        DB::table('zis_categories')->insert([
                            'mosque_id' => $mosque->id,
                            'name' => $category['name'],
                            'type' => $category['type'],
                            'usage_type' => $category['usage_type'],
                            'allow_operational_transfer' => $category['allow_operational_transfer'],
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    $legacyCategories = DB::table('zis_categories')
                        ->where('mosque_id', $mosque->id)
                        ->whereNotIn('name', $defaultNames)
                        ->get();

                    foreach ($legacyCategories as $legacyCategory) {
                        $receiptCount = DB::table('zis_receipts')
                            ->where('zis_category_id', $legacyCategory->id)
                            ->count();
                        $distributionCount = DB::table('zis_distributions')
                            ->where('zis_category_id', $legacyCategory->id)
                            ->count();

                        if ($receiptCount > 0 || $distributionCount > 0) {
                            DB::table('zis_categories')
                                ->where('id', $legacyCategory->id)
                                ->update([
                                    'is_active' => false,
                                    'updated_at' => $now,
                                ]);

                            continue;
                        }

                        DB::table('zis_categories')
                            ->where('id', $legacyCategory->id)
                            ->delete();
                    }
                }
            });
    }

    public function down(): void
    {
        // Audit data is intentionally preserved.
    }
};
