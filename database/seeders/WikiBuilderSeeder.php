<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WikiBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $this->truncateTables();

        DB::transaction(function () use ($now): void {
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
                    'password' => Hash::make('builder123'),
                    'role' => 'admin',
                    'first_name' => 'Ada',
                    'last_name' => 'Admin',
                    'display_name' => 'Ada Admin',
                    'phone' => '+33102030405',
                    'birth_date' => '1990-01-15',
                    'country_code' => 'FR',
                    'city' => 'Paris',
                    'postal_code' => '75001',
                    'address_line_1' => '1 Rue du Wiki',
                    'avatar_url' => 'https://example.com/avatars/admin.png',
                    'is_anonymized' => false,
                    'privacy_policy_accepted_at' => $now,
                    'privacy_policy_version' => '2026-04-08',
                    'last_login_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'username' => 'player1',
                    'email' => 'player1@wikibuilder.local',
                    'password' => Hash::make('builder123'),
                    'role' => 'user',
                    'first_name' => 'Noah',
                    'last_name' => 'Player',
                    'display_name' => 'Noah Player',
                    'phone' => '+33611112222',
                    'birth_date' => '1998-08-22',
                    'country_code' => 'FR',
                    'city' => 'Lyon',
                    'postal_code' => '69001',
                    'address_line_1' => '9 Avenue Builder',
                    'avatar_url' => 'https://example.com/avatars/player1.png',
                    'is_anonymized' => false,
                    'privacy_policy_accepted_at' => $now,
                    'privacy_policy_version' => '2026-04-08',
                    'marketing_consent_at' => $now,
                    'last_login_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            DB::table('api_keys')->insert([
                [
                    'user_id' => 1,
                    'key' => 'dev-admin-key-1234567890abcdef',
                    'label' => 'Admin Dev Key',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'user_id' => 2,
                    'key' => 'dev-player-key-1234567890abcdef',
                    'label' => 'Player Dev Key',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            DB::table('archetypes')->insert(array_merge(
                $this->blackOpsThreeProfiles($bo3GameId, $now),
                $this->steelHorizonProfiles($steelGameId, $now)
            ));

            DB::table('items')->insert(array_merge(
                $this->blackOpsThreeItems($bo3GameId, $now),
                $this->steelHorizonItems($steelGameId, $now)
            ));

            DB::table('modifiers')->insert(array_merge(
                $this->blackOpsThreeModifiers($bo3GameId, $now),
                $this->steelHorizonModifiers($steelGameId, $now)
            ));

            $bo3CategoryIds = $this->seedCategories($bo3GameId, [
                'bo3',
                'assault-rifle',
                'smg',
                'burst',
                'precision',
                'aggressive',
                'high-capacity',
                'versatile',
            ], $now);
            $steelCategoryIds = $this->seedCategories($steelGameId, [
                'prototype',
                'shooter',
                'precision',
                'aggressive',
                'versatile',
            ], $now);
            $itemIds = DB::table('items')->pluck('id', 'name');
            $modifierIds = DB::table('modifiers')->pluck('id', 'name');
            $users = DB::table('users')->pluck('id', 'username');
            $profiles = DB::table('archetypes')->pluck('id', 'name');

            DB::table('item_categories')->insertOrIgnore(array_merge(
                $this->blackOpsThreeItemTags($itemIds, $bo3CategoryIds),
                $this->steelHorizonItemTags($steelGameId, $steelCategoryIds)
            ));

            DB::table('item_modifiers')->insertOrIgnore(array_merge(
                $this->blackOpsThreeCompatibility($itemIds, $modifierIds),
                $this->steelHorizonCompatibility($steelGameId)
            ));

            DB::table('item_slots')->insert($this->itemSlotsFromCompatibilities($now));

            $itemSlotIds = DB::table('item_slots')
                ->get(['id', 'item_id', 'slot_key'])
                ->mapWithKeys(fn ($row) => [$row->item_id.'-'.$row->slot_key => $row->id]);

            DB::table('builds')->insert([
                [
                    'game_id' => $bo3GameId,
                    'user_id' => $users['player1'],
                    'archetype_id' => $profiles['BO3 Flex Rifle'],
                    'title' => 'BO3 Ranked Flex Core',
                    'description' => 'A compact BO3 sample build seeded with verified weapons and attachments.',
                    'share_token' => 'bo3-ranked-flex-core',
                    'is_public' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $steelGameId,
                    'user_id' => $users['admin'],
                    'archetype_id' => $profiles['Precision Sniper'],
                    'title' => 'Steel Horizon Sniper Setup',
                    'description' => 'Seeded sample build for compare endpoint.',
                    'share_token' => 'steel-horizon-sniper-setup',
                    'is_public' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            $buildIds = DB::table('builds')->pluck('id', 'title');

            DB::table('build_items')->insertOrIgnore([
                ['build_id' => $buildIds['BO3 Ranked Flex Core'], 'item_id' => $itemIds['KN-44'], 'position' => 1],
                ['build_id' => $buildIds['BO3 Ranked Flex Core'], 'item_id' => $itemIds['ICR-1'], 'position' => 2],
                ['build_id' => $buildIds['BO3 Ranked Flex Core'], 'item_id' => $itemIds['VMP'], 'position' => 3],
                ['build_id' => $buildIds['Steel Horizon Sniper Setup'], 'item_id' => $itemIds['SH Item 1'], 'position' => 1],
                ['build_id' => $buildIds['Steel Horizon Sniper Setup'], 'item_id' => $itemIds['SH Item 2'], 'position' => 2],
                ['build_id' => $buildIds['Steel Horizon Sniper Setup'], 'item_id' => $itemIds['SH Item 3'], 'position' => 3],
            ]);

            DB::table('build_slots')->insertOrIgnore([
                [
                    'build_id' => $buildIds['BO3 Ranked Flex Core'],
                    'item_id' => $itemIds['KN-44'],
                    'item_slot_id' => $itemSlotIds[$itemIds['KN-44'].'-rear_grip'],
                    'modifier_id' => $modifierIds['Quickdraw Handle'],
                ],
                [
                    'build_id' => $buildIds['BO3 Ranked Flex Core'],
                    'item_id' => $itemIds['ICR-1'],
                    'item_slot_id' => $itemSlotIds[$itemIds['ICR-1'].'-underbarrel'],
                    'modifier_id' => $modifierIds['Grip'],
                ],
                [
                    'build_id' => $buildIds['BO3 Ranked Flex Core'],
                    'item_id' => $itemIds['VMP'],
                    'item_slot_id' => $itemSlotIds[$itemIds['VMP'].'-mechanism'],
                    'modifier_id' => $modifierIds['Rapid Fire'],
                ],
                [
                    'build_id' => $buildIds['Steel Horizon Sniper Setup'],
                    'item_id' => $itemIds['SH Item 1'],
                    'item_slot_id' => $itemSlotIds[$itemIds['SH Item 1'].'-defense'],
                    'modifier_id' => $modifierIds['SH Mod 1'],
                ],
                [
                    'build_id' => $buildIds['Steel Horizon Sniper Setup'],
                    'item_id' => $itemIds['SH Item 2'],
                    'item_slot_id' => $itemSlotIds[$itemIds['SH Item 2'].'-mobility'],
                    'modifier_id' => $modifierIds['SH Mod 2'],
                ],
                [
                    'build_id' => $buildIds['Steel Horizon Sniper Setup'],
                    'item_id' => $itemIds['SH Item 3'],
                    'item_slot_id' => $itemSlotIds[$itemIds['SH Item 3'].'-utility'],
                    'modifier_id' => $modifierIds['SH Mod 3'],
                ],
            ]);
        });
    }

    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'game_draft_item_modifiers',
            'game_draft_item_categories',
            'game_draft_item_slots',
            'game_draft_modifiers',
            'game_draft_items',
            'game_draft_categories',
            'game_draft_archetypes',
            'game_draft_steps',
            'game_drafts',
            'api_keys',
            'build_slots',
            'build_items',
            'item_slots',
            'item_modifiers',
            'item_categories',
            'builds',
            'modifiers',
            'items',
            'archetypes',
            'categories',
            'users',
            'games',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
            'slug' => Str::slug($item['name']),
            'external_ref' => null,
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
                'slug' => 'sh-item-'.$i,
                'external_ref' => 'sh-item-'.$i,
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
            'slug' => Str::slug($modifier['name']),
            'external_ref' => null,
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
                'slug' => 'sh-mod-'.$i,
                'external_ref' => 'sh-mod-'.$i,
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

    private function seedCategories(int $gameId, array $labels, object $now): Collection
    {
        $rows = collect($labels)
            ->unique()
            ->map(fn (string $label) => [
                'game_id' => $gameId,
                'label' => $label,
                'slug' => Str::slug($label),
                'source_type' => 'system',
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        DB::table('categories')->upsert($rows, ['game_id', 'slug'], ['label', 'source_type', 'updated_at']);

        return DB::table('categories')
            ->where('game_id', $gameId)
            ->whereIn('label', $labels)
            ->pluck('id', 'label');
    }

    private function blackOpsThreeItemTags(Collection $itemIds, Collection $categoryIds): array
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
                    'item_id' => $itemIds[$itemName],
                    'category_id' => $categoryIds[$label],
                ];
            }
        }

        return $rows;
    }

    private function steelHorizonItemTags(int $gameId, Collection $categoryIds): array
    {
        $itemIds = DB::table('items')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $baseLabels = ['prototype', 'shooter', 'precision', 'aggressive', 'versatile'];
        $rows = [];

        foreach ($itemIds as $index => $itemId) {
            $rows[] = [
                'item_id' => $itemId,
                'category_id' => $categoryIds[$baseLabels[$index % count($baseLabels)]],
            ];
            $rows[] = [
                'item_id' => $itemId,
                'category_id' => $categoryIds[$baseLabels[($index + 1) % count($baseLabels)]],
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
                    'item_id' => $itemIds[$itemName],
                    'modifier_id' => $modifierIds[$modifierName],
                ];
            }
        }

        return $rows;
    }

    private function steelHorizonCompatibility(int $gameId): array
    {
        $itemIds = DB::table('items')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $modifierIds = DB::table('modifiers')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $rows = [];
        foreach ($itemIds as $index => $itemId) {
            $rows[] = [
                'item_id' => $itemId,
                'modifier_id' => $modifierIds[$index % $modifierIds->count()],
            ];
            $rows[] = [
                'item_id' => $itemId,
                'modifier_id' => $modifierIds[($index + 2) % $modifierIds->count()],
            ];
        }

        return $rows;
    }

    private function itemSlotsFromCompatibilities(object $now): array
    {
        return DB::table('item_modifiers as im')
            ->join('modifiers as m', 'm.id', '=', 'im.modifier_id')
            ->select('im.item_id', 'm.modifier_type')
            ->get()
            ->groupBy('item_id')
            ->flatMap(function (Collection $rows, int $itemId) use ($now) {
                return $rows
                    ->pluck('modifier_type')
                    ->unique()
                    ->values()
                    ->map(fn (string $modifierType, int $index) => [
                        'item_id' => $itemId,
                        'slot_key' => $modifierType,
                        'slot_label' => Str::headline(str_replace('_', ' ', $modifierType)),
                        'accepted_modifier_type' => $modifierType,
                        'sort_order' => $index + 1,
                        'is_required' => false,
                        'metadata' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
            })
            ->values()
            ->all();
    }
}
