<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategoryGameScopeBackfillService
{
    public function analyze(): array
    {
        $links = $this->linksNeedingBackfill();

        return [
            'global_categories' => DB::table('categories')->whereNull('game_id')->count(),
            'scoped_categories' => DB::table('categories')->whereNotNull('game_id')->count(),
            'links_to_rewire' => $links->count(),
            'game_category_pairs' => $links
                ->map(fn (object $row): string => $row->game_id.'|'.$row->slug)
                ->unique()
                ->count(),
        ];
    }

    public function backfill(): array
    {
        $links = $this->linksNeedingBackfill();
        $createdCategories = 0;
        $rewiredLinks = 0;

        if ($links->isNotEmpty()) {
            $categoryMap = [];

            foreach ($links->groupBy(fn (object $row): string => $row->game_id.'|'.$row->slug) as $group) {
                $sample = $group->first();
                $existingCategoryId = DB::table('categories')
                    ->where('game_id', $sample->game_id)
                    ->where('slug', $sample->slug)
                    ->value('id');

                if ($existingCategoryId === null) {
                    $existingCategoryId = DB::table('categories')->insertGetId([
                        'game_id' => $sample->game_id,
                        'label' => $sample->label,
                        'slug' => $sample->slug,
                        'source_type' => $sample->source_type ?? 'system',
                        'created_at' => $sample->created_at,
                        'updated_at' => $sample->updated_at,
                    ]);

                    $createdCategories += 1;
                }

                $categoryMap[$sample->old_category_id.'|'.$sample->game_id] = (int) $existingCategoryId;
            }

            $replacementRows = $links
                ->map(function (object $row) use ($categoryMap): ?array {
                    $newCategoryId = $categoryMap[$row->old_category_id.'|'.$row->game_id] ?? null;

                    if ($newCategoryId === null) {
                        return null;
                    }

                    return [
                        'item_id' => (int) $row->item_id,
                        'category_id' => $newCategoryId,
                    ];
                })
                ->filter()
                ->unique(fn (array $row): string => $row['item_id'].'|'.$row['category_id'])
                ->values()
                ->all();

            if ($replacementRows !== []) {
                DB::table('item_categories')->insertOrIgnore($replacementRows);
                $rewiredLinks = count($replacementRows);
            }

            DB::table('item_categories')
                ->whereIn('category_id', $links->pluck('old_category_id')->unique()->all())
                ->delete();
        }

        $deletedGlobalCategories = DB::table('categories')
            ->whereNull('game_id')
            ->delete();

        return [
            'created_categories' => $createdCategories,
            'rewired_links' => $rewiredLinks,
            'deleted_global_categories' => $deletedGlobalCategories,
            'remaining_global_categories' => (int) DB::table('categories')->whereNull('game_id')->count(),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, object{
     *     item_id:int,
     *     old_category_id:int,
     *     game_id:int,
     *     label:string,
     *     slug:string,
     *     source_type:?string,
     *     created_at:mixed,
     *     updated_at:mixed
     * }>
     */
    private function linksNeedingBackfill(): Collection
    {
        return DB::table('item_categories as ic')
            ->join('items as i', 'i.id', '=', 'ic.item_id')
            ->join('categories as c', 'c.id', '=', 'ic.category_id')
            ->whereNull('c.game_id')
            ->orderBy('c.id')
            ->orderBy('i.game_id')
            ->get([
                'ic.item_id',
                DB::raw('ic.category_id as old_category_id'),
                'i.game_id',
                'c.label',
                'c.slug',
                'c.source_type',
                'c.created_at',
                'c.updated_at',
            ]);
    }
}
