<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zis_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_categories', 'usage_type')) {
                $table->string('usage_type')->nullable()->after('type');
            }

            if (! Schema::hasColumn('zis_categories', 'allow_operational_transfer')) {
                $table->boolean('allow_operational_transfer')->default(false)->after('usage_type');
            }
        });

        Schema::table('zis_distributions', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_distributions', 'distribution_target')) {
                $table->string('distribution_target')->nullable()->after('recipient_type');
            }
        });

        $this->backfillCategoryUsageRules();

        if (Schema::hasColumn('zis_distributions', 'distribution_target')) {
            DB::table('zis_distributions')
                ->whereNull('distribution_target')
                ->update(['distribution_target' => 'penerima_manfaat']);
        }
    }

    public function down(): void
    {
        Schema::table('zis_distributions', function (Blueprint $table) {
            if (Schema::hasColumn('zis_distributions', 'distribution_target')) {
                $table->dropColumn('distribution_target');
            }
        });

        Schema::table('zis_categories', function (Blueprint $table) {
            foreach (['allow_operational_transfer', 'usage_type'] as $column) {
                if (Schema::hasColumn('zis_categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function backfillCategoryUsageRules(): void
    {
        DB::table('zis_categories')
            ->where('type', 'zakat')
            ->update([
                'usage_type' => 'khusus_mustahik',
                'allow_operational_transfer' => false,
            ]);

        DB::table('zis_categories')
            ->where('type', 'wakaf')
            ->update([
                'usage_type' => 'wakaf',
                'allow_operational_transfer' => false,
            ]);

        DB::table('zis_categories')
            ->whereIn('type', ['infak', 'sedekah'])
            ->where(function ($query) {
                $query->where('name', 'like', '%khusus%')
                    ->orWhere('name', 'like', '%yatim%')
                    ->orWhere('name', 'like', '%pembangunan%');
            })
            ->update([
                'usage_type' => 'khusus_program',
                'allow_operational_transfer' => false,
            ]);

        DB::table('zis_categories')
            ->whereIn('type', ['infak', 'sedekah'])
            ->whereNull('usage_type')
            ->update([
                'usage_type' => 'bebas_operasional',
                'allow_operational_transfer' => true,
            ]);

        DB::table('zis_categories')
            ->where('type', 'pendapatan_layanan')
            ->update([
                'usage_type' => 'bebas_operasional',
                'allow_operational_transfer' => true,
            ]);

        DB::table('zis_categories')
            ->where('type', 'bantuan')
            ->whereNull('usage_type')
            ->update([
                'usage_type' => 'terikat_perjanjian',
                'allow_operational_transfer' => false,
            ]);

        DB::table('zis_categories')
            ->whereNull('usage_type')
            ->update([
                'usage_type' => 'khusus_program',
                'allow_operational_transfer' => false,
            ]);
    }
};
