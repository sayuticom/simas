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
            if (! Schema::hasColumn('cash_accounts', 'can_receive_zis')) {
                $table->boolean('can_receive_zis')->default(true)->after('is_active');
            }

            if (! Schema::hasColumn('cash_accounts', 'can_distribute_zis')) {
                $table->boolean('can_distribute_zis')->default(true)->after('can_receive_zis');
            }

            if (! Schema::hasColumn('cash_accounts', 'can_operational')) {
                $table->boolean('can_operational')->default(true)->after('can_distribute_zis');
            }
        });

        DB::table('cash_accounts')
            ->orderBy('id')
            ->chunkById(100, function ($accounts) {
                foreach ($accounts as $account) {
                    $allowsOutgoing = in_array($account->account_type, ['cash', 'bank'], true);

                    DB::table('cash_accounts')
                        ->where('id', $account->id)
                        ->update([
                            'can_receive_zis' => true,
                            'can_distribute_zis' => $allowsOutgoing,
                            'can_operational' => $allowsOutgoing,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('cash_accounts', function (Blueprint $table) {
            foreach (['can_operational', 'can_distribute_zis', 'can_receive_zis'] as $column) {
                if (Schema::hasColumn('cash_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
