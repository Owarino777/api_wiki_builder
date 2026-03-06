<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_modifiers', function (Blueprint $table): void {
            $table->foreignId('item_id')
                ->constrained('items', indexName: 'im_item_fk')
                ->onDelete('cascade');
            $table->foreignId('modifier_id')
                ->constrained('modifiers', indexName: 'im_modifier_fk')
                ->onDelete('cascade');
            $table->primary(
                ['item_id', 'modifier_id'],
                'im_pk'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_modifiers');
    }
};
