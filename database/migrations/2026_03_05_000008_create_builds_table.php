<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('builds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('archetype_id')->nullable()->constrained('archetypes')->onDelete('set null');
            $table->string('title', 140);
            $table->text('description')->nullable();
            $table->string('share_token', 64)->nullable()->unique();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('archetype_id');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builds');
    }
};
