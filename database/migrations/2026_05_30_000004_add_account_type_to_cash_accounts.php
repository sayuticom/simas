<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('cash_accounts', 'account_type')) {
                $table->string('account_type')->nullable()->after('type');
            }
        });

        DB::table('cash_accounts')
            ->whereNull('account_type')
            ->orWhere('account_type', '')
            ->orderBy('id')
            ->chunkById(100, function ($accounts) {
                foreach ($accounts as $account) {
                    DB::table('cash_accounts')
                        ->where('id', $account->id)
                        ->update(['account_type' => $this->accountTypeFor($account->type)]);
                }
            });

        DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(50) NULL");
    }

    public function down(): void
    {
        Schema::table('cash_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('cash_accounts', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });
    }

    private function accountTypeFor(?string $type): string
    {
        return match ($type) {
            'tunai' => 'cash',
            'bank' => 'bank',
            'qris' => 'qris',
            'ewallet' => 'ewallet',
            default => 'other',
        };
    }
};
