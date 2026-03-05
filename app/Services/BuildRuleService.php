<?php

namespace App\Services;

use App\Models\BuildProfile;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use Illuminate\Support\Facades\DB;

class BuildRuleService
{
    /**
     * @param  array<int, array{equipment_item_id:int, position:int|null}>  $equipmentItems
     * @param  array<int, array{equipment_item_id:int, equipment_modifier_id:int}>  $equipmentModifiers
     * @return array<int, string>
     */
    public function validate(
        int $gameId,
        ?int $buildProfileId,
        array $equipmentItems,
        array $equipmentModifiers
    ): array {
        $errors = [];

        if ($buildProfileId !== null) {
            $profile = BuildProfile::query()->find($buildProfileId);
            if ($profile === null) {
                $errors[] = 'build_profile_id does not exist.';
            } elseif ((int) $profile->game_id !== $gameId) {
                $errors[] = 'build_profile must belong to the same game as the build.';
            }
        }

        $itemIds = collect($equipmentItems)
            ->pluck('equipment_item_id')
            ->merge(collect($equipmentModifiers)->pluck('equipment_item_id'))
            ->unique()
            ->values();

        if ($itemIds->isNotEmpty()) {
            $itemGameById = EquipmentItem::query()
                ->whereIn('id', $itemIds)
                ->pluck('game_id', 'id');

            foreach ($itemIds as $itemId) {
                if (! isset($itemGameById[$itemId])) {
                    $errors[] = "equipment_item_id {$itemId} does not exist.";
                    continue;
                }
                if ((int) $itemGameById[$itemId] !== $gameId) {
                    $errors[] = "equipment_item_id {$itemId} is not in the build game.";
                }
            }
        }

        $modifierIds = collect($equipmentModifiers)
            ->pluck('equipment_modifier_id')
            ->unique()
            ->values();

        if ($modifierIds->isNotEmpty()) {
            $modifierGameById = EquipmentModifier::query()
                ->whereIn('id', $modifierIds)
                ->pluck('game_id', 'id');

            foreach ($modifierIds as $modifierId) {
                if (! isset($modifierGameById[$modifierId])) {
                    $errors[] = "equipment_modifier_id {$modifierId} does not exist.";
                    continue;
                }
                if ((int) $modifierGameById[$modifierId] !== $gameId) {
                    $errors[] = "equipment_modifier_id {$modifierId} is not in the build game.";
                }
            }
        }

        $buildItemIdSet = collect($equipmentItems)->pluck('equipment_item_id')->flip();
        foreach ($equipmentModifiers as $row) {
            $itemId = (int) $row['equipment_item_id'];
            $modifierId = (int) $row['equipment_modifier_id'];

            if (! $buildItemIdSet->has($itemId)) {
                $errors[] = "modifier relation references equipment_item_id {$itemId} that is not in the build.";
            }

            $isCompatible = DB::table('equipment_item_compatible_modifiers')
                ->where('equipment_item_id', $itemId)
                ->where('equipment_modifier_id', $modifierId)
                ->exists();

            if (! $isCompatible) {
                $errors[] = "modifier {$modifierId} is not compatible with equipment item {$itemId}.";
            }
        }

        return array_values(array_unique($errors));
    }
}
