<?php

namespace App\Services;

use App\Models\Build;
use Illuminate\Support\Facades\DB;

class BuildSummaryService
{
    public function summary(Build $build): array
    {
        $itemTotals = DB::table('build_equipment_items as bei')
            ->join('equipment_items as ei', 'ei.id', '=', 'bei.equipment_item_id')
            ->where('bei.build_id', $build->id)
            ->selectRaw('
                COUNT(*) as items_count,
                COALESCE(SUM(ei.score), 0) as score,
                COALESCE(SUM(ei.attack), 0) as attack,
                COALESCE(SUM(ei.defense), 0) as defense,
                COALESCE(SUM(ei.utility), 0) as utility,
                COALESCE(SUM(ei.accuracy), 0) as accuracy,
                COALESCE(SUM(ei.mobility), 0) as mobility
            ')
            ->first();

        $modifierTotals = DB::table('build_equipment_modifiers as bem')
            ->join('equipment_modifiers as em', 'em.id', '=', 'bem.equipment_modifier_id')
            ->where('bem.build_id', $build->id)
            ->selectRaw('
                COUNT(*) as modifiers_count,
                COALESCE(SUM(em.delta_score), 0) as score,
                COALESCE(SUM(em.delta_attack), 0) as attack,
                COALESCE(SUM(em.delta_defense), 0) as defense,
                COALESCE(SUM(em.delta_utility), 0) as utility,
                COALESCE(SUM(em.delta_accuracy), 0) as accuracy,
                COALESCE(SUM(em.delta_mobility), 0) as mobility
            ')
            ->first();

        $totals = [
            'score' => (int) $itemTotals->score + (int) $modifierTotals->score,
            'attack' => (int) $itemTotals->attack + (int) $modifierTotals->attack,
            'defense' => (int) $itemTotals->defense + (int) $modifierTotals->defense,
            'utility' => (int) $itemTotals->utility + (int) $modifierTotals->utility,
            'accuracy' => (int) $itemTotals->accuracy + (int) $modifierTotals->accuracy,
            'mobility' => (int) $itemTotals->mobility + (int) $modifierTotals->mobility,
        ];

        return [
            'build_id' => $build->id,
            'items_count' => (int) $itemTotals->items_count,
            'modifiers_count' => (int) $modifierTotals->modifiers_count,
            'totals' => $totals,
        ];
    }

    public function compare(Build $left, Build $right): array
    {
        $leftSummary = $this->summary($left);
        $rightSummary = $this->summary($right);

        return [
            'left' => [
                'build_id' => $leftSummary['build_id'],
                'totals' => $leftSummary['totals'],
            ],
            'right' => [
                'build_id' => $rightSummary['build_id'],
                'totals' => $rightSummary['totals'],
            ],
            'diff' => [
                'score' => $leftSummary['totals']['score'] - $rightSummary['totals']['score'],
                'attack' => $leftSummary['totals']['attack'] - $rightSummary['totals']['attack'],
                'defense' => $leftSummary['totals']['defense'] - $rightSummary['totals']['defense'],
                'utility' => $leftSummary['totals']['utility'] - $rightSummary['totals']['utility'],
                'accuracy' => $leftSummary['totals']['accuracy'] - $rightSummary['totals']['accuracy'],
                'mobility' => $leftSummary['totals']['mobility'] - $rightSummary['totals']['mobility'],
            ],
        ];
    }
}
