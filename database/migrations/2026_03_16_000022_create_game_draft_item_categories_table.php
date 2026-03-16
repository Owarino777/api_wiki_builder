<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_item_categories', function (Blueprint $table): void {
            $table->foreignId('game_draft_item_id')->constrained('game_draft_items')->cascadeOnDelete();
            $table->foreignId('game_draft_category_id')->constrained('game_draft_categories')->cascadeOnDelete();
            $table->primary(['game_draft_item_id', 'game_draft_category_id']);
            $table->index('game_draft_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_item_categories');
    }
};
