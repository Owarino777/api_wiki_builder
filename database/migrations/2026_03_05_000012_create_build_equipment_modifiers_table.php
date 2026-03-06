<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_slots', function (Blueprint $table): void {
            $table->foreignId('build_id')->constrained('builds')->onDelete('cascade');
            $table->foreignId('item_id')
                ->constrained('items', indexName: 'bs_item_fk')
                ->onDelete('cascade');
            $table->foreignId('item_slot_id')
                ->constrained('item_slots', indexName: 'bs_item_slot_fk')
                ->onDelete('cascade');
            $table->foreignId('modifier_id')
                ->constrained('modifiers', indexName: 'bs_modifier_fk')
                ->onDelete('cascade');
            $table->primary(
                ['build_id', 'item_id', 'item_slot_id'],
                'bs_pk'
            );
            $table->unique(['build_id', 'item_id', 'modifier_id'], 'bs_build_item_modifier_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_slots');
    }
};
