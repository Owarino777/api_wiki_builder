<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('label', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('username', 80)->unique();
            $table->string('email', 190)->unique();
            $table->string('role', 30);
            $table->timestamps();
        });

        Schema::create('games', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->string('genre', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('build_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games');
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('game_id', 'build_profiles_index_0');
            $table->unique(['game_id', 'name'], 'build_profiles_index_1');
        });

        Schema::create('equipment_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games');
            $table->string('name', 140);
            $table->string('slot', 60);
            $table->string('rarity', 40);
            $table->string('tier_level', 20)->nullable();
            $table->integer('score')->default(0);
            $table->integer('attack')->nullable();
            $table->integer('defense')->nullable();
            $table->integer('utility')->nullable();
            $table->integer('accuracy')->nullable();
            $table->integer('mobility')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('game_id', 'equipment_items_index_2');
            $table->index('slot', 'equipment_items_index_3');
            $table->index('rarity', 'equipment_items_index_4');
            $table->index(['game_id', 'score'], 'equipment_items_index_5');
            $table->unique(['game_id', 'name'], 'equipment_items_index_6');
        });

        Schema::create('equipment_modifiers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games');
            $table->string('name', 140);
            $table->string('modifier_type', 60);
            $table->text('description')->nullable();
            $table->integer('delta_score')->default(0);
            $table->integer('delta_attack')->nullable();
            $table->integer('delta_defense')->nullable();
            $table->integer('delta_utility')->nullable();
            $table->integer('delta_accuracy')->nullable();
            $table->integer('delta_mobility')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('game_id', 'equipment_modifiers_index_7');
            $table->index('modifier_type', 'equipment_modifiers_index_8');
            $table->index(['game_id', 'modifier_type'], 'equipment_modifiers_index_9');
            $table->unique(['game_id', 'name'], 'equipment_modifiers_index_10');
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->string('label', 80)->unique();
            $table->timestamps();
        });

        Schema::create('builds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('build_profile_id')->nullable()->constrained('build_profiles');
            $table->string('title', 140);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('game_id', 'builds_index_11');
            $table->index('user_id', 'builds_index_12');
            $table->index('build_profile_id', 'builds_index_13');
        });

        Schema::create('equipment_item_tags', function (Blueprint $table): void {
            $table->foreignId('equipment_item_id')->constrained('equipment_items');
            $table->foreignId('tag_id')->constrained('tags');
            $table->primary(['equipment_item_id', 'tag_id']);

            $table->index('tag_id', 'equipment_item_tags_index_14');
        });

        Schema::create('equipment_item_compatible_modifiers', function (Blueprint $table): void {
            $table->foreignId('equipment_item_id')->constrained('equipment_items');
            $table->foreignId('equipment_modifier_id')->constrained('equipment_modifiers');
            $table->primary(['equipment_item_id', 'equipment_modifier_id']);

            $table->index('equipment_modifier_id', 'equipment_item_compatible_modifiers_index_15');
        });

        Schema::create('build_equipment_items', function (Blueprint $table): void {
            $table->foreignId('build_id')->constrained('builds');
            $table->foreignId('equipment_item_id')->constrained('equipment_items');
            $table->smallInteger('position')->nullable();
            $table->primary(['build_id', 'equipment_item_id']);

            $table->index('equipment_item_id', 'build_equipment_items_index_16');
        });

        Schema::create('build_equipment_modifiers', function (Blueprint $table): void {
            $table->foreignId('build_id')->constrained('builds');
            $table->foreignId('equipment_item_id')->constrained('equipment_items');
            $table->foreignId('equipment_modifier_id')->constrained('equipment_modifiers');
            $table->primary(['build_id', 'equipment_item_id', 'equipment_modifier_id']);

            $table->index('equipment_modifier_id', 'build_equipment_modifiers_index_17');
            $table->index('equipment_item_id', 'build_equipment_modifiers_index_18');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_equipment_modifiers');
        Schema::dropIfExists('build_equipment_items');
        Schema::dropIfExists('equipment_item_compatible_modifiers');
        Schema::dropIfExists('equipment_item_tags');
        Schema::dropIfExists('builds');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('equipment_modifiers');
        Schema::dropIfExists('equipment_items');
        Schema::dropIfExists('build_profiles');
        Schema::dropIfExists('games');
        Schema::dropIfExists('users');
        Schema::dropIfExists('api_keys');
    }
};
