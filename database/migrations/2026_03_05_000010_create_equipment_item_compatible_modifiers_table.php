<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment_item_compatible_modifiers', function (Blueprint $table): void {
            $table->foreignId('equipment_item_id')
                ->constrained('equipment_items', indexName: 'eicm_item_fk')
                ->onDelete('cascade');
            $table->foreignId('equipment_modifier_id')
                ->constrained('equipment_modifiers', indexName: 'eicm_modifier_fk')
                ->onDelete('cascade');
            $table->primary(
                ['equipment_item_id', 'equipment_modifier_id'],
                'eicm_pk'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_item_compatible_modifiers');
    }
};
