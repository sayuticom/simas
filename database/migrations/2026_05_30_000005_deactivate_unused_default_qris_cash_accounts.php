<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cash_accounts')
            ->where('name', 'QRIS')
            ->where('account_type', 'qris')
            ->whereNotExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('zis_receipts')
                ->whereColumn('zis_receipts.cash_account_id', 'cash_accounts.id'))
            ->whereNotExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('zis_distributions')
                ->whereColumn('zis_distributions.cash_account_id', 'cash_accounts.id'))
            ->whereNotExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('transactions')
                ->whereColumn('transactions.cash_account_id', 'cash_accounts.id'))
            ->whereNotExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('cash_account_transfers')
                ->whereColumn('cash_account_transfers.from_cash_account_id', 'cash_accounts.id'))
            ->whereNotExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('cash_account_transfers')
                ->whereColumn('cash_account_transfers.to_cash_account_id', 'cash_accounts.id'))
            ->update(['is_active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('cash_accounts')
            ->where('name', 'QRIS')
            ->where('account_type', 'qris')
            ->update(['is_active' => true, 'updated_at' => now()]);
    }
};
