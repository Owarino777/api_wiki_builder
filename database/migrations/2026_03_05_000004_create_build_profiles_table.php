<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('archetypes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archetypes');
    }
};
