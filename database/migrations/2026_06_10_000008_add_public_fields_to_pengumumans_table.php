<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumumans', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('judul');
            $table->text('excerpt')->nullable()->after('slug');
            $table->string('featured_image')->nullable()->after('excerpt');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->index(['mosque_id', 'status', 'published_at'], 'pengumumans_public_index');
            $table->unique(['mosque_id', 'slug'], 'pengumumans_mosque_slug_unique');
        });

        DB::table('pengumumans')
            ->where('status', 'terbit')
            ->whereNull('published_at')
            ->orderBy('id')
            ->get()
            ->each(function ($pengumuman): void {
                $baseSlug = Str::slug($pengumuman->judul) ?: 'pengumuman-'.$pengumuman->id;
                $slug = $baseSlug;
                $counter = 2;

                while (DB::table('pengumumans')
                    ->where('mosque_id', $pengumuman->mosque_id)
                    ->where('slug', $slug)
                    ->where('id', '<>', $pengumuman->id)
                    ->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                DB::table('pengumumans')
                    ->where('id', $pengumuman->id)
                    ->update([
                        'slug' => $slug,
                        'published_at' => $pengumuman->tanggal_mulai ?: $pengumuman->created_at,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('pengumumans', function (Blueprint $table) {
            $table->dropUnique('pengumumans_mosque_slug_unique');
            $table->dropIndex('pengumumans_public_index');
            $table->dropColumn([
                'slug',
                'excerpt',
                'featured_image',
                'published_at',
            ]);
        });
    }
};
