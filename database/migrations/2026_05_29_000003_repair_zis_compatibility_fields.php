<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_receipts', 'proof_file')) {
                $table->string('proof_file')->nullable()->after('description');
            }
        });

        if (Schema::hasColumn('zis_receipts', 'bukti_file')) {
            DB::table('zis_receipts')
                ->whereNull('proof_file')
                ->whereNotNull('bukti_file')
                ->update(['proof_file' => DB::raw('bukti_file')]);
        }

        $this->backfillReceiptCategories();
        $this->backfillDistributionCategories();
    }

    public function down(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('zis_receipts', 'proof_file')) {
                $table->dropColumn('proof_file');
            }
        });
    }

    private function backfillReceiptCategories(): void
    {
        if (! Schema::hasTable('zis_categories')) {
            return;
        }

        DB::table('zis_receipts')
            ->whereNull('zis_category_id')
            ->whereNotNull('mosque_id')
            ->orderBy('id')
            ->chunkById(100, function ($receipts) {
                foreach ($receipts as $receipt) {
                    $name = $receipt->jenis_zakat
                        ?: $receipt->jenis_penerimaan
                        ?: 'Penerimaan ZIS Lama';

                    $categoryId = $this->findOrCreateCategory(
                        (int) $receipt->mosque_id,
                        $this->normalizeName($name),
                        $this->guessType($name)
                    );

                    DB::table('zis_receipts')
                        ->where('id', $receipt->id)
                        ->update(['zis_category_id' => $categoryId]);
                }
            });
    }

    private function backfillDistributionCategories(): void
    {
        if (! Schema::hasTable('zis_categories')) {
            return;
        }

        DB::table('zis_distributions')
            ->whereNull('zis_category_id')
            ->whereNotNull('mosque_id')
            ->orderBy('id')
            ->chunkById(100, function ($distributions) {
                foreach ($distributions as $distribution) {
                    $name = $distribution->sumber_dana ?: 'Penyaluran ZIS Lama';

                    $categoryId = $this->findOrCreateCategory(
                        (int) $distribution->mosque_id,
                        $this->normalizeName($name),
                        $this->guessType($name)
                    );

                    DB::table('zis_distributions')
                        ->where('id', $distribution->id)
                        ->update(['zis_category_id' => $categoryId]);
                }
            });
    }

    private function findOrCreateCategory(int $mosqueId, string $name, string $type): int
    {
        $categoryId = DB::table('zis_categories')
            ->where('mosque_id', $mosqueId)
            ->where('name', $name)
            ->value('id');

        if ($categoryId) {
            return (int) $categoryId;
        }

        return (int) DB::table('zis_categories')->insertGetId([
            'mosque_id' => $mosqueId,
            'name' => $name,
            'type' => $type,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function normalizeName(?string $name): string
    {
        $name = trim((string) $name);

        return $name !== '' ? $name : 'ZIS Lama';
    }

    private function guessType(?string $name): string
    {
        $normalized = strtolower((string) $name);

        if (str_contains($normalized, 'zakat')) {
            return 'zakat';
        }

        if (str_contains($normalized, 'sedekah')) {
            return 'sedekah';
        }

        return 'infak';
    }
};
