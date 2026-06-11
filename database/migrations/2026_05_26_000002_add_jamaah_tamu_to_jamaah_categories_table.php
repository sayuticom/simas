<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('jamaah_categories')->updateOrInsert(
            ['name' => 'jamaah_tamu'],
            [
                'label' => 'Jamaah Tamu',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        // Preserve reference data that may already be assigned to jamaah records.
    }
};
