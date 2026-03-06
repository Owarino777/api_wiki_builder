<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('name', 140);
            $table->string('slug', 160);
            $table->string('external_ref', 160)->nullable();
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

            $table->unique(['game_id', 'slug']);
            $table->index(['game_id', 'external_ref']);
            $table->index('slot');
            $table->index('rarity');
            $table->index(['game_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
