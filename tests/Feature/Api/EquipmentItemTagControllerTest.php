<?php

use App\Models\EquipmentItem;
use App\Models\Tag;
use Database\Seeders\WikiBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(WikiBuilderSeeder::class);
});

it('rejects attaching a category from a different game to an item', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();
    $foreignCategory = Tag::query()->where('game_id', 2)->firstOrFail();

    $response = $this->postJson(
        "/api/v1/items/{$item->id}/categories/{$foreignCategory->id}",
        [],
        [
            'API-KEY' => 'dev-admin-key-1234567890abcdef',
            'X-WIKI-ACTOR-ID' => '1',
        ]
    );

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Item and category must belong to the same game.');
});

it('allows attaching a category from the same game to an item', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();
    $category = Tag::query()->where('game_id', 1)->firstOrFail();

    $response = $this->postJson(
        "/api/v1/items/{$item->id}/categories/{$category->id}",
        [],
        [
            'API-KEY' => 'dev-admin-key-1234567890abcdef',
            'X-WIKI-ACTOR-ID' => '1',
        ]
    );

    $response->assertOk()
        ->assertJsonPath('success', true);
});
