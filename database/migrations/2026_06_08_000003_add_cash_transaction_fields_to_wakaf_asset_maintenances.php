<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wakaf_asset_maintenances', function (Blueprint $table) {
            if (! Schema::hasColumn('wakaf_asset_maintenances', 'cash_account_id')) {
                $table->unsignedBigInteger('cash_account_id')
                    ->nullable()
                    ->after('dibayar_dari');

                $table->foreign('cash_account_id')
                    ->references('id')
                    ->on('cash_accounts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('wakaf_asset_maintenances', 'mosque_cash_transaction_id')) {
                $table->unsignedBigInteger('mosque_cash_transaction_id')
                    ->nullable()
                    ->after('cash_account_id');

                $table->foreign('mosque_cash_transaction_id')
                    ->references('id')
                    ->on('transactions')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('wakaf_asset_maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('wakaf_asset_maintenances', 'mosque_cash_transaction_id')) {
                $table->dropForeign(['mosque_cash_transaction_id']);
                $table->dropColumn('mosque_cash_transaction_id');
            }

            if (Schema::hasColumn('wakaf_asset_maintenances', 'cash_account_id')) {
                $table->dropForeign(['cash_account_id']);
                $table->dropColumn('cash_account_id');
            }
        });
    }
};
