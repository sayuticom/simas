<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'wakifs',
            'nazhirs',
            'wakaf_programs',
            'wakaf_cashes',
            'wakaf_non_cashes',
            'wakaf_assets',
            'wakaf_productive_assets',
            'wakaf_management_results',
            'wakaf_asset_maintenances',
            'wakaf_documents',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'mosque_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('mosque_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('mosques')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'wakifs',
            'nazhirs',
            'wakaf_programs',
            'wakaf_cashes',
            'wakaf_non_cashes',
            'wakaf_assets',
            'wakaf_productive_assets',
            'wakaf_management_results',
            'wakaf_asset_maintenances',
            'wakaf_documents',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'mosque_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('mosque_id');
            });
        }
    }
};
