<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mosque_id')->nullable()->index();
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->string('kategori')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe_model')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->nullable();
            $table->string('kondisi')->default('baik');
            $table->string('lokasi')->nullable();
            $table->date('tanggal_perolehan')->nullable();
            $table->string('sumber_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 15, 2)->default(0);
            $table->string('penanggung_jawab')->nullable();
            $table->string('foto')->nullable();
            $table->string('status')->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
