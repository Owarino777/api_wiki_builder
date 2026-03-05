<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WikiBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::transaction(function () use ($now): void {
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

            DB::table('games')->insert([
                [
                    'name' => 'Aetherfall',
                    'slug' => 'aetherfall',
                    'genre' => 'Action RPG',
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

            DB::table('build_profiles')->insert([
                [
                    'game_id' => $gameIds['aetherfall'],
                    'name' => 'Burst Assassin',
                    'description' => 'High damage loadout with mobility focus.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $gameIds['aetherfall'],
                    'name' => 'Tank Vanguard',
                    'description' => 'Defensive setup for frontline survivability.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $gameIds['aetherfall'],
                    'name' => 'Hybrid Scout',
                    'description' => 'Balanced setup with flexible utility.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $gameIds['steel-horizon'],
                    'name' => 'Precision Sniper',
                    'description' => 'Long-range profile with accuracy priority.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $gameIds['steel-horizon'],
                    'name' => 'Close Quarters',
                    'description' => 'Aggressive profile for short distance combat.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            $itemSlots = ['weapon', 'helmet', 'armor', 'gloves', 'boots'];
            $rarities = ['common', 'rare', 'epic', 'legendary'];

            $itemRows = [];
            foreach (['aetherfall', 'steel-horizon'] as $gameSlug) {
                for ($i = 1; $i <= 10; $i++) {
                    $itemRows[] = [
                        'game_id' => $gameIds[$gameSlug],
                        'name' => strtoupper(substr($gameSlug, 0, 2)).' Item '.$i,
                        'slot' => $itemSlots[$i % count($itemSlots)],
                        'rarity' => $rarities[$i % count($rarities)],
                        'tier_level' => 'T'.(($i % 5) + 1),
                        'score' => 50 + ($i * 3),
                        'attack' => ($i % 2 === 0) ? 10 + $i : null,
                        'defense' => ($i % 3 === 0) ? 8 + $i : null,
                        'utility' => ($i % 4 === 0) ? 5 + $i : null,
                        'accuracy' => ($i % 5 === 0) ? 4 + $i : null,
                        'mobility' => ($i % 2 !== 0) ? 6 + $i : null,
                        'metadata' => json_encode(['seed' => 'equipment_items', 'index' => $i]),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('equipment_items')->insert($itemRows);

            $modifierTypes = ['offense', 'defense', 'mobility', 'utility'];

            $modifierRows = [];
            foreach (['aetherfall', 'steel-horizon'] as $gameSlug) {
                for ($i = 1; $i <= 10; $i++) {
                    $modifierRows[] = [
                        'game_id' => $gameIds[$gameSlug],
                        'name' => strtoupper(substr($gameSlug, 0, 2)).' Mod '.$i,
                        'modifier_type' => $modifierTypes[$i % count($modifierTypes)],
                        'description' => 'Generated modifier '.$i.' for '.$gameSlug.'.',
                        'delta_score' => ($i % 2 === 0) ? 4 : -1,
                        'delta_attack' => ($i % 2 === 0) ? 2 : null,
                        'delta_defense' => ($i % 3 === 0) ? 2 : null,
                        'delta_utility' => ($i % 4 === 0) ? 1 : null,
                        'delta_accuracy' => ($i % 5 === 0) ? 1 : null,
                        'delta_mobility' => ($i % 2 !== 0) ? 2 : null,
                        'metadata' => json_encode(['seed' => 'equipment_modifiers', 'index' => $i]),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('equipment_modifiers')->insert($modifierRows);

            DB::table('tags')->insert([
                ['label' => 'pve', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'pvp', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'starter', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'endgame', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'tank', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'damage', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'support', 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'mobility', 'created_at' => $now, 'updated_at' => $now],
            ]);

            $tagIds = DB::table('tags')->pluck('id')->values();
            $items = DB::table('equipment_items')->select('id')->orderBy('id')->get();

            $itemTagRows = [];
            foreach ($items as $index => $item) {
                $firstTag = $tagIds[$index % $tagIds->count()];
                $secondTag = $tagIds[($index + 3) % $tagIds->count()];
                $itemTagRows[] = ['equipment_item_id' => $item->id, 'tag_id' => $firstTag];
                $itemTagRows[] = ['equipment_item_id' => $item->id, 'tag_id' => $secondTag];
            }
            DB::table('equipment_item_tags')->insertOrIgnore($itemTagRows);

            $compatibilityRows = [];
            foreach (['aetherfall', 'steel-horizon'] as $gameSlug) {
                $gameItems = DB::table('equipment_items')
                    ->where('game_id', $gameIds[$gameSlug])
                    ->orderBy('id')
                    ->pluck('id')
                    ->values();
                $gameModifiers = DB::table('equipment_modifiers')
                    ->where('game_id', $gameIds[$gameSlug])
                    ->orderBy('id')
                    ->pluck('id')
                    ->values();

                foreach ($gameItems as $idx => $itemId) {
                    $compatibilityRows[] = [
                        'equipment_item_id' => $itemId,
                        'equipment_modifier_id' => $gameModifiers[$idx % $gameModifiers->count()],
                    ];
                    $compatibilityRows[] = [
                        'equipment_item_id' => $itemId,
                        'equipment_modifier_id' => $gameModifiers[($idx + 3) % $gameModifiers->count()],
                    ];
                }
            }
            DB::table('equipment_item_compatible_modifiers')->insertOrIgnore($compatibilityRows);

            $users = DB::table('users')->pluck('id', 'username');
            $profiles = DB::table('build_profiles')->pluck('id', 'name');

            DB::table('builds')->insert([
                [
                    'game_id' => $gameIds['aetherfall'],
                    'user_id' => $users['player1'],
                    'build_profile_id' => $profiles['Burst Assassin'],
                    'title' => 'Aetherfall Burst Setup',
                    'description' => 'Seeded sample build for summary checks.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'game_id' => $gameIds['steel-horizon'],
                    'user_id' => $users['admin'],
                    'build_profile_id' => $profiles['Precision Sniper'],
                    'title' => 'Steel Horizon Sniper Setup',
                    'description' => 'Seeded sample build for compare endpoint.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            $builds = DB::table('builds')->orderBy('id')->get();
            $buildItemRows = [];
            $buildModifierRows = [];

            foreach ($builds as $build) {
                $buildItems = DB::table('equipment_items')
                    ->where('game_id', $build->game_id)
                    ->orderBy('id')
                    ->limit(3)
                    ->pluck('id')
                    ->values();

                foreach ($buildItems as $pos => $itemId) {
                    $buildItemRows[] = [
                        'build_id' => $build->id,
                        'equipment_item_id' => $itemId,
                        'position' => $pos + 1,
                    ];

                    $compatibleModifierId = DB::table('equipment_item_compatible_modifiers')
                        ->where('equipment_item_id', $itemId)
                        ->orderBy('equipment_modifier_id')
                        ->value('equipment_modifier_id');

                    if ($compatibleModifierId !== null) {
                        $buildModifierRows[] = [
                            'build_id' => $build->id,
                            'equipment_item_id' => $itemId,
                            'equipment_modifier_id' => $compatibleModifierId,
                        ];
                    }
                }
            }

            DB::table('build_equipment_items')->insertOrIgnore($buildItemRows);
            DB::table('build_equipment_modifiers')->insertOrIgnore($buildModifierRows);
        });
    }
}
