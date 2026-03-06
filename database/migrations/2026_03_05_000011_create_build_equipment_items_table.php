<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_items', function (Blueprint $table): void {
            $table->foreignId('build_id')->constrained('builds')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->unsignedSmallInteger('position')->nullable();
            $table->primary(['build_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_items');
    }
};
