<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_drafts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('published_game_id')->nullable()->constrained('games')->nullOnDelete();
            $table->string('category_key', 40);
            $table->string('template_version', 20);
            $table->string('status', 20)->default('draft');
            $table->string('current_step', 40)->default('foundation');
            $table->string('name', 120);
            $table->string('slug', 120);
            $table->text('description')->nullable();
            $table->string('visibility', 20)->default('private');
            $table->unsignedSmallInteger('completion_percent')->nullable();
            $table->timestamp('last_validated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('settings')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['owner_user_id', 'slug']);
            $table->unique('published_game_id');
            $table->index(['owner_user_id', 'status']);
            $table->index(['owner_user_id', 'updated_at']);
            $table->index('category_key');
            $table->index('current_step');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_drafts');
    }
};
