<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->boolean('show_public_pengumuman')->default(true)->after('status_website');
            $table->boolean('show_public_informasi')->default(true)->after('show_public_pengumuman');
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_public_pengumuman',
                'show_public_informasi',
            ]);
        });
    }
};
