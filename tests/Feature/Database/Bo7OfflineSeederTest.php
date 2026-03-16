<?php

use Database\Seeders\Bo7OfflineSeeder;
use Database\Seeders\WikiBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('seeds a real offline bo7 demo dataset with several weapons and public builds', function (): void {
    $this->seed(WikiBuilderSeeder::class);
    $this->seed(Bo7OfflineSeeder::class);

    $gameId = DB::table('games')
        ->where('slug', 'call-of-duty-black-ops-7')
        ->value('id');

    expect($gameId)->not->toBeNull();

    $weaponCount = DB::table('items')
        ->where('game_id', $gameId)
        ->count();

    $buildCount = DB::table('builds')
        ->where('game_id', $gameId)
        ->where('is_public', true)
        ->count();

    expect($weaponCount)->toBeGreaterThanOrEqual(3);
    expect($buildCount)->toBeGreaterThanOrEqual(3);

    expect(DB::table('items')
        ->where('game_id', $gameId)
        ->where('slug', 'm15-mod-0')
        ->exists())->toBeTrue();

    expect(DB::table('items')
        ->where('game_id', $gameId)
        ->where('slug', 'c9')
        ->exists())->toBeTrue();

    expect(DB::table('items')
        ->where('game_id', $gameId)
        ->where('slug', 'swat-5-56')
        ->exists())->toBeTrue();
});

it('keeps the bo7 offline dataset stable when the seeder is run twice', function (): void {
    $this->seed(WikiBuilderSeeder::class);
    $this->seed(Bo7OfflineSeeder::class);
    $this->seed(Bo7OfflineSeeder::class);

    $gameId = DB::table('games')
        ->where('slug', 'call-of-duty-black-ops-7')
        ->value('id');

    expect(DB::table('items')
        ->where('game_id', $gameId)
        ->count())->toBe(3);

    expect(DB::table('builds')
        ->where('game_id', $gameId)
        ->count())->toBe(3);

    expect(DB::table('item_slots')
        ->join('items', 'items.id', '=', 'item_slots.item_id')
        ->where('items.game_id', $gameId)
        ->count())->toBe(9);
});
