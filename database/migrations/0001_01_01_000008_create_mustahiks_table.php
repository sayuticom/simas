<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mustahiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('kategori_asnaf', ['Fakir', 'Miskin', 'Amil', 'Mualaf', 'Riqab', 'Gharimin', 'Fi Sabilillah', 'Ibnu Sabil']);
            $table->text('kondisi_ekonomi')->nullable();
            $table->integer('jumlah_tanggungan')->nullable();
            $table->boolean('status_verifikasi')->default(false);
            $table->text('catatan_survei')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mustahiks');
    }
};
