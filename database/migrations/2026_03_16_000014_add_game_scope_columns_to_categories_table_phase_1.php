<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained('games')
                ->cascadeOnDelete();
            $table->string('source_type', 20)->default('system')->after('slug');

            $table->index('game_id');
            $table->index(['game_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex(['game_id', 'slug']);
            $table->dropIndex(['game_id']);
            $table->dropConstrainedForeignId('game_id');
            $table->dropColumn('source_type');
        });
    }
};
