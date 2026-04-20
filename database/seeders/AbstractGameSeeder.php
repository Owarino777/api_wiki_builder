<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

abstract class AbstractGameSeeder extends Seeder
{
    /**
     * @return array{name: string, slug: string, genre: string, creation_category?: string}
     */
    abstract protected function gameDefinition(): array;

    /**
     * @return list<array{label: string, slug: string}>
     */
    abstract protected function categoryDefinitions(): array;

    /**
     * @return list<array<string, mixed>>
     */
    abstract protected function itemDefinitions(): array;

    public function run(): void
    {
        $gameSlug = $this->gameDefinition()['slug'];

        $existingGameId = DB::table('games')->where('slug', $gameSlug)->value('id');
        if ($existingGameId && DB::table('items')->where('game_id', $existingGameId)->count() > 0) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($now): void {
            $game = $this->gameDefinition();

            DB::table('games')->upsert([
                [
                    'name' => $game['name'],
                    'slug' => $game['slug'],
                    'genre' => $game['genre'],
                    'creation_category' => $game['creation_category'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['slug'], ['name', 'genre', 'creation_category', 'updated_at']);

            $gameId = (int) DB::table('games')
                ->where('slug', $game['slug'])
                ->value('id');

            $this->resetGameState($gameId);

            $categories = $this->categoryDefinitions();

            DB::table('categories')->upsert(
                array_map(fn (array $cat) => [
                    'game_id' => $gameId,
                    'label' => $cat['label'],
                    'slug' => $cat['slug'],
                    'source_type' => 'system',
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $categories),
                ['game_id', 'slug'],
                ['label', 'source_type', 'updated_at']
            );

            $items = $this->itemDefinitions();

            if (count($items) < 3) {
                throw new LogicException(static::class . ' must provide at least 3 items.');
            }

            $adminUserId = DB::table('users')
                ->where('role', 'admin')
                ->value('id');

            $categoryIds = DB::table('categories')
                ->where('game_id', $gameId)
                ->whereIn('slug', array_column($categories, 'slug'))
                ->pluck('id', 'slug');

            foreach ($items as $entry) {
                DB::table('archetypes')->upsert([
                    [
                        'game_id' => $gameId,
                        'name' => $entry['archetype']['name'],
                        'description' => $entry['archetype']['description'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ], ['game_id', 'name'], ['description', 'updated_at']);

                DB::table('items')->upsert([
                    [
                        'game_id' => $gameId,
                        ...$entry['item'],
                        'metadata' => json_encode($entry['item']['metadata'], JSON_THROW_ON_ERROR),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ], ['game_id', 'slug'], [
                    'name', 'external_ref', 'slot', 'rarity', 'tier_level',
                    'score', 'attack', 'defense', 'utility', 'accuracy', 'mobility',
                    'metadata', 'updated_at',
                ]);

                $itemId = (int) DB::table('items')
                    ->where('game_id', $gameId)
                    ->where('slug', $entry['item']['slug'])
                    ->value('id');

                foreach ($entry['modifiers'] as $index => $modifier) {
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
                                'seed_origin' => $this->gameDefinition()['slug'],
                            ], JSON_THROW_ON_ERROR),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ], ['game_id', 'slug'], [
                        'name', 'external_ref', 'modifier_type', 'description',
                        'delta_score', 'delta_attack', 'delta_defense',
                        'delta_utility', 'delta_accuracy', 'delta_mobility',
                        'metadata', 'updated_at',
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
                                'seed_origin' => $this->gameDefinition()['slug'],
                            ], JSON_THROW_ON_ERROR),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ], ['item_id', 'slot_key'], [
                        'slot_label', 'accepted_modifier_type', 'sort_order',
                        'is_required', 'metadata', 'updated_at',
                    ]);
                }

                $modifierIds = DB::table('modifiers')
                    ->where('game_id', $gameId)
                    ->whereIn('slug', array_column($entry['modifiers'], 'slug'))
                    ->pluck('id', 'slug');

                $itemCategoryRows = array_map(fn (string $slug) => [
                    'item_id' => $itemId,
                    'category_id' => $categoryIds[$slug],
                ], $entry['categories']);

                DB::table('item_categories')->insertOrIgnore($itemCategoryRows);

                $itemModifierRows = array_map(fn (string $slug) => [
                    'item_id' => $itemId,
                    'modifier_id' => $modifierIds[$slug],
                ], array_column($entry['modifiers'], 'slug'));

                DB::table('item_modifiers')->insertOrIgnore($itemModifierRows);

                $archetypeId = (int) DB::table('archetypes')
                    ->where('game_id', $gameId)
                    ->where('name', $entry['archetype']['name'])
                    ->value('id');

                DB::table('builds')->upsert([
                    [
                        'game_id' => $gameId,
                        'user_id' => $adminUserId,
                        'archetype_id' => $archetypeId,
                        'title' => $entry['build']['title'],
                        'description' => $entry['build']['description'],
                        'share_token' => $entry['build']['share_token'],
                        'is_public' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ], ['share_token'], [
                    'game_id', 'user_id', 'archetype_id', 'title',
                    'description', 'is_public', 'updated_at',
                ]);

                $buildId = (int) DB::table('builds')
                    ->where('share_token', $entry['build']['share_token'])
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

                foreach ($entry['build']['modifier_slugs'] as $modifierSlug) {
                    $modifierId = $modifierIds[$modifierSlug];
                    $slotKey = DB::table('modifiers')
                        ->where('id', $modifierId)
                        ->value('modifier_type');

                    if (isset($itemSlotIds[$slotKey])) {
                        $buildSlotRows[] = [
                            'build_id' => $buildId,
                            'item_id' => $itemId,
                            'item_slot_id' => $itemSlotIds[$slotKey],
                            'modifier_id' => $modifierId,
                        ];
                    }
                }

                DB::table('build_slots')->insertOrIgnore($buildSlotRows);
            }

            $this->assertSeededDataset($gameId, count($items));
        });
    }

    /**
     * Compact helper to define an item entry.
     *
     * @param array{0: int, 1: int, 2: int, 3: int, 4: int} $stats [attack, defense, utility, accuracy, mobility]
     * @param list<array<string, mixed>> $modifiers
     * @param list<string> $categories
     */
    protected function item(
        string $name,
        string $slot,
        string $archetypeName,
        string $archetypeDesc,
        int $score,
        array $stats,
        array $modifiers,
        array $categories,
        ?string $buildDesc = null,
        ?string $imageUrl = null,
    ): array {
        $slug = Str::slug($name);
        $gameSlug = $this->gameDefinition()['slug'];

        $metadata = ['seed_origin' => $gameSlug];
        if ($imageUrl) {
            $metadata['source'] = ['image_url' => $imageUrl];
        }

        return [
            'archetype' => [
                'name' => $archetypeName,
                'description' => $archetypeDesc,
            ],
            'item' => [
                'name' => $name,
                'slug' => $slug,
                'external_ref' => $slug,
                'slot' => $slot,
                'rarity' => 'standard-issue',
                'tier_level' => 'L0',
                'score' => $score,
                'attack' => $stats[0],
                'defense' => $stats[1],
                'utility' => $stats[2],
                'accuracy' => $stats[3],
                'mobility' => $stats[4],
                'metadata' => $metadata,
            ],
            'categories' => $categories,
            'modifiers' => $modifiers,
            'build' => [
                'title' => $name . ' Build',
                'description' => $buildDesc ?? "Recommended build for {$name}.",
                'share_token' => $gameSlug . '-' . $slug . '-build',
                'modifier_slugs' => array_slice(
                    array_map(fn (array $m) => $m['slug'], $modifiers),
                    0,
                    2
                ),
            ],
        ];
    }

    /**
     * Compact helper to define a modifier entry.
     *
     * @param array{0: int, 1: int, 2: int, 3: int, 4: int, 5: int} $d [score, attack, defense, utility, accuracy, mobility]
     */
    protected function mod(string $name, string $type, string $desc, array $d): array
    {
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'external_ref' => Str::slug($name),
            'modifier_type' => $type,
            'slot_label' => ucfirst(str_replace('_', ' ', $type)),
            'description' => $desc,
            'delta_score' => $d[0],
            'delta_attack' => $d[1],
            'delta_defense' => $d[2],
            'delta_utility' => $d[3],
            'delta_accuracy' => $d[4],
            'delta_mobility' => $d[5],
        ];
    }

    private function resetGameState(int $gameId): void
    {
        DB::table('builds')->where('game_id', $gameId)->delete();
        DB::table('items')->where('game_id', $gameId)->delete();
        DB::table('modifiers')->where('game_id', $gameId)->delete();
        DB::table('archetypes')->where('game_id', $gameId)->delete();
        DB::table('categories')->where('game_id', $gameId)->delete();
    }

    private function assertSeededDataset(int $gameId, int $expectedCount): void
    {
        $itemCount = DB::table('items')->where('game_id', $gameId)->count();
        $buildCount = DB::table('builds')->where('game_id', $gameId)->count();

        if ($itemCount !== $expectedCount) {
            throw new LogicException(sprintf(
                '%s: expected %d items but found %d.',
                static::class,
                $expectedCount,
                $itemCount
            ));
        }

        if ($buildCount !== $expectedCount) {
            throw new LogicException(sprintf(
                '%s: expected %d builds but found %d.',
                static::class,
                $expectedCount,
                $buildCount
            ));
        }
    }
}
