<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('email_publik');
            $table->string('tiktok_url')->nullable()->after('instagram_url');
            $table->string('facebook_url')->nullable()->after('tiktok_url');
            $table->string('youtube_url')->nullable()->after('facebook_url');
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_url',
                'tiktok_url',
                'facebook_url',
                'youtube_url',
            ]);
        });
    }
};
