<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_receipts', 'public_receipt_token')) {
                $table->string('public_receipt_token', 80)->nullable()->unique()->after('proof_file');
            }
        });

        if (! Schema::hasColumn('zis_receipts', 'public_receipt_token')) {
            return;
        }

        DB::table('zis_receipts')
            ->whereNull('public_receipt_token')
            ->orderBy('id')
            ->chunkById(100, function ($receipts) {
                foreach ($receipts as $receipt) {
                    DB::table('zis_receipts')
                        ->where('id', $receipt->id)
                        ->update([
                            'public_receipt_token' => $this->uniqueToken(),
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('zis_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('zis_receipts', 'public_receipt_token')) {
                $table->dropUnique(['public_receipt_token']);
                $table->dropColumn('public_receipt_token');
            }
        });
    }

    private function uniqueToken(): string
    {
        do {
            $token = Str::random(40);
        } while (DB::table('zis_receipts')->where('public_receipt_token', $token)->exists());

        return $token;
    }
};
