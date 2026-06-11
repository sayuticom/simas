<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zis_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_penerimaan');
            $table->foreignId('muzakki_id')->constrained('muzakkis')->cascadeOnDelete();
            $table->foreignId('zis_program_id')->constrained('zis_programs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('metode_pembayaran');
            $table->string('jenis_zakat')->nullable();
            $table->string('jenis_fitrah')->nullable();
            $table->integer('jumlah_jiwa')->nullable();
            $table->decimal('jumlah_beras', 12, 2)->nullable();
            $table->decimal('nominal_uang', 15, 2)->default(0);
            $table->string('bukti_file')->nullable();
            $table->string('diterima_oleh')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zis_receipts');
    }
};
