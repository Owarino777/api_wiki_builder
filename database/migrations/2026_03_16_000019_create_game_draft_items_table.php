<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_draft_id')->constrained('game_drafts')->cascadeOnDelete();
            $table->string('name', 140);
            $table->string('slug', 160);
            $table->string('slot', 60);
            $table->string('rarity', 40);
            $table->string('tier_level', 20)->nullable();
            $table->integer('score')->default(0);
            $table->integer('attack')->nullable();
            $table->integer('defense')->nullable();
            $table->integer('utility')->nullable();
            $table->integer('accuracy')->nullable();
            $table->integer('mobility')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['game_draft_id', 'slug']);
            $table->index(['game_draft_id', 'slot']);
            $table->index(['game_draft_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_items');
    }
};
