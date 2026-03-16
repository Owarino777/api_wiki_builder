<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class Bo7OfflineSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::transaction(function () use ($now): void {
            DB::table('games')->upsert([
                [
                    'name' => 'Call of Duty: Black Ops 7',
                    'slug' => 'call-of-duty-black-ops-7',
                    'genre' => 'Arena Shooter',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['slug'], ['name', 'genre', 'updated_at']);

            $gameId = (int) DB::table('games')
                ->where('slug', 'call-of-duty-black-ops-7')
                ->value('id');

            $this->resetOfflineGameState($gameId);

            $categories = [
                ['label' => 'bo7', 'slug' => 'bo7'],
                ['label' => 'assault-rifle', 'slug' => 'assault-rifle'],
                ['label' => 'smg', 'slug' => 'smg'],
                ['label' => 'marksman-rifle', 'slug' => 'marksman-rifle'],
                ['label' => 'versatile', 'slug' => 'versatile'],
                ['label' => 'aggressive', 'slug' => 'aggressive'],
                ['label' => 'precision', 'slug' => 'precision'],
            ];

            DB::table('categories')->upsert(
                array_map(fn (array $category) => [
                    ...$category,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $categories),
                ['slug'],
                ['label', 'updated_at']
            );

            $weapons = [
                [
                    'archetype' => [
                        'name' => 'BO7 Versatile AR',
                        'description' => 'Offline BO7 archetype focused on a balanced assault rifle setup.',
                    ],
                    'item' => [
                        'name' => 'M15 Mod 0',
                        'slug' => 'm15-mod-0',
                        'external_ref' => 'weapon-1',
                        'slot' => 'weapon',
                        'rarity' => 'standard-issue',
                        'tier_level' => 'L0',
                        'score' => 83,
                        'attack' => 79,
                        'defense' => 54,
                        'utility' => 67,
                        'accuracy' => 77,
                        'mobility' => 64,
                        'metadata' => [
                            'seed_origin' => 'bo7-offline',
                            'bo7' => [
                                'category' => 'assault-rifle',
                                'playstyles' => ['Versatile'],
                            ],
                            'source' => [
                                'provider' => 'local-fixture',
                                'api_weapon_url' => 'https://api.codmunity.gg/website/assets/weapon/bo7/m15-mod-0',
                                'page_url' => 'https://codmunity.gg/weapon/bo7/m15-mod-0',
                                'image_url' => 'https://assets.codmunity.gg/optimized/M15-Mod-0-Green.webp',
                            ],
                        ],
                    ],
                    'categories' => ['bo7', 'assault-rifle', 'versatile'],
                    'modifiers' => [
                        [
                            'name' => 'Kepler Microflex',
                            'slug' => 'kepler-microflex',
                            'external_ref' => 'optic-1',
                            'modifier_type' => 'optic',
                            'slot_label' => 'Optic',
                            'description' => 'Clean BO7 optic with minimal handling penalty.',
                            'delta_score' => 1,
                            'delta_attack' => 0,
                            'delta_defense' => 0,
                            'delta_utility' => 1,
                            'delta_accuracy' => 2,
                            'delta_mobility' => 0,
                            'image_url' => 'https://assets.codmunity.gg/optimized/kepler-microflex.webp',
                        ],
                        [
                            'name' => 'Monolithic Suppressor',
                            'slug' => 'monolithic-suppressor',
                            'external_ref' => 'muzzle-1',
                            'modifier_type' => 'muzzle',
                            'slot_label' => 'Muzzle',
                            'description' => 'Suppressor with better effective range and velocity.',
                            'delta_score' => 2,
                            'delta_attack' => 1,
                            'delta_defense' => 0,
                            'delta_utility' => 1,
                            'delta_accuracy' => 1,
                            'delta_mobility' => -1,
                            'image_url' => 'https://assets.codmunity.gg/optimized/monolithic-suppressor.webp',
                        ],
                        [
                            'name' => 'Ranger Foregrip',
                            'slug' => 'ranger-foregrip',
                            'external_ref' => 'underbarrel-1',
                            'modifier_type' => 'underbarrel',
                            'slot_label' => 'Underbarrel',
                            'description' => 'Improves stability for longer fights.',
                            'delta_score' => 2,
                            'delta_attack' => 0,
                            'delta_defense' => 1,
                            'delta_utility' => 0,
                            'delta_accuracy' => 2,
                            'delta_mobility' => -1,
                            'image_url' => 'https://assets.codmunity.gg/optimized/ranger-foregrip.webp',
                        ],
                    ],
                    'build' => [
                        'title' => 'BO7 Offline Demo AR',
                        'description' => 'Offline BO7 assault rifle build available without CODMunity network access.',
                        'share_token' => 'bo7-offline-demo-ar',
                        'modifier_slugs' => ['kepler-microflex', 'monolithic-suppressor'],
                    ],
                ],
                [
                    'archetype' => [
                        'name' => 'BO7 Entry SMG',
                        'description' => 'Offline BO7 archetype focused on close-range, aggressive movement.',
                    ],
                    'item' => [
                        'name' => 'C9',
                        'slug' => 'c9',
                        'external_ref' => 'weapon-2',
                        'slot' => 'weapon',
                        'rarity' => 'standard-issue',
                        'tier_level' => 'L4',
                        'score' => 86,
                        'attack' => 74,
                        'defense' => 49,
                        'utility' => 72,
                        'accuracy' => 68,
                        'mobility' => 89,
                        'metadata' => [
                            'seed_origin' => 'bo7-offline',
                            'bo7' => [
                                'category' => 'smg',
                                'playstyles' => ['Aggressive'],
                            ],
                            'source' => [
                                'provider' => 'local-fixture',
                                'api_weapon_url' => 'https://api.codmunity.gg/website/assets/weapon/bo7/c9',
                                'page_url' => 'https://codmunity.gg/weapon/bo7/c9',
                                'image_url' => 'https://assets.codmunity.gg/optimized/C9.webp',
                            ],
                        ],
                    ],
                    'categories' => ['bo7', 'smg', 'aggressive'],
                    'modifiers' => [
                        [
                            'name' => 'Volzhskiy Reflex',
                            'slug' => 'volzhskiy-reflex',
                            'external_ref' => 'optic-2',
                            'modifier_type' => 'optic',
                            'slot_label' => 'Optic',
                            'description' => 'Compact optic for fast target acquisition.',
                            'delta_score' => 1,
                            'delta_attack' => 0,
                            'delta_defense' => 0,
                            'delta_utility' => 1,
                            'delta_accuracy' => 1,
                            'delta_mobility' => 0,
                            'image_url' => 'https://assets.codmunity.gg/optimized/volzhskiy-reflex.webp',
                        ],
                        [
                            'name' => 'Rapid Fire Springs',
                            'slug' => 'rapid-fire-springs',
                            'external_ref' => 'firemods-1',
                            'modifier_type' => 'fire_mods',
                            'slot_label' => 'Fire Mods',
                            'description' => 'Faster fire rate with a little control loss.',
                            'delta_score' => 2,
                            'delta_attack' => 2,
                            'delta_defense' => -1,
                            'delta_utility' => 0,
                            'delta_accuracy' => -1,
                            'delta_mobility' => 1,
                            'image_url' => 'https://assets.codmunity.gg/optimized/rapid-fire-springs.webp',
                        ],
                        [
                            'name' => 'Ergonomic Rear Grip',
                            'slug' => 'ergonomic-rear-grip',
                            'external_ref' => 'reargrip-1',
                            'modifier_type' => 'rear_grip',
                            'slot_label' => 'Rear Grip',
                            'description' => 'Improves sprint-to-fire and handling.',
                            'delta_score' => 2,
                            'delta_attack' => 0,
                            'delta_defense' => 0,
                            'delta_utility' => 1,
                            'delta_accuracy' => 0,
                            'delta_mobility' => 2,
                            'image_url' => 'https://assets.codmunity.gg/optimized/ergonomic-rear-grip.webp',
                        ],
                    ],
                    'build' => [
                        'title' => 'BO7 Offline Demo SMG',
                        'description' => 'Offline BO7 SMG build for fast routes and entry fights.',
                        'share_token' => 'bo7-offline-demo-smg',
                        'modifier_slugs' => ['rapid-fire-springs', 'ergonomic-rear-grip'],
                    ],
                ],
                [
                    'archetype' => [
                        'name' => 'BO7 Precision Marksman',
                        'description' => 'Offline BO7 archetype focused on precision and lane control.',
                    ],
                    'item' => [
                        'name' => 'SWAT 5.56',
                        'slug' => 'swat-5-56',
                        'external_ref' => 'weapon-3',
                        'slot' => 'weapon',
                        'rarity' => 'standard-issue',
                        'tier_level' => 'L9',
                        'score' => 85,
                        'attack' => 82,
                        'defense' => 58,
                        'utility' => 64,
                        'accuracy' => 86,
                        'mobility' => 57,
                        'metadata' => [
                            'seed_origin' => 'bo7-offline',
                            'bo7' => [
                                'category' => 'marksman-rifle',
                                'playstyles' => ['Precision'],
                            ],
                            'source' => [
                                'provider' => 'local-fixture',
                                'api_weapon_url' => 'https://api.codmunity.gg/website/assets/weapon/bo7/swat-5-56',
                                'page_url' => 'https://codmunity.gg/weapon/bo7/swat-5-56',
                                'image_url' => 'https://assets.codmunity.gg/optimized/SWAT-5-56.webp',
                            ],
                        ],
                    ],
                    'categories' => ['bo7', 'marksman-rifle', 'precision'],
                    'modifiers' => [
                        [
                            'name' => '4x Precision Scope',
                            'slug' => '4x-precision-scope',
                            'external_ref' => 'optic-3',
                            'modifier_type' => 'optic',
                            'slot_label' => 'Optic',
                            'description' => 'Long-range optic tuned for precise lane holding.',
                            'delta_score' => 2,
                            'delta_attack' => 1,
                            'delta_defense' => 0,
                            'delta_utility' => 0,
                            'delta_accuracy' => 3,
                            'delta_mobility' => -1,
                            'image_url' => 'https://assets.codmunity.gg/optimized/4x-precision-scope.webp',
                        ],
                        [
                            'name' => 'Reinforced Barrel',
                            'slug' => 'reinforced-barrel',
                            'external_ref' => 'barrel-1',
                            'modifier_type' => 'barrel',
                            'slot_label' => 'Barrel',
                            'description' => 'Extends range and bullet velocity.',
                            'delta_score' => 2,
                            'delta_attack' => 2,
                            'delta_defense' => 0,
                            'delta_utility' => 1,
                            'delta_accuracy' => 1,
                            'delta_mobility' => -1,
                            'image_url' => 'https://assets.codmunity.gg/optimized/reinforced-barrel.webp',
                        ],
                        [
                            'name' => 'Precision Stock',
                            'slug' => 'precision-stock',
                            'external_ref' => 'stock-1',
                            'modifier_type' => 'stock',
                            'slot_label' => 'Stock',
                            'description' => 'Stabilizes aim during long-range fights.',
                            'delta_score' => 1,
                            'delta_attack' => 0,
                            'delta_defense' => 1,
                            'delta_utility' => 0,
                            'delta_accuracy' => 2,
                            'delta_mobility' => 0,
                            'image_url' => 'https://assets.codmunity.gg/optimized/precision-stock.webp',
                        ],
                    ],
                    'build' => [
                        'title' => 'BO7 Offline Demo Precision',
                        'description' => 'Offline BO7 marksman build for controlled long-range fights.',
                        'share_token' => 'bo7-offline-demo-precision',
                        'modifier_slugs' => ['4x-precision-scope', 'reinforced-barrel'],
                    ],
                ],
            ];

            $this->validateFixtures($weapons);

            $adminUserId = DB::table('users')
                ->where('role', 'admin')
                ->value('id');

            $categoryIds = DB::table('categories')
                ->whereIn('slug', array_column($categories, 'slug'))
                ->pluck('id', 'slug');

            foreach ($weapons as $weapon) {
                DB::table('archetypes')->upsert([
                    [
                        'game_id' => $gameId,
                        'name' => $weapon['archetype']['name'],
                        'description' => $weapon['archetype']['description'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ], ['game_id', 'name'], ['description', 'updated_at']);

                DB::table('items')->upsert([
                    [
                        'game_id' => $gameId,
                        ...$weapon['item'],
                        'metadata' => json_encode($weapon['item']['metadata'], JSON_THROW_ON_ERROR),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ], ['game_id', 'slug'], [
                    'name',
                    'external_ref',
                    'slot',
                    'rarity',
                    'tier_level',
                    'score',
                    'attack',
                    'defense',
                    'utility',
                    'accuracy',
                    'mobility',
                    'metadata',
                    'updated_at',
                ]);

                $itemId = (int) DB::table('items')
                    ->where('game_id', $gameId)
                    ->where('slug', $weapon['item']['slug'])
                    ->value('id');

                foreach ($weapon['modifiers'] as $index => $modifier) {
                    DB::table('modifiers')->upsert([
                        [
                            'game_id' => $gameId,
                            'name' => $modifier['name'],
                            'slug' => $modifier['slug'],
                            'external_ref' => $modifier['external_ref'],
                            'modifier_type' => $modifier['modifier_type'],
                            'description' => $modifier['description'],
                            'delta_score' => $modifier['delta_score'],
                            'delta_attack' => $modifier['delta_attack'],
                            'delta_defense' => $modifier['delta_defense'],
                            'delta_utility' => $modifier['delta_utility'],
                            'delta_accuracy' => $modifier['delta_accuracy'],
                            'delta_mobility' => $modifier['delta_mobility'],
                            'metadata' => json_encode([
                                'seed_origin' => 'bo7-offline',
                                'codmunity' => [
                                    'image_url' => $modifier['image_url'],
                                ],
                            ], JSON_THROW_ON_ERROR),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ], ['game_id', 'slug'], [
                        'name',
                        'external_ref',
                        'modifier_type',
                        'description',
                        'delta_score',
                        'delta_attack',
                        'delta_defense',
                        'delta_utility',
                        'delta_accuracy',
                        'delta_mobility',
                        'metadata',
                        'updated_at',
                    ]);

                    DB::table('item_slots')->upsert([
                        [
                            'item_id' => $itemId,
                            'slot_key' => $modifier['modifier_type'],
                            'slot_label' => $modifier['slot_label'],
                            'accepted_modifier_type' => $modifier['modifier_type'],
                            'sort_order' => $index + 1,
                            'is_required' => false,
                            'metadata' => json_encode([
                                'seed_origin' => 'bo7-offline',
                                'offline_fixture' => true,
                            ], JSON_THROW_ON_ERROR),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ], ['item_id', 'slot_key'], [
                        'slot_label',
                        'accepted_modifier_type',
                        'sort_order',
                        'is_required',
                        'metadata',
                        'updated_at',
                    ]);
                }

                $modifierIds = DB::table('modifiers')
                    ->where('game_id', $gameId)
                    ->whereIn('slug', array_column($weapon['modifiers'], 'slug'))
                    ->pluck('id', 'slug');

                $itemCategoryRows = array_map(fn (string $slug) => [
                    'item_id' => $itemId,
                    'category_id' => $categoryIds[$slug],
                ], $weapon['categories']);

                DB::table('item_categories')->insertOrIgnore($itemCategoryRows);

                $itemModifierRows = array_map(fn (string $slug) => [
                    'item_id' => $itemId,
                    'modifier_id' => $modifierIds[$slug],
                ], array_column($weapon['modifiers'], 'slug'));

                DB::table('item_modifiers')->insertOrIgnore($itemModifierRows);

                $archetypeId = (int) DB::table('archetypes')
                    ->where('game_id', $gameId)
                    ->where('name', $weapon['archetype']['name'])
                    ->value('id');

                DB::table('builds')->upsert([
                    [
                        'game_id' => $gameId,
                        'user_id' => $adminUserId,
                        'archetype_id' => $archetypeId,
                        'title' => $weapon['build']['title'],
                        'description' => $weapon['build']['description'],
                        'share_token' => $weapon['build']['share_token'],
                        'is_public' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ], ['share_token'], [
                    'game_id',
                    'user_id',
                    'archetype_id',
                    'title',
                    'description',
                    'is_public',
                    'updated_at',
                ]);

                $buildId = (int) DB::table('builds')
                    ->where('share_token', $weapon['build']['share_token'])
                    ->value('id');

                DB::table('build_items')->insertOrIgnore([
                    'build_id' => $buildId,
                    'item_id' => $itemId,
                    'position' => 1,
                ]);

                $itemSlotIds = DB::table('item_slots')
                    ->where('item_id', $itemId)
                    ->pluck('id', 'slot_key');

                $buildSlotRows = [];

                foreach ($weapon['build']['modifier_slugs'] as $modifierSlug) {
                    $modifierId = $modifierIds[$modifierSlug];
                    $slotKey = DB::table('modifiers')
                        ->where('id', $modifierId)
                        ->value('modifier_type');

                    $buildSlotRows[] = [
                        'build_id' => $buildId,
                        'item_id' => $itemId,
                        'item_slot_id' => $itemSlotIds[$slotKey],
                        'modifier_id' => $modifierId,
                    ];
                }

                DB::table('build_slots')->insertOrIgnore($buildSlotRows);
            }

            $this->assertSeededDataset($gameId, count($weapons));
        });
    }

    private function resetOfflineGameState(int $gameId): void
    {
        DB::table('builds')->where('game_id', $gameId)->delete();
        DB::table('items')->where('game_id', $gameId)->delete();
        DB::table('modifiers')->where('game_id', $gameId)->delete();
        DB::table('archetypes')->where('game_id', $gameId)->delete();
    }

    private function validateFixtures(array $weapons): void
    {
        if (count($weapons) < 3) {
            throw new LogicException('BO7 offline seed must provide at least 3 weapons.');
        }

        foreach ($weapons as $weapon) {
            if (count($weapon['modifiers']) < 2) {
                throw new LogicException(
                    sprintf('BO7 offline seed weapon "%s" must provide at least 2 modifiers.', $weapon['item']['name'])
                );
            }
        }
    }

    private function assertSeededDataset(int $gameId, int $expectedWeaponCount): void
    {
        $actualWeaponCount = DB::table('items')
            ->where('game_id', $gameId)
            ->count();

        $actualBuildCount = DB::table('builds')
            ->where('game_id', $gameId)
            ->count();

        if ($actualWeaponCount !== $expectedWeaponCount) {
            throw new LogicException(sprintf(
                'BO7 offline seed expected %d weapons but found %d after seeding.',
                $expectedWeaponCount,
                $actualWeaponCount
            ));
        }

        if ($actualBuildCount !== $expectedWeaponCount) {
            throw new LogicException(sprintf(
                'BO7 offline seed expected %d builds but found %d after seeding.',
                $expectedWeaponCount,
                $actualBuildCount
            ));
        }
    }
}
