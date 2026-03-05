<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_equipment_modifiers', function (Blueprint $table): void {
            $table->foreignId('build_id')->constrained('builds')->onDelete('cascade');
            $table->foreignId('equipment_item_id')
                ->constrained('equipment_items', indexName: 'bem_item_fk')
                ->onDelete('cascade');
            $table->foreignId('equipment_modifier_id')
                ->constrained('equipment_modifiers', indexName: 'bem_modifier_fk')
                ->onDelete('cascade');
            $table->primary(
                ['build_id', 'equipment_item_id', 'equipment_modifier_id'],
                'bem_pk'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_equipment_modifiers');
    }
};
