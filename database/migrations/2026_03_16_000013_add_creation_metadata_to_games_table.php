<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table): void {
            $table->foreignId('owner_user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('creation_category', 40)->nullable()->after('genre');
            $table->string('source_type', 20)->default('system')->after('creation_category');
            $table->string('visibility', 20)->default('public')->after('source_type');
            $table->timestamp('published_at')->nullable()->after('visibility');

            $table->index('owner_user_id');
            $table->index('source_type');
            $table->index('visibility');
            $table->index(['source_type', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table): void {
            $table->dropIndex(['source_type', 'visibility']);
            $table->dropIndex(['visibility']);
            $table->dropIndex(['source_type']);
            $table->dropIndex(['owner_user_id']);
            $table->dropConstrainedForeignId('owner_user_id');
            $table->dropColumn([
                'creation_category',
                'source_type',
                'visibility',
                'published_at',
            ]);
        });
    }
};
