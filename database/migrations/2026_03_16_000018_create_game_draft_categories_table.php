<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_draft_id')->constrained('game_drafts')->cascadeOnDelete();
            $table->string('label', 80);
            $table->string('slug', 100);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['game_draft_id', 'label']);
            $table->unique(['game_draft_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_categories');
    }
};
