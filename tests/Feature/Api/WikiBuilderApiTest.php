<?php

use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use Database\Seeders\WikiBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(WikiBuilderSeeder::class);
});

it('returns a documented api root payload', function (): void {
    $response = $this->getJson('/api/v1');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Wiki Builder API root')
        ->assertJsonPath('data.name', 'Wiki Builder API')
        ->assertJsonPath('data.version', 'v1')
        ->assertJsonPath('data.public_routes.0.path', '/api/v1/games');
});

it('seeds call of duty black ops iii as the primary real game dataset', function (): void {
    $response = $this->getJson('/api/v1/games/1');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Call of Duty: Black Ops III')
        ->assertJsonPath('data.slug', 'call-of-duty-black-ops-iii');

    $itemResponse = $this->getJson('/api/v1/items/1');

    $itemResponse->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'KN-44')
        ->assertJsonPath('data.slug', 'kn-44')
        ->assertJsonPath('data.metadata.seed_origin', 'verified-bo3')
        ->assertJsonPath('data.metadata.source.page_url', 'https://callofduty.fandom.com/wiki/KN-44');
});

it('filters items by tier level', function (): void {
    $response = $this->getJson('/api/v1/items?tier_level=T1');

    $response->assertOk()
        ->assertJsonPath('success', true);

    $items = collect($response->json('data.data'));

    expect($items)->not->toBeEmpty();
    expect($items->every(fn (array $item): bool => $item['tier_level'] === 'T1'))->toBeTrue();
});

it('includes categories, item slots and compatible modifiers on item detail', function (): void {
    $response = $this->getJson('/api/v1/items/1');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'categories',
                'item_slots',
                'compatible_modifiers',
            ],
        ]);

    expect($response->json('data.item_slots'))->not->toBeEmpty();
    expect($response->json('data.compatible_modifiers'))->not->toBeEmpty();
});

it('includes associated items when listing categories', function (): void {
    $response = $this->getJson('/api/v1/categories');

    $response->assertOk()
        ->assertJsonPath('success', true);

    $categories = collect($response->json('data.data'));
    $categoryWithItems = $categories->first(fn (array $category): bool => !empty($category['items']));

    expect($categoryWithItems)->not->toBeNull();
    expect($categoryWithItems['items'][0])->toHaveKeys([
        'id',
        'game_id',
        'name',
        'slug',
        'slot',
        'rarity',
        'tier_level',
        'score',
    ]);
});

it('includes compatible items when showing a modifier', function (): void {
    $link = DB::table('item_modifiers')->first();

    $response = $this->getJson("/api/v1/modifiers/{$link->modifier_id}");

    $response->assertOk()
        ->assertJsonPath('success', true);

    $compatibleItems = collect($response->json('data.compatible_items'));

    expect($compatibleItems->pluck('id')->contains($link->item_id))->toBeTrue();
});

it('rejects build creation without api key', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();

    $response = $this->postJson('/api/v1/builds', [
        'game_id' => 1,
        'title' => 'No Key Build',
        'items' => [
            ['item_id' => $item->id, 'position' => 1],
        ],
    ]);

    $response->assertUnauthorized()
        ->assertJsonPath('success', false);
});

it('rejects admin routes without an admin user header', function (): void {
    $response = $this->postJson(
        '/api/v1/items',
        [
            'game_id' => 1,
            'name' => 'Admin Guard Item',
            'slug' => 'admin-guard-item',
            'slot' => 'helmet',
            'rarity' => 'rare',
            'score' => 55,
        ],
        ['API-KEY' => 'dev-wiki-builder-key-1234567890abcdef']
    );

    $response->assertForbidden()
        ->assertJsonPath('success', false);
});

it('rejects cross game items in a build', function (): void {
    $crossGameItem = EquipmentItem::query()->where('game_id', 2)->firstOrFail();

    $response = $this->postJson(
        '/api/v1/builds',
        [
            'game_id' => 1,
            'title' => 'Cross Game Build',
            'items' => [
                ['item_id' => $crossGameItem->id, 'position' => 1],
            ],
        ],
        ['API-KEY' => 'dev-wiki-builder-key-1234567890abcdef']
    );

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Business rule violation.');
});

it('rejects incompatible modifiers in a build', function (): void {
    $item = EquipmentItem::query()->where('name', 'KN-44')->firstOrFail();
    $modifier = EquipmentModifier::query()->where('name', 'Laser Sight')->firstOrFail();
    $itemSlotId = (int) DB::table('item_slots')
        ->where('item_id', $item->id)
        ->where('slot_key', 'rear_grip')
        ->value('id');

    $response = $this->postJson(
        '/api/v1/builds',
        [
            'game_id' => 1,
            'title' => 'Invalid Modifier Build',
            'items' => [
                ['item_id' => $item->id, 'position' => 1],
            ],
            'slots' => [
                [
                    'item_id' => $item->id,
                    'item_slot_id' => $itemSlotId,
                    'modifier_id' => $modifier->id,
                ],
            ],
        ],
        ['API-KEY' => 'dev-wiki-builder-key-1234567890abcdef']
    );

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Business rule violation.');
});

it('lists public builds filtered by game', function (): void {
    $response = $this->getJson('/api/v1/builds?game_id=1&is_public=true');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.title', 'BO3 Ranked Flex Core')
        ->assertJsonPath('data.data.0.game_id', 1)
        ->assertJsonPath('data.data.0.is_public', true);
});

it('returns the expected summary shape for a seeded build', function (): void {
    $response = $this->getJson('/api/v1/builds/1/summary');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'build_id',
                'items_count',
                'modifiers_count',
                'totals' => ['score', 'attack', 'defense', 'utility', 'accuracy', 'mobility'],
            ],
        ]);

    expect($response->json('data.items_count'))->toBe(3);
    expect($response->json('data.modifiers_count'))->toBe(3);
});

it('returns compare payload inside the standard api envelope for builds from the same game', function (): void {
    $bo3GameId = 1;
    $playerId = 2;
    $archetypeId = (int) DB::table('archetypes')->where('name', 'BO3 Entry SMG')->value('id');
    $itemId = (int) DB::table('items')->where('name', 'Kuda')->value('id');
    $modifierId = (int) DB::table('modifiers')->where('name', 'Quickdraw Handle')->value('id');
    $itemSlotId = (int) DB::table('item_slots')
        ->where('item_id', $itemId)
        ->where('slot_key', 'rear_grip')
        ->value('id');

    DB::table('builds')->insert([
        'id' => 3,
        'game_id' => $bo3GameId,
        'user_id' => $playerId,
        'archetype_id' => $archetypeId,
        'title' => 'BO3 Entry Test',
        'description' => 'Second BO3 build for compare.',
        'share_token' => 'bo3-entry-test',
        'is_public' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('build_items')->insert([
        'build_id' => 3,
        'item_id' => $itemId,
        'position' => 1,
    ]);

    DB::table('build_slots')->insert([
        'build_id' => 3,
        'item_id' => $itemId,
        'item_slot_id' => $itemSlotId,
        'modifier_id' => $modifierId,
    ]);

    $response = $this->postJson('/api/v1/builds/compare', [
        'left_build_id' => 1,
        'right_build_id' => 3,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'left' => ['build_id', 'totals'],
                'right' => ['build_id', 'totals'],
                'diff' => ['score', 'attack', 'defense', 'utility', 'accuracy', 'mobility'],
            ],
        ]);
});

it('rejects compare between builds from different games', function (): void {
    $response = $this->postJson('/api/v1/builds/compare', [
        'left_build_id' => 1,
        'right_build_id' => 2,
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Builds must belong to the same game to be compared.');
});

it('keeps not found responses uniform on summary endpoint', function (): void {
    $response = $this->getJson('/api/v1/builds/999/summary');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Build not found.');
});
