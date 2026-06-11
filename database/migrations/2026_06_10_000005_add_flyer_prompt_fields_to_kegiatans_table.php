<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->string('prompt_nuansa_desain')->nullable()->after('poster_publik');
            $table->string('prompt_warna_utama')->nullable()->after('prompt_nuansa_desain');
            $table->string('prompt_gaya_desain')->nullable()->after('prompt_warna_utama');
            $table->text('prompt_catatan_khusus')->nullable()->after('prompt_gaya_desain');
            $table->text('prompt_instruksi_foto')->nullable()->after('prompt_catatan_khusus');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn([
                'prompt_nuansa_desain',
                'prompt_warna_utama',
                'prompt_gaya_desain',
                'prompt_catatan_khusus',
                'prompt_instruksi_foto',
            ]);
        });
    }
};
