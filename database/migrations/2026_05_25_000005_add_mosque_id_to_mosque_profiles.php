<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mosque_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('mosque_profiles', 'mosque_id')) {
                $table->foreignId('mosque_id')->nullable()->after('id')->constrained('mosques')->nullOnDelete();
                $table->unique('mosque_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mosque_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('mosque_profiles', 'mosque_id')) {
                $table->dropUnique(['mosque_id']);
                $table->dropForeign(['mosque_id']);
                $table->dropColumn('mosque_id');
            }
        });
    }
};
