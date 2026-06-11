<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->cascadeOnDelete();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('design_prompt_template_id')->nullable()->constrained('design_prompt_templates')->nullOnDelete();
            $table->string('title');
            $table->longText('prompt_text');
            $table->longText('negative_prompt')->nullable();
            $table->string('generated_image_path')->nullable();
            $table->string('reference_image_path')->nullable();
            $table->json('selected_options')->nullable();
            $table->json('source_snapshot')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mosque_id', 'status']);
            $table->index(['source_type', 'source_id']);
            $table->index('design_prompt_template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_requests');
    }
};
