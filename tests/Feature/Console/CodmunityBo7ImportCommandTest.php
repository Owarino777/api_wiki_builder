<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('imports bo7 weapons, modifiers, slots and builds from codmunity endpoints', function (): void {
    Http::fake([
        'https://api.codmunity.gg/website/assets/meta-ranking/one/bo7' => Http::response([
            'topWeapons' => [
                [
                    'weapon' => 'M15 Mod 0',
                    'slug' => 'm15-mod-0',
                    'game' => 'bo7',
                    'popularity' => 17.3,
                ],
            ],
        ]),
        'https://api.codmunity.gg/website/assets/attachments-data' => Http::response([
            'weapons' => [
                [
                    '_id' => 'weapon-1',
                    'WeaponName' => 'M15 Mod 0',
                    'Category' => 'Assault Rifle',
                    'Description' => 'Full-auto assault rifle.',
                    'Unlock' => 0,
                    'ShareNumber' => 2,
                    'Icon' => 'https://assets.codmunity.gg/optimized/M15-Mod-0-Green.webp',
                    'Link' => 'https://warzoneloadout.games/bo7/m15-mod-0/',
                    'slug' => 'm15-mod-0',
                    'appGame' => 'bo7',
                    'game' => 'bo7',
                    'slots' => ['Optic', 'Muzzle'],
                    'attachments' => [
                        'Optic' => [
                            'limit' => false,
                            'universalStats' => false,
                            'list' => ['optic-1'],
                        ],
                        'Muzzle' => [
                            'limit' => true,
                            'universalStats' => false,
                            'list' => [
                                ['_id' => 'muzzle-1'],
                            ],
                        ],
                    ],
                ],
            ],
            'attachments' => [
                [
                    '_id' => 'optic-1',
                    'collec' => 'bo7-attachments',
                    'Attachmentname' => 'Kepler Microflex',
                    'Label' => 'Microflex',
                    'LabelMP' => 'Kepler Microflex',
                    'Slot' => 'Optic',
                    'WeaponUnlock' => 'M15 Mod 0',
                    'Unlock Level' => 3,
                    'Icon' => 'https://assets.codmunity.gg/optimized/kepler-microflex.webp',
                ],
                [
                    '_id' => 'muzzle-1',
                    'collec' => 'bo7-attachments',
                    'Attachmentname' => 'Monolithic Suppressor',
                    'Label' => 'Suppressor',
                    'LabelMP' => 'Monolithic Suppressor',
                    'Slot' => 'Muzzle',
                    'WeaponUnlock' => 'M15 Mod 0',
                    'Unlock Level' => 5,
                    'Icon' => 'https://assets.codmunity.gg/optimized/monolithic-suppressor.webp',
                ],
            ],
        ]),
        'https://api.codmunity.gg/weapon-stats' => Http::response([
            [
                'game' => 'bo7',
                'gun' => 'M15 Mod 0',
                'weapon_type' => 'Assault Rifle',
                'sourceGame' => 'bo7',
                'avgTtk1' => 312,
                'avgTtk2' => 340,
                'avgTtk3' => 422,
                'avgTtk4' => 477,
                'ads' => 250,
                'stf' => 210,
                'reload' => 3000,
                'bv' => 730,
                'rpm' => 769,
                'gun_kick' => 15.5,
                'horiz_recoil' => 11.11,
                'vert_recoil' => 46.67,
                'movement' => 4.7,
                'sprint' => 6.8,
                'ads_movement' => 2.9,
                'crouch_movement' => 2.6,
                'mag_size' => 30,
                'hitscan_range' => 36.5,
                'easeScore' => 7.8,
            ],
        ]),
        'https://api.codmunity.gg/attachment-stats' => Http::response([
            [
                'game' => 'bo7',
                'gun' => 'M15 Mod 0',
                'attachment' => 'Kepler Microflex',
                'ads_mod' => 0.96,
                'horiz_recoil_mod' => 0.98,
                'vert_recoil_mod' => 0.97,
            ],
            [
                'game' => 'bo7',
                'gun' => 'M15 Mod 0',
                'attachment' => 'Monolithic Suppressor',
                'bv_mod' => 1.14,
                'range_mod' => 1.08,
                'ads_mod' => 1.05,
            ],
        ]),
        'https://api.codmunity.gg/website/assets/weapon/bo7/m15-mod-0' => Http::response([
            'weapon' => [
                'WeaponName' => 'M15 Mod 0',
                'slug' => 'm15-mod-0',
            ],
            'loadouts' => [
                'bo7-mp' => [
                    [
                        'active' => true,
                        'WeaponName' => 'M15 Mod 0',
                        'Playstyle' => 'Versatile',
                        'LoadoutCode' => 'A01-21X3B-KI9MW-PB11',
                        'Description' => 'Stable versatile BO7 build.',
                        'Optic' => 'Kepler Microflex',
                        'Muzzle' => 'Monolithic Suppressor',
                    ],
                ],
            ],
            'creatorLoadouts' => [
                'bo7' => [
                    [
                        'playstyle' => 'Versatile',
                    ],
                ],
            ],
        ]),
    ]);

    Artisan::call('codmunity:import-bo7', ['--fresh' => true]);

    $gameId = DB::table('games')
        ->where('slug', 'call-of-duty-black-ops-7')
        ->value('id');

    expect($gameId)->not->toBeNull();

    $item = DB::table('items')
        ->where('game_id', $gameId)
        ->where('slug', 'm15-mod-0')
        ->first();

    expect($item)->not->toBeNull();
    expect($item->slot)->toBe('weapon');
    expect($item->rarity)->toBe('standard-issue');
    expect($item->tier_level)->toBe('L0');
    expect($item->score)->toBeGreaterThan(0);

    $itemMetadata = json_decode($item->metadata, true, 512, JSON_THROW_ON_ERROR);

    expect($itemMetadata['seed_origin'])->toBe('codmunity-bo7');
    expect($itemMetadata['source']['image_url'])->toBe('https://assets.codmunity.gg/optimized/M15-Mod-0-Green.webp');

    $slots = DB::table('item_slots')
        ->where('item_id', $item->id)
        ->orderBy('sort_order')
        ->get();

    expect($slots)->toHaveCount(2);
    expect($slots[0]->slot_key)->toBe('optic');
    expect($slots[1]->slot_key)->toBe('muzzle');

    $modifier = DB::table('modifiers')
        ->where('game_id', $gameId)
        ->where('name', 'Monolithic Suppressor')
        ->first();

    expect($modifier)->not->toBeNull();
    expect($modifier->modifier_type)->toBe('muzzle');
    expect($modifier->delta_attack)->toBeGreaterThan(0);

    $modifierMetadata = json_decode($modifier->metadata, true, 512, JSON_THROW_ON_ERROR);

    expect($modifierMetadata['codmunity']['image_url'])->toBe('https://assets.codmunity.gg/optimized/monolithic-suppressor.webp');

    $assaultRifleCategoryId = DB::table('categories')
        ->where('slug', 'assault-rifle')
        ->value('id');

    $versatileCategoryId = DB::table('categories')
        ->where('slug', 'versatile')
        ->value('id');

    expect(DB::table('item_categories')
        ->where('item_id', $item->id)
        ->where('category_id', $assaultRifleCategoryId)
        ->exists())->toBeTrue();

    expect(DB::table('item_categories')
        ->where('item_id', $item->id)
        ->where('category_id', $versatileCategoryId)
        ->exists())->toBeTrue();

    $build = DB::table('builds')
        ->where('game_id', $gameId)
        ->where('share_token', 'codmunity-bo7-m15-mod-0-a01-21x3b-ki9mw-pb11')
        ->first();

    expect($build)->not->toBeNull();
    expect($build->title)->toBe('M15 Mod 0 - Versatile');
    expect($build->is_public)->toBe(1);

    expect(DB::table('build_items')
        ->where('build_id', $build->id)
        ->where('item_id', $item->id)
        ->where('position', 1)
        ->exists())->toBeTrue();

    expect(DB::table('build_slots')
        ->where('build_id', $build->id)
        ->where('item_id', $item->id)
        ->count())->toBe(2);
});
