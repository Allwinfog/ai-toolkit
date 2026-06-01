<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->default('file-text');
            $table->string('category');
            $table->enum('type', ['text', 'image', 'code'])->default('text');
            $table->text('system_prompt');
            $table->text('user_prompt_template');
            $table->json('fields')->comment('Input fields definition');
            $table->string('model')->nullable()->comment('Override default model');
            $table->integer('max_tokens')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->integer('usage_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
