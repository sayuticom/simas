<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['masuk', 'keluar']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('transaction_categories')->insert([
            ['name' => 'Infak Jumat umum', 'type' => 'masuk', 'description' => 'Penerimaan infak umum pada hari Jumat', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kotak amal umum', 'type' => 'masuk', 'description' => 'Dana dari kotak amal umum', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Donasi operasional', 'type' => 'masuk', 'description' => 'Donasi untuk kegiatan operasional masjid', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biaya listrik', 'type' => 'keluar', 'description' => 'Pembayaran tagihan listrik', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biaya kebersihan', 'type' => 'keluar', 'description' => 'Pengeluaran untuk kebersihan masjid', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Konsumsi kegiatan', 'type' => 'keluar', 'description' => 'Biaya konsumsi untuk kegiatan masjid', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operasional masjid', 'type' => 'keluar', 'description' => 'Pengeluaran operasional rutin masjid', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Renovasi ringan', 'type' => 'keluar', 'description' => 'Biaya renovasi ringan dan perawatan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_categories');
    }
};
