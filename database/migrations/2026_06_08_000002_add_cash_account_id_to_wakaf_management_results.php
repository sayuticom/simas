<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wakaf_management_results', function (Blueprint $table) {
            if (! Schema::hasColumn('wakaf_management_results', 'cash_account_id')) {
                $table->unsignedBigInteger('cash_account_id')
                    ->nullable()
                    ->after('masuk_ke_kas_masjid');

                $table->foreign('cash_account_id')
                    ->references('id')
                    ->on('cash_accounts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('wakaf_management_results', function (Blueprint $table) {
            if (Schema::hasColumn('wakaf_management_results', 'cash_account_id')) {
                $table->dropForeign(['cash_account_id']);
                $table->dropColumn('cash_account_id');
            }
        });
    }
};
