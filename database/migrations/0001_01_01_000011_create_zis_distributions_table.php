<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zis_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mustahik_id')->constrained('mustahiks')->cascadeOnDelete();
            $table->foreignId('zis_program_id')->constrained('zis_programs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('sumber_dana');
            $table->string('kategori_asnaf');
            $table->string('jenis_bantuan');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('nama_barang')->nullable();
            $table->integer('jumlah_barang')->nullable();
            $table->string('disalurkan_oleh')->nullable();
            $table->string('bukti_serah_terima')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zis_distributions');
    }
};
