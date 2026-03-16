<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_draft_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_draft_id')->constrained('game_drafts')->cascadeOnDelete();
            $table->string('step_key', 40);
            $table->string('status', 20)->default('not_started');
            $table->unsignedSmallInteger('blocking_errors_count')->default(0);
            $table->unsignedSmallInteger('warnings_count')->default(0);
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamp('last_validated_at')->nullable();
            $table->json('payload_snapshot')->nullable();
            $table->timestamps();

            $table->unique(['game_draft_id', 'step_key']);
            $table->index(['game_draft_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_draft_steps');
    }
};
