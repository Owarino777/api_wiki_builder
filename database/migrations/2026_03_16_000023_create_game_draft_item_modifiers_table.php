<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_item_modifiers', function (Blueprint $table): void {
            $table->foreignId('game_draft_item_id')->constrained('game_draft_items')->cascadeOnDelete();
            $table->foreignId('game_draft_modifier_id')->constrained('game_draft_modifiers')->cascadeOnDelete();
            $table->primary(['game_draft_item_id', 'game_draft_modifier_id']);
            $table->index('game_draft_modifier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_item_modifiers');
    }
};
