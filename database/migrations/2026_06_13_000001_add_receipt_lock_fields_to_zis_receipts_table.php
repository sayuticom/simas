<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('zis_receipts', 'receipt_status')) {
            Schema::table('zis_receipts', function (Blueprint $table) {
                $table->string('receipt_status')->default('belum_diterbitkan');
                $table->timestamp('receipt_issued_at')->nullable();
                $table->foreignId('receipt_issued_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        // Backfill: if receipt already has a public receipt token, consider it issued.
        DB::table('zis_receipts')
            ->whereNotNull('public_receipt_token')
            ->update([
                'receipt_status' => 'sudah_diterbitkan',
                // Use created_at as fallback (if issued_at is null)
                'receipt_issued_at' => DB::raw('COALESCE(receipt_issued_at, created_at)'),
                // Keep null when unknown (unless already set)
                'receipt_issued_by' => DB::raw('receipt_issued_by'),
            ]);

        // Ensure rows with no token remain default status
        DB::table('zis_receipts')
            ->whereNull('public_receipt_token')
            ->update([
                'receipt_status' => 'belum_diterbitkan',
            ]);
    }

    public function down(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('zis_receipts', 'receipt_status')) {
                $table->dropColumn(['receipt_status', 'receipt_issued_at', 'receipt_issued_by']);
            }
        });
    }
};

