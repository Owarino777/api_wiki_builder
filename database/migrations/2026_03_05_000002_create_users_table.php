<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('username', 80)->unique();
            $table->string('email', 190)->unique();
            $table->string('role', 30);
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('display_name', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('address_line_1', 190)->nullable();
            $table->string('address_line_2', 190)->nullable();
            $table->string('avatar_url')->nullable();
            $table->boolean('is_anonymized')->default(false);
            $table->timestamp('anonymized_at')->nullable();
            $table->timestamps();

            $table->index('role');
            $table->index('is_anonymized');
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
