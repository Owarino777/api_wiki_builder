<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WikiBuilderSeeder::class,
            Bo7OfflineSeeder::class,
            BattlefieldSeeder::class,
            ValorantSeeder::class,
            LeagueOfLegendsSeeder::class,
            NewWorldSeeder::class,
        ]);
    }
}
