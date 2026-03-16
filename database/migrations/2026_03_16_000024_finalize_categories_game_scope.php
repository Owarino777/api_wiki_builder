<?php

use App\Services\CategoryGameScopeBackfillService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique('categories_slug_unique');
            $table->dropUnique('categories_label_unique');
        });

        DB::transaction(function (): void {
            (new CategoryGameScopeBackfillService())->backfill();
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->unsignedBigInteger('game_id')->nullable(false)->change();
            $table->dropIndex('categories_game_id_slug_index');
            $table->unique(['game_id', 'slug']);
            $table->unique(['game_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique('categories_game_id_slug_unique');
            $table->dropUnique('categories_game_id_label_unique');
            $table->index(['game_id', 'slug']);
            $table->unsignedBigInteger('game_id')->nullable()->change();
            $table->unique('slug');
            $table->unique('label');
        });
    }
};
