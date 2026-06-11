<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zis_distributions', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_distributions', 'zis_receipt_id')) {
                $table->foreignId('zis_receipt_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('zis_receipts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('zis_distributions', function (Blueprint $table) {
            if (Schema::hasColumn('zis_distributions', 'zis_receipt_id')) {
                $table->dropConstrainedForeignId('zis_receipt_id');
            }
        });
    }
};
