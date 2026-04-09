<?php

use App\Models\BuildProfile;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use App\Services\BuildRuleService;
use Database\Seeders\WikiBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(WikiBuilderSeeder::class);
    $this->service = app(BuildRuleService::class);
});

it('passes validation for a valid same-game build', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();

    $errors = $this->service->validate(1, null, [
        ['item_id' => $item->id, 'position' => 1],
    ], []);

    expect($errors)->toBeEmpty();
});

it('rejects an archetype from a different game', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();
    $foreignArchetype = BuildProfile::query()->where('game_id', 2)->firstOrFail();

    $errors = $this->service->validate(1, $foreignArchetype->id, [
        ['item_id' => $item->id, 'position' => 1],
    ], []);

    expect($errors)->toContain('archetype must belong to the same game as the build.');
});

it('rejects a nonexistent archetype', function (): void {
    $item = EquipmentItem::query()->where('game_id', 1)->firstOrFail();

    $errors = $this->service->validate(1, 99999, [
        ['item_id' => $item->id, 'position' => 1],
    ], []);

    expect($errors)->toContain('archetype_id does not exist.');
});

it('rejects items from a different game', function (): void {
    $crossGameItem = EquipmentItem::query()->where('game_id', 2)->firstOrFail();

    $errors = $this->service->validate(1, null, [
        ['item_id' => $crossGameItem->id, 'position' => 1],
    ], []);

    expect($errors)->not->toBeEmpty();
    expect(collect($errors)->first(fn (string $e) => str_contains($e, 'is not in the build game')))->not->toBeNull();
});

it('rejects nonexistent items', function (): void {
    $errors = $this->service->validate(1, null, [
        ['item_id' => 99999, 'position' => 1],
    ], []);

    expect($errors)->toContain('item_id 99999 does not exist.');
});

it('rejects incompatible modifier for a slot assignment', function (): void {
    $item = EquipmentItem::query()->where('name', 'KN-44')->firstOrFail();
    $incompatibleModifier = EquipmentModifier::query()->where('name', 'Laser Sight')->firstOrFail();

    $slotId = (int) DB::table('item_slots')
        ->where('item_id', $item->id)
        ->where('slot_key', 'rear_grip')
        ->value('id');

    $errors = $this->service->validate(1, null, [
        ['item_id' => $item->id, 'position' => 1],
    ], [
        ['item_id' => $item->id, 'item_slot_id' => $slotId, 'modifier_id' => $incompatibleModifier->id],
    ]);

    expect($errors)->not->toBeEmpty();
});

it('detects duplicate slot assignments on the same item', function (): void {
    $item = EquipmentItem::query()->where('name', 'KN-44')->firstOrFail();
    $modifier = EquipmentModifier::query()->where('name', 'Quickdraw Handle')->firstOrFail();

    $slotId = (int) DB::table('item_slots')
        ->where('item_id', $item->id)
        ->where('slot_key', 'rear_grip')
        ->value('id');

    $errors = $this->service->validate(1, null, [
        ['item_id' => $item->id, 'position' => 1],
    ], [
        ['item_id' => $item->id, 'item_slot_id' => $slotId, 'modifier_id' => $modifier->id],
        ['item_id' => $item->id, 'item_slot_id' => $slotId, 'modifier_id' => $modifier->id],
    ]);

    expect(collect($errors)->first(fn (string $e) => str_contains($e, 'assigned more than once')))->not->toBeNull();
});
