<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMosqueIdToMainTables extends Migration
{
    public function up()
    {
        $tables = [
            'jamaahs', 'transactions', 'muzakkis', 'mustahiks', 'zis_programs', 'zis_receipts', 'zis_distributions',
            'wakifs', 'nazhirs', 'waqf_programs', 'cash_waqf_receipts', 'non_cash_waqf_receipts', 'waqf_assets',
            'activities', 'schedules', 'inventories', 'documents', 'announcements',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableB) use ($table) {
                    if (! Schema::hasColumn($table, 'mosque_id')) {
                        $tableB->foreignId('mosque_id')->nullable()->constrained('mosques')->nullOnDelete();
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'jamaahs', 'transactions', 'muzakkis', 'mustahiks', 'zis_programs', 'zis_receipts', 'zis_distributions',
            'wakifs', 'nazhirs', 'waqf_programs', 'cash_waqf_receipts', 'non_cash_waqf_receipts', 'waqf_assets',
            'activities', 'schedules', 'inventories', 'documents', 'announcements',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableB) use ($table) {
                    if (Schema::hasColumn($table, 'mosque_id')) {
                        $tableB->dropConstrainedForeignId('mosque_id');
                    }
                });
            }
        }
    }
}
