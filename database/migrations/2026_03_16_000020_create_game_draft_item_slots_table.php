<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_item_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_draft_item_id')->constrained('game_draft_items')->cascadeOnDelete();
            $table->string('slot_key', 60);
            $table->string('slot_label', 80);
            $table->string('accepted_modifier_type', 60)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['game_draft_item_id', 'slot_key']);
            $table->index(['game_draft_item_id', 'sort_order']);
            $table->index('accepted_modifier_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_item_slots');
    }
};
