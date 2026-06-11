<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'source_type')) {
                $table->string('source_type')->nullable()->after('created_by');
            }

            if (! Schema::hasColumn('transactions', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }

            if (! Schema::hasIndex('transactions', 'transactions_source_type_source_id_index')) {
                $table->index(['source_type', 'source_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasIndex('transactions', 'transactions_source_type_source_id_index')) {
                $table->dropIndex('transactions_source_type_source_id_index');
            }

            foreach (['source_id', 'source_type'] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
