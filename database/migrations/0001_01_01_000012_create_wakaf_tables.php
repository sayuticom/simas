<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wakifs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis_wakif')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_identitas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('nazhirs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_identitas')->nullable();
            $table->string('jabatan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_programs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('target_dana', 15, 2)->default(0);
            $table->text('tujuan')->nullable();
            $table->string('status')->default('Berjalan');
            $table->timestamps();
        });

        Schema::create('wakaf_cashes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wakif_id')->constrained('wakifs')->cascadeOnDelete();
            $table->foreignId('nazhir_id')->constrained('nazhirs')->cascadeOnDelete();
            $table->foreignId('waqf_program_id')->constrained('wakaf_programs')->cascadeOnDelete();
            $table->date('tanggal_terima');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('tujuan_investasi')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_file')->nullable();
            $table->string('dokumen_ikrar')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_non_cashes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wakif_id')->constrained('wakifs')->cascadeOnDelete();
            $table->foreignId('nazhir_id')->constrained('nazhirs')->cascadeOnDelete();
            $table->date('tanggal_terima');
            $table->string('jenis_aset')->nullable();
            $table->string('nama_aset')->nullable();
            $table->decimal('nilai_estimasi', 15, 2)->default(0);
            $table->string('lokasi')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('luas')->nullable();
            $table->string('nomor_sertifikat')->nullable();
            $table->string('status_dokumen')->nullable();
            $table->string('dokumen_ikrar')->nullable();
            $table->string('dokumen_aset')->nullable();
            $table->string('foto')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_assets', function (Blueprint $table) {
            $table->id();
            $table->string('sumber_wakaf')->nullable();
            $table->foreignId('wakaf_tunai_id')->nullable()->constrained('wakaf_cashes')->nullOnDelete();
            $table->foreignId('wakaf_non_tunai_id')->nullable()->constrained('wakaf_non_cashes')->nullOnDelete();
            $table->foreignId('nazhir_id')->constrained('nazhirs')->cascadeOnDelete();
            $table->string('jenis_aset')->nullable();
            $table->string('nama_aset')->nullable();
            $table->string('lokasi')->nullable();
            $table->decimal('nilai_estimasi', 15, 2)->default(0);
            $table->string('kondisi')->nullable();
            $table->string('status_hukum')->nullable();
            $table->string('status_pemanfaatan')->nullable();
            $table->boolean('produktif')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_productive_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waqf_asset_id')->constrained('wakaf_assets')->cascadeOnDelete();
            $table->string('jenis_pengelolaan')->nullable();
            $table->string('nama_penyewa_atau_mitra')->nullable();
            $table->date('tanggal_mulai_kontrak')->nullable();
            $table->date('tanggal_selesai_kontrak')->nullable();
            $table->decimal('target_pendapatan', 15, 2)->default(0);
            $table->string('periode_pendapatan')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_management_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productive_waqf_asset_id')->constrained('wakaf_productive_assets')->cascadeOnDelete();
            $table->date('tanggal_penerimaan');
            $table->string('jenis_hasil')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('periode')->nullable();
            $table->string('nama_pembayar')->nullable();
            $table->string('bukti_file')->nullable();
            $table->enum('masuk_ke_kas_masjid', ['Ya', 'Tidak'])->default('Tidak');
            $table->foreignId('mosque_cash_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waqf_asset_id')->constrained('wakaf_assets')->cascadeOnDelete();
            $table->date('tanggal_pengeluaran');
            $table->string('jenis_biaya')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('dibayar_dari')->nullable();
            $table->string('bukti_file')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waqf_asset_id')->constrained('wakaf_assets')->cascadeOnDelete();
            $table->string('jenis_dokumen')->nullable();
            $table->string('nomor_dokumen')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        DB::table('transaction_categories')->insertOrIgnore([
            ['name' => 'Hasil Kelola Wakaf', 'type' => 'masuk', 'description' => 'Pencatatan penerimaan hasil kelola aset wakaf', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('wakaf_documents');
        Schema::dropIfExists('wakaf_asset_maintenances');
        Schema::dropIfExists('wakaf_management_results');
        Schema::dropIfExists('wakaf_productive_assets');
        Schema::dropIfExists('wakaf_assets');
        Schema::dropIfExists('wakaf_non_cashes');
        Schema::dropIfExists('wakaf_cashes');
        Schema::dropIfExists('wakaf_programs');
        Schema::dropIfExists('nazhirs');
        Schema::dropIfExists('wakifs');
    }
};
