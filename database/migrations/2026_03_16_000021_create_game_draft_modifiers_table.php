<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_modifiers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_draft_id')->constrained('game_drafts')->cascadeOnDelete();
            $table->string('name', 140);
            $table->string('slug', 160);
            $table->string('modifier_type', 60);
            $table->text('description')->nullable();
            $table->integer('delta_score')->default(0);
            $table->integer('delta_attack')->nullable();
            $table->integer('delta_defense')->nullable();
            $table->integer('delta_utility')->nullable();
            $table->integer('delta_accuracy')->nullable();
            $table->integer('delta_mobility')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['game_draft_id', 'name']);
            $table->unique(['game_draft_id', 'slug']);
            $table->index(['game_draft_id', 'modifier_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_modifiers');
    }
};
