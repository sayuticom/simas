<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->boolean('prompt_pakai_foto_narasumber')->default(false)->after('prompt_instruksi_foto');
            $table->string('prompt_target_audiens')->nullable()->after('prompt_pakai_foto_narasumber');
            $table->string('prompt_tingkat_keramaian')->nullable()->after('prompt_target_audiens');
            $table->string('prompt_fokus_utama')->nullable()->after('prompt_tingkat_keramaian');
            $table->json('prompt_elemen_desain')->nullable()->after('prompt_fokus_utama');
            $table->text('prompt_catatan_tambahan')->nullable()->after('prompt_elemen_desain');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn([
                'prompt_pakai_foto_narasumber',
                'prompt_target_audiens',
                'prompt_tingkat_keramaian',
                'prompt_fokus_utama',
                'prompt_elemen_desain',
                'prompt_catatan_tambahan',
            ]);
        });
    }
};
