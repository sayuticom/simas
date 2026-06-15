<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_receipts', 'recap_status')) {
                $table->string('recap_status')->default('belum_direkap')->after('receipt_issued_by');
            }

            if (! Schema::hasColumn('zis_receipts', 'recapped_at')) {
                $table->timestamp('recapped_at')->nullable()->after('recap_status');
            }

            if (! Schema::hasColumn('zis_receipts', 'recapped_by')) {
                $table->foreignId('recapped_by')->nullable()->after('recapped_at')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('zis_receipts', 'recap_note')) {
                $table->text('recap_note')->nullable()->after('recapped_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('zis_receipts', 'recapped_by')) {
                $table->dropConstrainedForeignId('recapped_by');
            }

            foreach (['recap_note', 'recapped_at', 'recap_status'] as $column) {
                if (Schema::hasColumn('zis_receipts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

