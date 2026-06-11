<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('transaction_categories')
            ->where('type', 'masuk')
            ->where('name', '!=', 'Transfer dari ZIS')
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('transaction_categories')
            ->where('type', 'masuk')
            ->where('name', '!=', 'Transfer dari ZIS')
            ->update([
                'is_active' => true,
                'updated_at' => now(),
            ]);
    }
};
