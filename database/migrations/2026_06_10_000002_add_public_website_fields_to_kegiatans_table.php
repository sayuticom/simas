<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->boolean('tampilkan_di_website')->default(false)->after('catatan');
            $table->string('status_publik')->default('draft')->after('tampilkan_di_website');
            $table->string('judul_publik')->nullable()->after('status_publik');
            $table->text('deskripsi_publik')->nullable()->after('judul_publik');
            $table->string('poster_publik')->nullable()->after('deskripsi_publik');

            $table->index('tampilkan_di_website');
            $table->index('status_publik');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropIndex(['tampilkan_di_website']);
            $table->dropIndex(['status_publik']);
            $table->dropColumn([
                'tampilkan_di_website',
                'status_publik',
                'judul_publik',
                'deskripsi_publik',
                'poster_publik',
            ]);
        });
    }
};
