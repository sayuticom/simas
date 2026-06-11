<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->cascadeOnDelete();
            $table->string('subdomain')->unique();
            $table->string('nama_website')->nullable();
            $table->string('slogan')->nullable();
            $table->text('deskripsi_singkat')->nullable();
            $table->text('alamat_publik')->nullable();
            $table->string('no_whatsapp_publik')->nullable();
            $table->string('email_publik')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('status_website')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('mosque_id');
            $table->index('status_website');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
