<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('api_keys', function (Blueprint $table) use ($driver): void {
            $col = $table->string('key_hash', 64)->default('');
            if ($driver !== 'sqlite') {
                $col->after('id');
            }
        });

        if ($driver !== 'sqlite') {
            DB::table('api_keys')->whereNotNull('key')->update([
                'key_hash' => DB::raw("SHA2(`key`, 256)"),
            ]);

            Schema::table('api_keys', function (Blueprint $table): void {
                $table->dropUnique(['key']);
                $table->dropColumn('key');
            });
        } else {
            Schema::table('api_keys', function (Blueprint $table): void {
                $table->dropUnique(['key']);
            });
            Schema::table('api_keys', function (Blueprint $table): void {
                $table->dropColumn('key');
            });
        }

        Schema::table('api_keys', function (Blueprint $table): void {
            $table->unique('key_hash');
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table): void {
            $table->string('key', 64)->nullable();
            $table->dropUnique(['key_hash']);
            $table->dropColumn('key_hash');
        });
    }
};
