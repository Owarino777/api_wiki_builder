<?php

use App\Models\Build;
use App\Services\BuildSummaryService;
use Database\Seeders\WikiBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(WikiBuilderSeeder::class);
    $this->service = app(BuildSummaryService::class);
});

it('returns zero totals when a build has no items', function (): void {
    $build = Build::query()->create([
        'game_id' => 1,
        'user_id' => 2,
        'title' => 'Empty Build',
        'is_public' => false,
    ]);

    $summary = $this->service->summary($build);

    expect($summary['items_count'])->toBe(0);
    expect($summary['modifiers_count'])->toBe(0);
    expect($summary['totals']['score'])->toBe(0);
    expect($summary['totals']['attack'])->toBe(0);
});

it('returns valid summary shape for a seeded build', function (): void {
    $build = Build::query()->findOrFail(1);

    $summary = $this->service->summary($build);

    expect($summary)->toHaveKeys(['build_id', 'items_count', 'modifiers_count', 'totals']);
    expect($summary['totals'])->toHaveKeys(['score', 'attack', 'defense', 'utility', 'accuracy', 'mobility']);
    expect($summary['items_count'])->toBeGreaterThan(0);
});

it('compares two builds from the same game with correct diff', function (): void {
    $left = Build::query()->findOrFail(1);

    $right = Build::query()->create([
        'game_id' => 1,
        'user_id' => 2,
        'title' => 'Compare Target',
        'is_public' => false,
    ]);

    $result = $this->service->compare($left, $right);

    expect($result)->toHaveKeys(['left', 'right', 'diff']);
    expect($result['right']['totals']['score'])->toBe(0);
    expect($result['diff']['score'])->toBe($result['left']['totals']['score']);
});
