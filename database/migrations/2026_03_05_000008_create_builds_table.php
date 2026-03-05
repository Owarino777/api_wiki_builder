<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('builds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('build_profile_id')->nullable()->constrained('build_profiles')->onDelete('set null');
            $table->string('title', 140);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('build_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builds');
    }
};
