<?php

namespace App\Services;

use App\Models\BuildProfile;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use Illuminate\Support\Facades\DB;

class BuildRuleService
{
    /**
     * @param  array<int, array{item_id:int, position:int|null}>  $items
     * @param  array<int, array{item_id:int, item_slot_id:int, modifier_id:int}>  $slots
     * @return array<int, string>
     */
    public function validate(
        int $gameId,
        ?int $archetypeId,
        array $items,
        array $slots
    ): array {
        $errors = [];

        if ($archetypeId !== null) {
            $archetype = BuildProfile::query()->find($archetypeId);

            if ($archetype === null) {
                $errors[] = 'archetype_id does not exist.';
            } elseif ((int) $archetype->game_id !== $gameId) {
                $errors[] = 'archetype must belong to the same game as the build.';
            }
        }

        $itemIds = collect($items)
            ->pluck('item_id')
            ->merge(collect($slots)->pluck('item_id'))
            ->unique()
            ->values();

        $itemGameById = EquipmentItem::query()
            ->whereIn('id', $itemIds)
            ->pluck('game_id', 'id');

        foreach ($itemIds as $itemId) {
            if (!isset($itemGameById[$itemId])) {
                $errors[] = "item_id {$itemId} does not exist.";
                continue;
            }

            if ((int) $itemGameById[$itemId] !== $gameId) {
                $errors[] = "item_id {$itemId} is not in the build game.";
            }
        }

        $modifierIds = collect($slots)->pluck('modifier_id')->unique()->values();
        $modifierGameById = EquipmentModifier::query()
            ->whereIn('id', $modifierIds)
            ->pluck('game_id', 'id');

        foreach ($modifierIds as $modifierId) {
            if (!isset($modifierGameById[$modifierId])) {
                $errors[] = "modifier_id {$modifierId} does not exist.";
                continue;
            }

            if ((int) $modifierGameById[$modifierId] !== $gameId) {
                $errors[] = "modifier_id {$modifierId} is not in the build game.";
            }
        }

        $buildItemIdSet = collect($items)->pluck('item_id')->flip();
        $slotIds = collect($slots)->pluck('item_slot_id')->unique()->values();

        $itemSlots = DB::table('item_slots')
            ->whereIn('id', $slotIds)
            ->get(['id', 'item_id', 'accepted_modifier_type'])
            ->keyBy('id');

        $modifierTypes = DB::table('modifiers')
            ->whereIn('id', $modifierIds)
            ->pluck('modifier_type', 'id');

        $slotOccupancySet = [];

        foreach ($slots as $row) {
            $itemId = (int) $row['item_id'];
            $itemSlotId = (int) $row['item_slot_id'];
            $modifierId = (int) $row['modifier_id'];
            $occupancyKey = $itemId.'-'.$itemSlotId;

            if (!$buildItemIdSet->has($itemId)) {
                $errors[] = "slot assignment references item_id {$itemId} that is not in the build.";
            }

            if (!isset($itemSlots[$itemSlotId])) {
                $errors[] = "item_slot_id {$itemSlotId} does not exist.";
                continue;
            }

            $slot = $itemSlots[$itemSlotId];
            if ((int) $slot->item_id !== $itemId) {
                $errors[] = "item_slot_id {$itemSlotId} does not belong to item_id {$itemId}.";
            }

            if (isset($slotOccupancySet[$occupancyKey])) {
                $errors[] = "item_slot_id {$itemSlotId} is assigned more than once for item_id {$itemId}.";
            }
            $slotOccupancySet[$occupancyKey] = true;

            $isCompatible = DB::table('item_modifiers')
                ->where('item_id', $itemId)
                ->where('modifier_id', $modifierId)
                ->exists();

            if (!$isCompatible) {
                $errors[] = "modifier {$modifierId} is not compatible with item {$itemId}.";
            }

            $acceptedModifierType = $slot->accepted_modifier_type;
            $modifierType = $modifierTypes[$modifierId] ?? null;

            if ($acceptedModifierType !== null && $modifierType !== $acceptedModifierType) {
                $errors[] = "modifier {$modifierId} does not match the accepted type for item_slot_id {$itemSlotId}.";
            }
        }

        return array_values(array_unique($errors));
    }
}
