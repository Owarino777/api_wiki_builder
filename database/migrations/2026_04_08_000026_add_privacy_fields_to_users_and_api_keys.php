<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('privacy_policy_accepted_at')->nullable()->after('anonymized_at');
            $table->string('privacy_policy_version', 30)->nullable()->after('privacy_policy_accepted_at');
            $table->timestamp('marketing_consent_at')->nullable()->after('privacy_policy_version');
            $table->timestamp('last_login_at')->nullable()->after('marketing_consent_at');
        });

        Schema::table('api_keys', function (Blueprint $table): void {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'is_active']);
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'privacy_policy_accepted_at',
                'privacy_policy_version',
                'marketing_consent_at',
                'last_login_at',
            ]);
        });
    }
};
