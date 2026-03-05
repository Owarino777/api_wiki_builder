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

it('filters equipment items by tier level', function (): void {
    $response = $this->getJson('/api/v1/equipment-items?tier_level=T1');

    $response->assertOk()
        ->assertJsonPath('success', true);

    $items = collect($response->json('data.data'));

    expect($items)->not->toBeEmpty();
    expect($items->every(fn (array $item): bool => $item['tier_level'] === 'T1'))->toBeTrue();
});

it('includes associated equipment items when listing tags', function (): void {
    $response = $this->getJson('/api/v1/tags');

    $response->assertOk()
        ->assertJsonPath('success', true);

    $tags = collect($response->json('data.data'));
    $tagWithItems = $tags->first(fn (array $tag): bool => ! empty($tag['equipment_items']));

    expect($tagWithItems)->not->toBeNull();
    expect($tagWithItems['equipment_items'][0])->toHaveKeys([
        'id',
        'game_id',
        'name',
        'slot',
        'rarity',
        'tier_level',
        'score',
    ]);
});

it('includes compatible equipment items when showing a modifier', function (): void {
    $link = DB::table('equipment_item_compatible_modifiers')->first();

    $response = $this->getJson("/api/v1/equipment-modifiers/{$link->equipment_modifier_id}");

    $response->assertOk()
        ->assertJsonPath('success', true);

    $compatibleItems = collect($response->json('data.compatible_equipment_items'));

    expect($compatibleItems->pluck('id')->contains($link->equipment_item_id))->toBeTrue();
});

it('rejects build creation without api key', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();

    $response = $this->postJson('/api/v1/builds', [
        'game_id' => 1,
        'title' => 'No Key Build',
        'equipment_items' => [
            ['equipment_item_id' => $item->id, 'position' => 1],
        ],
    ]);

    $response->assertUnauthorized()
        ->assertJsonPath('success', false);
});

it('rejects admin routes without an admin user header', function (): void {
    $response = $this->postJson(
        '/api/v1/equipment-items',
        [
            'game_id' => 1,
            'name' => 'Admin Guard Item',
            'slot' => 'helmet',
            'rarity' => 'rare',
            'score' => 55,
        ],
        ['API-KEY' => 'dev-wiki-builder-key-1234567890abcdef']
    );

    $response->assertForbidden()
        ->assertJsonPath('success', false);
});

it('rejects cross game equipment items in a build', function (): void {
    $crossGameItem = EquipmentItem::query()->where('game_id', 2)->firstOrFail();

    $response = $this->postJson(
        '/api/v1/builds',
        [
            'game_id' => 1,
            'title' => 'Cross Game Build',
            'equipment_items' => [
                ['equipment_item_id' => $crossGameItem->id, 'position' => 1],
            ],
        ],
        ['API-KEY' => 'dev-wiki-builder-key-1234567890abcdef']
    );

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Business rule violation.');
});

it('rejects incompatible modifiers in a build', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();
    $modifier = EquipmentModifier::query()
        ->where('game_id', 1)
        ->whereNotIn('id', function ($query) use ($item): void {
            $query->select('equipment_modifier_id')
                ->from('equipment_item_compatible_modifiers')
                ->where('equipment_item_id', $item->id);
        })
        ->firstOrFail();

    $response = $this->postJson(
        '/api/v1/builds',
        [
            'game_id' => 1,
            'title' => 'Invalid Modifier Build',
            'equipment_items' => [
                ['equipment_item_id' => $item->id, 'position' => 1],
            ],
            'equipment_modifiers' => [
                [
                    'equipment_item_id' => $item->id,
                    'equipment_modifier_id' => $modifier->id,
                ],
            ],
        ],
        ['API-KEY' => 'dev-wiki-builder-key-1234567890abcdef']
    );

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Business rule violation.');
});

it('returns the expected summary shape for a seeded build', function (): void {
    $response = $this->getJson('/api/v1/builds/1/summary');

    $response->assertOk()
        ->assertJsonStructure([
            'build_id',
            'items_count',
            'modifiers_count',
            'totals' => ['score', 'attack', 'defense', 'utility', 'accuracy', 'mobility'],
        ]);

    expect($response->json('items_count'))->toBe(3);
    expect($response->json('modifiers_count'))->toBe(3);
});
