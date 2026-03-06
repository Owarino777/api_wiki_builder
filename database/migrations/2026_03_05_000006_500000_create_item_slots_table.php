<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('slot_key', 60);
            $table->string('slot_label', 80);
            $table->string('accepted_modifier_type', 60)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'slot_key']);
            $table->index('accepted_modifier_type');
            $table->index(['item_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_slots');
    }
};
