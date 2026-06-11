<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_ACCOUNTS = [
        ['name' => 'Kas Tunai', 'type' => 'tunai'],
        ['name' => 'Rekening Bank', 'type' => 'bank'],
        ['name' => 'QRIS', 'type' => 'qris'],
    ];

    public function up(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['mosque_id', 'name']);
        });

        Schema::table('zis_receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_receipts', 'cash_account_id')) {
                $table->foreignId('cash_account_id')
                    ->nullable()
                    ->after('zis_category_id')
                    ->constrained('cash_accounts')
                    ->nullOnDelete();
            }
        });

        Schema::table('zis_distributions', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_distributions', 'cash_account_id')) {
                $table->foreignId('cash_account_id')
                    ->nullable()
                    ->after('zis_category_id')
                    ->constrained('cash_accounts')
                    ->nullOnDelete();
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'cash_account_id')) {
                $table->foreignId('cash_account_id')
                    ->nullable()
                    ->after('transaction_category_id')
                    ->constrained('cash_accounts')
                    ->nullOnDelete();
            }
        });

        $this->seedDefaultsAndBackfill();
    }

    public function down(): void
    {
        foreach (['transactions', 'zis_distributions', 'zis_receipts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'cash_account_id')) {
                    $table->dropConstrainedForeignId('cash_account_id');
                }
            });
        }

        Schema::dropIfExists('cash_accounts');
    }

    private function seedDefaultsAndBackfill(): void
    {
        $now = now();

        DB::table('mosques')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($mosques) use ($now) {
                foreach ($mosques as $mosque) {
                    foreach (self::DEFAULT_ACCOUNTS as $account) {
                        DB::table('cash_accounts')->updateOrInsert(
                            [
                                'mosque_id' => $mosque->id,
                                'name' => $account['name'],
                            ],
                            [
                                'type' => $account['type'],
                                'is_active' => true,
                                'updated_at' => $now,
                                'created_at' => $now,
                            ]
                        );
                    }

                    $cashAccountId = DB::table('cash_accounts')
                        ->where('mosque_id', $mosque->id)
                        ->where('type', 'tunai')
                        ->orderBy('id')
                        ->value('id');

                    if (! $cashAccountId) {
                        continue;
                    }

                    DB::table('zis_receipts')
                        ->where('mosque_id', $mosque->id)
                        ->whereNull('cash_account_id')
                        ->update(['cash_account_id' => $cashAccountId]);

                    DB::table('zis_distributions')
                        ->where('mosque_id', $mosque->id)
                        ->whereNull('cash_account_id')
                        ->update(['cash_account_id' => $cashAccountId]);

                    DB::table('transactions')
                        ->where('mosque_id', $mosque->id)
                        ->whereNull('cash_account_id')
                        ->update(['cash_account_id' => $cashAccountId]);
                }
            });
    }
};
