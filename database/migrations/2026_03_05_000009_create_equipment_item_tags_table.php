<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment_item_tags', function (Blueprint $table): void {
            $table->foreignId('equipment_item_id')->constrained('equipment_items')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->primary(['equipment_item_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_item_tags');
    }
};
