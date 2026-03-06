<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_categories', function (Blueprint $table): void {
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->primary(['item_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_categories');
    }
};
