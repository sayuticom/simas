<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->nullable()->constrained('mosques')->nullOnDelete();
            $table->string('name');
            $table->string('module_type')->nullable();
            $table->string('design_type');
            $table->string('canvas_size', 50)->nullable();
            $table->json('platforms')->nullable();
            $table->string('tone')->nullable();
            $table->string('style')->nullable();
            $table->string('color_palette')->nullable();
            $table->string('target_audience')->nullable();
            $table->string('layout_density')->nullable();
            $table->json('elements')->nullable();
            $table->json('required_text_rules')->nullable();
            $table->json('photo_rules')->nullable();
            $table->longText('prompt_structure');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mosque_id', 'module_type', 'is_active'], 'design_prompt_templates_mosque_module_active_index');
            $table->index('module_type');
            $table->index('design_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_prompt_templates');
    }
};
