<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WikiBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::transaction(function () use ($now): void {
            $this->truncateTables();

            DB::table('games')->insert([
                [
                    'name' => 'Call of Duty: Black Ops III',
                    'slug' => 'call-of-duty-black-ops-iii',
                    'genre' => 'Arena Shooter',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Steel Horizon',
                    'slug' => 'steel-horizon',
                    'genre' => 'Tactical Shooter',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            $gameIds = DB::table('games')->pluck('id', 'slug');
            $bo3GameId = (int) $gameIds['call-of-duty-black-ops-iii'];
            $steelGameId = (int) $gameIds['steel-horizon'];

            DB::table('users')->insert([
                [
                    'username' => 'admin',
                    'email' => 'admin@wikibuilder.local',
                    'role' => 'admin',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'username' => 'player1',
                    'email' => 'player1@wikibuilder.local',
                    'role' => 'user',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            DB::table('api_keys')->insert([
                'key' => 'dev-wiki-builder-key-1234567890abcdef',
                'label' => 'Postman Dev Key',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('build_profiles')->insert(array_merge(
                $this->blackOpsThreeProfiles($bo3GameId, $now),
                $this->steelHorizonProfiles($steelGameId, $now)
            ));

            DB::table('equipment_items')->insert(array_merge(
                $this->blackOpsThreeItems($bo3GameId, $now),
                $this->steelHorizonItems($steelGameId, $now)
            ));

            DB::table('equipment_modifiers')->insert(array_merge(
                $this->blackOpsThreeModifiers($bo3GameId, $now),
                $this->steelHorizonModifiers($steelGameId, $now)
            ));

            DB::table('tags')->insert([
                ['label' => 'bo3', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'assault-rifle', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'smg', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'burst', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'precision', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'aggressive', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'high-capacity', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'versatile', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'prototype', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'shooter', 'created_at' => $now, 'updated_at' => $now],
            ]);

            $tagIds = DB::table('tags')->pluck('id', 'label');
            $itemIds = DB::table('equipment_items')->pluck('id', 'name');
            $modifierIds = DB::table('equipment_modifiers')->pluck('id', 'name');
            $users = DB::table('users')->pluck('id', 'username');
            $profiles = DB::table('build_profiles')->pluck('id', 'name');

            DB::table('equipment_item_tags')->insertOrIgnore(array_merge(
                $this->blackOpsThreeItemTags($itemIds, $tagIds),
                $this->steelHorizonItemTags($steelGameId, $tagIds)
            ));

            DB::table('equipment_item_compatible_modifiers')->insertOrIgnore(array_merge(
                $this->blackOpsThreeCompatibility($itemIds, $modifierIds),
                $this->steelHorizonCompatibility($steelGameId)
            ));

            DB::table('builds')->insert([
                [
                    'game_id' => $bo3GameId,
                    'user_id' => $users['player1'],
                    'build_profile_id' => $profiles['BO3 Flex Rifle'],
                    'title' => 'BO3 Ranked Flex Core',
                    'description' => 'A compact BO3 sample build seeded with verified weapons and attachments.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $steelGameId,
                    'user_id' => $users['admin'],
                    'build_profile_id' => $profiles['Precision Sniper'],
                    'title' => 'Steel Horizon Sniper Setup',
                    'description' => 'Seeded sample build for compare endpoint.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            $buildIds = DB::table('builds')->pluck('id', 'title');

            DB::table('build_equipment_items')->insertOrIgnore([
                ['build_id' => $buildIds['BO3 Ranked Flex Core'], 'equipment_item_id' => $itemIds['KN-44'], 'position' => 1],
                ['build_id' => $buildIds['BO3 Ranked Flex Core'], 'equipment_item_id' => $itemIds['ICR-1'], 'position' => 2],
                ['build_id' => $buildIds['BO3 Ranked Flex Core'], 'equipment_item_id' => $itemIds['VMP'], 'position' => 3],
                ['build_id' => $buildIds['Steel Horizon Sniper Setup'], 'equipment_item_id' => $itemIds['SH Item 1'], 'position' => 1],
                ['build_id' => $buildIds['Steel Horizon Sniper Setup'], 'equipment_item_id' => $itemIds['SH Item 2'], 'position' => 2],
                ['build_id' => $buildIds['Steel Horizon Sniper Setup'], 'equipment_item_id' => $itemIds['SH Item 3'], 'position' => 3],
            ]);

            DB::table('build_equipment_modifiers')->insertOrIgnore([
                [
                    'build_id' => $buildIds['BO3 Ranked Flex Core'],
                    'equipment_item_id' => $itemIds['KN-44'],
                    'equipment_modifier_id' => $modifierIds['Quickdraw Handle'],
                ],
                [
                    'build_id' => $buildIds['BO3 Ranked Flex Core'],
                    'equipment_item_id' => $itemIds['ICR-1'],
                    'equipment_modifier_id' => $modifierIds['Grip'],
                ],
                [
                    'build_id' => $buildIds['BO3 Ranked Flex Core'],
                    'equipment_item_id' => $itemIds['VMP'],
                    'equipment_modifier_id' => $modifierIds['Rapid Fire'],
                ],
                [
                    'build_id' => $buildIds['Steel Horizon Sniper Setup'],
                    'equipment_item_id' => $itemIds['SH Item 1'],
                    'equipment_modifier_id' => $modifierIds['SH Mod 1'],
                ],
                [
                    'build_id' => $buildIds['Steel Horizon Sniper Setup'],
                    'equipment_item_id' => $itemIds['SH Item 2'],
                    'equipment_modifier_id' => $modifierIds['SH Mod 2'],
                ],
                [
                    'build_id' => $buildIds['Steel Horizon Sniper Setup'],
                    'equipment_item_id' => $itemIds['SH Item 3'],
                    'equipment_modifier_id' => $modifierIds['SH Mod 3'],
                ],
            ]);
        });
    }

    private function truncateTables(): void
    {
        DB::table('api_keys')->delete();
        DB::table('build_equipment_modifiers')->delete();
        DB::table('build_equipment_items')->delete();
        DB::table('equipment_item_compatible_modifiers')->delete();
        DB::table('equipment_item_tags')->delete();
        DB::table('builds')->delete();
        DB::table('equipment_modifiers')->delete();
        DB::table('equipment_items')->delete();
        DB::table('build_profiles')->delete();
        DB::table('tags')->delete();
        DB::table('users')->delete();
        DB::table('games')->delete();
    }

    private function blackOpsThreeProfiles(int $gameId, object $now): array
    {
        return [
            [
                'game_id' => $gameId,
                'name' => 'BO3 Flex Rifle',
                'description' => 'Balanced Black Ops III profile for adaptable assault rifle play.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'game_id' => $gameId,
                'name' => 'BO3 Entry SMG',
                'description' => 'Fast BO3 profile tuned for aggressive SMG routes and close fights.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'game_id' => $gameId,
                'name' => 'BO3 Burst Anchor',
                'description' => 'Disciplined profile built around burst rifles and long lane control.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }

    private function steelHorizonProfiles(int $gameId, object $now): array
    {
        return [
            [
                'game_id' => $gameId,
                'name' => 'Precision Sniper',
                'description' => 'Long-range profile with accuracy priority.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'game_id' => $gameId,
                'name' => 'Close Quarters',
                'description' => 'Aggressive profile for short distance combat.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }

    private function blackOpsThreeItems(int $gameId, object $now): array
    {
        $items = [
            [
                'name' => 'KN-44',
                'tier_level' => 'L1',
                'score' => 84,
                'attack' => 82,
                'utility' => 68,
                'accuracy' => 74,
                'mobility' => 66,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 1,
                        'damage_profile' => 'moderate',
                        'handling_profile' => 'versatile',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/KN-44',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/b/b8/KN44_BO3.png/revision/latest?cb=20151114232306',
                    ],
                ],
            ],
            [
                'name' => 'ICR-1',
                'tier_level' => 'L2',
                'score' => 83,
                'attack' => 74,
                'utility' => 70,
                'accuracy' => 92,
                'mobility' => 64,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 2,
                        'damage_profile' => 'low recoil',
                        'handling_profile' => 'precision',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/ICR-1',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/e/e2/ICR-1_BO3.png/revision/latest?cb=20151114232253',
                    ],
                ],
            ],
            [
                'name' => 'XR-2',
                'tier_level' => 'L7',
                'score' => 86,
                'attack' => 88,
                'utility' => 62,
                'accuracy' => 84,
                'mobility' => 58,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'burst',
                        'unlock_level' => 7,
                        'damage_profile' => 'burst',
                        'handling_profile' => 'lane control',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/XR-2',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/8/84/XR-2_BO3.png/revision/latest?cb=20151114232400',
                    ],
                ],
            ],
            [
                'name' => 'HVK-30',
                'tier_level' => 'L13',
                'score' => 82,
                'attack' => 72,
                'utility' => 76,
                'accuracy' => 72,
                'mobility' => 74,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 13,
                        'damage_profile' => 'high rate of fire',
                        'handling_profile' => 'aggressive flex',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/HVK-30',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/0/03/HVK-30_BO3.png/revision/latest?cb=20151114232319',
                    ],
                ],
            ],
            [
                'name' => 'Man-O-War',
                'tier_level' => 'L28',
                'score' => 88,
                'attack' => 90,
                'utility' => 58,
                'accuracy' => 70,
                'mobility' => 52,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 28,
                        'damage_profile' => 'high damage',
                        'handling_profile' => 'heavy rifle',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/Man-O-War',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/1/11/Man-O-War_BO3.png/revision/latest?cb=20151114232331',
                    ],
                ],
            ],
            [
                'name' => 'M8A7',
                'tier_level' => 'L16',
                'score' => 87,
                'attack' => 84,
                'utility' => 66,
                'accuracy' => 88,
                'mobility' => 57,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'burst',
                        'unlock_level' => 16,
                        'damage_profile' => 'consistent burst',
                        'handling_profile' => 'competitive anchor',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/M8A7',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/1/14/M8A7_BO3.png/revision/latest?cb=20151114232329',
                    ],
                ],
            ],
            [
                'name' => 'Sheiva',
                'tier_level' => 'L20',
                'score' => 85,
                'attack' => 92,
                'utility' => 55,
                'accuracy' => 90,
                'mobility' => 40,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'assault rifle',
                        'fire_mode' => 'semi-auto',
                        'unlock_level' => 20,
                        'damage_profile' => 'single-shot precision',
                        'handling_profile' => 'headshot weapon',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/Sheiva',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/b/b8/Sheiva_BO3.png/revision/latest?cb=20151114232341',
                    ],
                ],
            ],
            [
                'name' => 'Kuda',
                'tier_level' => 'L10',
                'score' => 85,
                'attack' => 78,
                'utility' => 76,
                'accuracy' => 76,
                'mobility' => 86,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'smg',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 10,
                        'damage_profile' => 'balanced smg',
                        'handling_profile' => 'stable close range',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/Kuda',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/a/a2/Kuda_BO3.png/revision/latest?cb=20151114232305',
                    ],
                ],
            ],
            [
                'name' => 'VMP',
                'tier_level' => 'L4',
                'score' => 88,
                'attack' => 81,
                'utility' => 80,
                'accuracy' => 68,
                'mobility' => 92,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'smg',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 4,
                        'damage_profile' => 'fast ttk smg',
                        'handling_profile' => 'entry weapon',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/VMP',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/2/28/VMP_BO3.png/revision/latest?cb=20151114232355',
                    ],
                ],
            ],
            [
                'name' => 'Weevil',
                'tier_level' => 'L34',
                'score' => 84,
                'attack' => 76,
                'utility' => 88,
                'accuracy' => 64,
                'mobility' => 79,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'app_scoring_note' => 'Simplified builder scores inferred from Black Ops III multiplayer weapon traits.',
                    'bo3' => [
                        'weapon_class' => 'smg',
                        'fire_mode' => 'full-auto',
                        'unlock_level' => 34,
                        'damage_profile' => 'large magazine',
                        'handling_profile' => 'sustained pressure',
                    ],
                    'source' => [
                        'page_url' => 'https://callofduty.fandom.com/wiki/Weevil',
                        'image_url' => 'https://static.wikia.nocookie.net/callofduty/images/7/7b/Weevil_BO3.png/revision/latest?cb=20151114232358',
                    ],
                ],
            ],
        ];

        return collect($items)->map(fn (array $item) => [
            'game_id' => $gameId,
            'name' => $item['name'],
            'slot' => 'weapon',
            'rarity' => 'standard-issue',
            'tier_level' => $item['tier_level'],
            'score' => $item['score'],
            'attack' => $item['attack'],
            'defense' => null,
            'utility' => $item['utility'],
            'accuracy' => $item['accuracy'],
            'mobility' => $item['mobility'],
            'metadata' => json_encode($item['metadata'], JSON_UNESCAPED_SLASHES),
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();
    }

    private function steelHorizonItems(int $gameId, object $now): array
    {
        $itemSlots = ['weapon', 'helmet', 'armor', 'gloves', 'boots'];
        $rarities = ['common', 'rare', 'epic', 'legendary'];
        $rows = [];

        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                'game_id' => $gameId,
                'name' => 'SH Item '.$i,
                'slot' => $itemSlots[$i % count($itemSlots)],
                'rarity' => $rarities[$i % count($rarities)],
                'tier_level' => 'T'.(($i % 5) + 1),
                'score' => 50 + ($i * 3),
                'attack' => ($i % 2 === 0) ? 10 + $i : null,
                'defense' => ($i % 3 === 0) ? 8 + $i : null,
                'utility' => ($i % 4 === 0) ? 5 + $i : null,
                'accuracy' => ($i % 5 === 0) ? 4 + $i : null,
                'mobility' => ($i % 2 !== 0) ? 6 + $i : null,
                'metadata' => json_encode([
                    'seed_origin' => 'synthetic-demo',
                    'game_family' => 'steel-horizon',
                    'index' => $i,
                ], JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $rows;
    }

    private function blackOpsThreeModifiers(int $gameId, object $now): array
    {
        $modifiers = [
            [
                'name' => 'Quickdraw Handle',
                'modifier_type' => 'rear_grip',
                'description' => 'Speeds up aim-down-sight transitions for snappier entries.',
                'delta_score' => 5,
                'delta_attack' => null,
                'delta_defense' => null,
                'delta_utility' => 1,
                'delta_accuracy' => 1,
                'delta_mobility' => 4,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Faster ADS speed.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Quickdraw_Handle'],
                ],
            ],
            [
                'name' => 'Grip',
                'modifier_type' => 'underbarrel',
                'description' => 'Helps keep recoil under control during sustained fire.',
                'delta_score' => 4,
                'delta_attack' => null,
                'delta_defense' => null,
                'delta_utility' => 1,
                'delta_accuracy' => 5,
                'delta_mobility' => null,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Reduced recoil while aiming.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Grip'],
                ],
            ],
            [
                'name' => 'Adjustable Stock',
                'modifier_type' => 'stock',
                'description' => 'Improves strafe speed while aiming around corners.',
                'delta_score' => 4,
                'delta_attack' => null,
                'delta_defense' => null,
                'delta_utility' => 2,
                'delta_accuracy' => null,
                'delta_mobility' => 4,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Move faster while aiming.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Adjustable_Stock'],
                ],
            ],
            [
                'name' => 'Laser Sight',
                'modifier_type' => 'laser',
                'description' => 'Tightens hip-fire spread for aggressive close-range fights.',
                'delta_score' => 3,
                'delta_attack' => null,
                'delta_defense' => null,
                'delta_utility' => 1,
                'delta_accuracy' => 3,
                'delta_mobility' => 2,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Improved hip-fire accuracy.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Laser_Sight'],
                ],
            ],
            [
                'name' => 'FMJ',
                'modifier_type' => 'ammunition',
                'description' => 'Boosts wallbang potential and rewards through-cover fights.',
                'delta_score' => 4,
                'delta_attack' => 3,
                'delta_defense' => null,
                'delta_utility' => 2,
                'delta_accuracy' => null,
                'delta_mobility' => null,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Higher bullet penetration.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/FMJ'],
                ],
            ],
            [
                'name' => 'Fast Mags',
                'modifier_type' => 'ammunition',
                'description' => 'Cuts reload downtime to keep pressure up between duels.',
                'delta_score' => 4,
                'delta_attack' => null,
                'delta_defense' => null,
                'delta_utility' => 4,
                'delta_accuracy' => null,
                'delta_mobility' => 1,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Faster reload speed.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Fast_Mag'],
                ],
            ],
            [
                'name' => 'Extended Mags',
                'modifier_type' => 'ammunition',
                'description' => 'Adds extra rounds for longer streak potential before reloading.',
                'delta_score' => 4,
                'delta_attack' => null,
                'delta_defense' => null,
                'delta_utility' => 5,
                'delta_accuracy' => null,
                'delta_mobility' => -1,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Larger magazine capacity.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Extended_Mags'],
                ],
            ],
            [
                'name' => 'Long Barrel',
                'modifier_type' => 'barrel',
                'description' => 'Extends effective range and keeps time-to-kill steadier at distance.',
                'delta_score' => 4,
                'delta_attack' => 2,
                'delta_defense' => null,
                'delta_utility' => 1,
                'delta_accuracy' => 3,
                'delta_mobility' => null,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Increased damage range.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Long_Barrel'],
                ],
            ],
            [
                'name' => 'Rapid Fire',
                'modifier_type' => 'mechanism',
                'description' => 'Raises fire rate at the cost of a little composure.',
                'delta_score' => 4,
                'delta_attack' => 4,
                'delta_defense' => null,
                'delta_utility' => null,
                'delta_accuracy' => -2,
                'delta_mobility' => null,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'Higher fire rate with a control trade-off.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/Rapid_Fire'],
                ],
            ],
            [
                'name' => 'High Caliber',
                'modifier_type' => 'ammunition',
                'description' => 'Rewards precise upper-chest and head shots with stronger damage.',
                'delta_score' => 5,
                'delta_attack' => 5,
                'delta_defense' => null,
                'delta_utility' => null,
                'delta_accuracy' => 1,
                'delta_mobility' => null,
                'metadata' => [
                    'seed_origin' => 'verified-bo3',
                    'bo3_effect' => 'More headshot damage.',
                    'source' => ['page_url' => 'https://callofduty.fandom.com/wiki/High_Caliber'],
                ],
            ],
        ];

        return collect($modifiers)->map(fn (array $modifier) => [
            'game_id' => $gameId,
            'name' => $modifier['name'],
            'modifier_type' => $modifier['modifier_type'],
            'description' => $modifier['description'],
            'delta_score' => $modifier['delta_score'],
            'delta_attack' => $modifier['delta_attack'],
            'delta_defense' => $modifier['delta_defense'],
            'delta_utility' => $modifier['delta_utility'],
            'delta_accuracy' => $modifier['delta_accuracy'],
            'delta_mobility' => $modifier['delta_mobility'],
            'metadata' => json_encode($modifier['metadata'], JSON_UNESCAPED_SLASHES),
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();
    }

    private function steelHorizonModifiers(int $gameId, object $now): array
    {
        $modifierTypes = ['offense', 'defense', 'mobility', 'utility'];
        $rows = [];

        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                'game_id' => $gameId,
                'name' => 'SH Mod '.$i,
                'modifier_type' => $modifierTypes[$i % count($modifierTypes)],
                'description' => 'Generated modifier '.$i.' for steel-horizon.',
                'delta_score' => ($i % 2 === 0) ? 4 : -1,
                'delta_attack' => ($i % 2 === 0) ? 2 : null,
                'delta_defense' => ($i % 3 === 0) ? 2 : null,
                'delta_utility' => ($i % 4 === 0) ? 1 : null,
                'delta_accuracy' => ($i % 5 === 0) ? 1 : null,
                'delta_mobility' => ($i % 2 !== 0) ? 2 : null,
                'metadata' => json_encode([
                    'seed_origin' => 'synthetic-demo',
                    'game_family' => 'steel-horizon',
                    'index' => $i,
                ], JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $rows;
    }

    private function blackOpsThreeItemTags(Collection $itemIds, Collection $tagIds): array
    {
        $mapping = [
            'KN-44' => ['bo3', 'assault-rifle', 'versatile'],
            'ICR-1' => ['bo3', 'assault-rifle', 'precision'],
            'XR-2' => ['bo3', 'assault-rifle', 'burst'],
            'HVK-30' => ['bo3', 'assault-rifle', 'aggressive'],
            'Man-O-War' => ['bo3', 'assault-rifle', 'aggressive'],
            'M8A7' => ['bo3', 'assault-rifle', 'burst'],
            'Sheiva' => ['bo3', 'assault-rifle', 'precision'],
            'Kuda' => ['bo3', 'smg', 'versatile'],
            'VMP' => ['bo3', 'smg', 'aggressive'],
            'Weevil' => ['bo3', 'smg', 'high-capacity'],
        ];

        $rows = [];
        foreach ($mapping as $itemName => $labels) {
            foreach ($labels as $label) {
                $rows[] = [
                    'equipment_item_id' => $itemIds[$itemName],
                    'tag_id' => $tagIds[$label],
                ];
            }
        }

        return $rows;
    }

    private function steelHorizonItemTags(int $gameId, Collection $tagIds): array
    {
        $itemIds = DB::table('equipment_items')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $baseLabels = ['prototype', 'shooter', 'precision', 'aggressive', 'versatile'];
        $rows = [];

        foreach ($itemIds as $index => $itemId) {
            $rows[] = [
                'equipment_item_id' => $itemId,
                'tag_id' => $tagIds[$baseLabels[$index % count($baseLabels)]],
            ];
            $rows[] = [
                'equipment_item_id' => $itemId,
                'tag_id' => $tagIds[$baseLabels[($index + 1) % count($baseLabels)]],
            ];
        }

        return $rows;
    }

    private function blackOpsThreeCompatibility(Collection $itemIds, Collection $modifierIds): array
    {
        $mapping = [
            'KN-44' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'Rapid Fire', 'High Caliber'],
            'ICR-1' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'Rapid Fire'],
            'XR-2' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'High Caliber'],
            'HVK-30' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'Laser Sight', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'Rapid Fire', 'High Caliber'],
            'Man-O-War' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'High Caliber'],
            'M8A7' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'High Caliber'],
            'Sheiva' => ['Quickdraw Handle', 'Grip', 'Adjustable Stock', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'High Caliber'],
            'Kuda' => ['Quickdraw Handle', 'Adjustable Stock', 'Laser Sight', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'Rapid Fire'],
            'VMP' => ['Quickdraw Handle', 'Adjustable Stock', 'Laser Sight', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'Rapid Fire'],
            'Weevil' => ['Quickdraw Handle', 'Adjustable Stock', 'Laser Sight', 'FMJ', 'Fast Mags', 'Extended Mags', 'Long Barrel', 'Rapid Fire'],
        ];

        $rows = [];
        foreach ($mapping as $itemName => $modifierNames) {
            foreach ($modifierNames as $modifierName) {
                $rows[] = [
                    'equipment_item_id' => $itemIds[$itemName],
                    'equipment_modifier_id' => $modifierIds[$modifierName],
                ];
            }
        }

        return $rows;
    }

    private function steelHorizonCompatibility(int $gameId): array
    {
        $itemIds = DB::table('equipment_items')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $modifierIds = DB::table('equipment_modifiers')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $rows = [];
        foreach ($itemIds as $index => $itemId) {
            $rows[] = [
                'equipment_item_id' => $itemId,
                'equipment_modifier_id' => $modifierIds[$index % $modifierIds->count()],
            ];
            $rows[] = [
                'equipment_item_id' => $itemId,
                'equipment_modifier_id' => $modifierIds[($index + 2) % $modifierIds->count()],
            ];
        }

        return $rows;
    }
}
