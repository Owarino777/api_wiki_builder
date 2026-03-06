<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modifiers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('name', 140);
            $table->string('slug', 160);
            $table->string('external_ref', 160)->nullable();
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

            $table->unique(['game_id', 'slug']);
            $table->index(['game_id', 'external_ref']);
            $table->index('modifier_type');
            $table->index(['game_id', 'modifier_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modifiers');
    }
};
