<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muzakkis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_kepala_keluarga')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->integer('jumlah_anggota_keluarga')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzakkis');
    }
};
