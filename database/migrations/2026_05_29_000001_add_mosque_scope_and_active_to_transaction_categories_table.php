<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_CATEGORIES = [
        ['name' => "Infak Jum'at", 'type' => 'masuk'],
        ['name' => 'Kotak Amal', 'type' => 'masuk'],
        ['name' => 'Donasi Jamaah', 'type' => 'masuk'],
        ['name' => 'Wakaf Tunai', 'type' => 'masuk'],
        ['name' => 'Zakat', 'type' => 'masuk'],
        ['name' => 'Sedekah', 'type' => 'masuk'],
        ['name' => 'Sewa Aula', 'type' => 'masuk'],
        ['name' => 'Bantuan Pemerintah', 'type' => 'masuk'],
        ['name' => 'Operasional Masjid', 'type' => 'keluar'],
        ['name' => 'Listrik & Air', 'type' => 'keluar'],
        ['name' => 'Kebersihan', 'type' => 'keluar'],
        ['name' => 'Konsumsi', 'type' => 'keluar'],
        ['name' => 'Honor Petugas', 'type' => 'keluar'],
        ['name' => 'Perbaikan Bangunan', 'type' => 'keluar'],
        ['name' => 'Dakwah & Kajian', 'type' => 'keluar'],
        ['name' => 'Sosial Jamaah', 'type' => 'keluar'],
    ];

    public function up(): void
    {
        Schema::table('transaction_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction_categories', 'mosque_id')) {
                $table->foreignId('mosque_id')->nullable()->after('id')->constrained('mosques')->nullOnDelete();
            }

            if (! Schema::hasColumn('transaction_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('type');
            }
        });

        $now = now();
        $mosques = DB::table('mosques')->select('id')->get();

        foreach ($mosques as $mosque) {
            foreach (self::DEFAULT_CATEGORIES as $category) {
                DB::table('transaction_categories')->updateOrInsert(
                    [
                        'mosque_id' => $mosque->id,
                        'name' => $category['name'],
                    ],
                    [
                        'type' => $category['type'],
                        'is_active' => true,
                        'description' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        DB::table('transactions')
            ->join('transaction_categories', 'transactions.transaction_category_id', '=', 'transaction_categories.id')
            ->whereNull('transactions.type')
            ->update(['transactions.type' => DB::raw('transaction_categories.type')]);
    }

    public function down(): void
    {
        Schema::table('transaction_categories', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_categories', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('transaction_categories', 'mosque_id')) {
                $table->dropConstrainedForeignId('mosque_id');
            }
        });
    }
};
