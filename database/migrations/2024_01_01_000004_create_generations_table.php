<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['text', 'image', 'code']);
            $table->string('model_used');
            $table->text('prompt');
            $table->longText('result');
            $table->string('image_url')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('tokens_used')->default(0);
            $table->integer('word_count')->default(0);
            $table->decimal('cost', 8, 6)->default(0);
            $table->json('metadata')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
