<?php

namespace App\Services;

use App\Models\Build;
use Illuminate\Support\Facades\DB;

class BuildSummaryService
{
    public function summary(Build $build): array
    {
        $itemTotals = DB::table('build_items as bi')
            ->join('items as i', 'i.id', '=', 'bi.item_id')
            ->where('bi.build_id', $build->id)
            ->selectRaw('
                COUNT(*) as items_count,
                COALESCE(SUM(i.score), 0) as score,
                COALESCE(SUM(i.attack), 0) as attack,
                COALESCE(SUM(i.defense), 0) as defense,
                COALESCE(SUM(i.utility), 0) as utility,
                COALESCE(SUM(i.accuracy), 0) as accuracy,
                COALESCE(SUM(i.mobility), 0) as mobility
            ')
            ->first();

        $modifierTotals = DB::table('build_slots as bs')
            ->join('modifiers as m', 'm.id', '=', 'bs.modifier_id')
            ->where('bs.build_id', $build->id)
            ->selectRaw('
                COUNT(*) as modifiers_count,
                COALESCE(SUM(m.delta_score), 0) as score,
                COALESCE(SUM(m.delta_attack), 0) as attack,
                COALESCE(SUM(m.delta_defense), 0) as defense,
                COALESCE(SUM(m.delta_utility), 0) as utility,
                COALESCE(SUM(m.delta_accuracy), 0) as accuracy,
                COALESCE(SUM(m.delta_mobility), 0) as mobility
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
