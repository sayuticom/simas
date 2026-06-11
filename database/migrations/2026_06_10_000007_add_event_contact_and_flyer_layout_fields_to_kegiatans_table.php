<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->string('kontak_person')->nullable()->after('target_peserta');
            $table->string('nomor_kontak')->nullable()->after('kontak_person');
            $table->string('label_kontak')->nullable()->after('nomor_kontak');
            $table->string('prompt_posisi_foto_pemateri')->nullable()->after('prompt_pakai_foto_narasumber');
            $table->string('prompt_tujuan_flyer')->nullable()->after('prompt_posisi_foto_pemateri');
            $table->string('prompt_model_layout')->nullable()->after('prompt_tujuan_flyer');
            $table->string('prompt_kepadatan_teks')->nullable()->after('prompt_model_layout');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn([
                'kontak_person',
                'nomor_kontak',
                'label_kontak',
                'prompt_posisi_foto_pemateri',
                'prompt_tujuan_flyer',
                'prompt_model_layout',
                'prompt_kepadatan_teks',
            ]);
        });
    }
};
