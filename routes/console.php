<?php

use App\Services\Bo7DatasetBootstrapService;
use App\Services\Bo7OperationLockService;
use App\Services\CategoryGameScopeBackfillService;
use App\Services\Codmunity\CodmunityBo7Importer;
use Database\Seeders\Bo7OfflineSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('codmunity:import-bo7 {--fresh : Reset imported BO7 game data before import} {--skip-builds : Import weapons and attachments only} {--limit= : Limit the number of weapons for a dry run} {--source=live : live, snapshot or prefer-snapshot} {--strict : Fail instead of falling back to the offline BO7 dataset when CODMunity is unavailable}', function () {
    try {
        return app(Bo7OperationLockService::class)->runExclusive(function () {
            try {
                $summary = app(CodmunityBo7Importer::class)->import(
                    fresh: (bool) $this->option('fresh'),
                    withBuilds: !$this->option('skip-builds'),
                    limit: $this->option('limit') !== null ? (int) $this->option('limit') : null,
                    source: (string) $this->option('source'),
                );

                $this->info('CODMunity BO7 import completed.');
                $this->line("Source: {$summary['source']}");
                $this->line("Game: {$summary['game']}");
                $this->line("Weapons: {$summary['weapons']}");
                $this->line("Modifiers: {$summary['modifiers']}");
                $this->line("Profiles: {$summary['profiles']}");
                $this->line("Builds: {$summary['builds']}");

                if (($summary['skipped_build_imports'] ?? 0) > 0) {
                    $this->warn("Skipped build imports: {$summary['skipped_build_imports']}");
                }

                return 0;
            } catch (\Throwable $exception) {
                if ((bool) $this->option('strict')) {
                    $this->error('CODMunity BO7 import failed.');
                    $this->error($exception->getMessage());

                    return 1;
                }

                app(Bo7OfflineSeeder::class)->run();

                $this->warn('CODMunity BO7 import failed. The offline BO7 dataset was loaded instead.');
                $this->line('Reason: '.$exception->getMessage());
                $this->line('Fallback dataset: 3 offline BO7 weapons, modifiers, slots and public demo builds.');

                return 0;
            }
        });
    } catch (\RuntimeException $exception) {
        $this->warn($exception->getMessage());

        return 1;
    }
})->purpose('Import Black Ops 7 weapons, attachments and public loadouts from CODMunity, with offline fallback by default');

Artisan::command('bo7:ensure-data {--mode=live : skip, offline, live or live-strict} {--source= : live, snapshot or prefer-snapshot} {--force : Refresh the BO7 dataset even if compatible data already exists}', function () {
    try {
        $result = app(Bo7OperationLockService::class)->runExclusive(fn () => app(Bo7DatasetBootstrapService::class)->ensure(
            mode: (string) $this->option('mode'),
            force: (bool) $this->option('force'),
            source: $this->option('source') !== null ? (string) $this->option('source') : null,
        ));
    } catch (\RuntimeException $exception) {
        $this->warn($exception->getMessage());

        return 1;
    }

    if ($result['action'] === 'skipped') {
        $this->info('BO7 dataset bootstrap skipped.');
        $this->line('Reason: '.$result['reason']);
    } elseif ($result['action'] === 'seeded_offline') {
        $this->info('BO7 offline dataset loaded.');
    } elseif ($result['action'] === 'imported_live') {
        $this->info('BO7 live dataset imported from CODMunity.');
    } elseif ($result['action'] === 'fallback_offline') {
        $this->warn('BO7 live import failed. Offline dataset loaded instead.');
        $this->line('Reason: '.$result['error']);
    }

    $after = $result['after'];
    $this->table(
        ['mode', 'source', 'dataset_flavor', 'items', 'modifiers', 'builds', 'categories'],
        [[
            $result['mode'],
            $result['source'],
            $after['dataset_flavor'],
            $after['items'],
            $after['modifiers'],
            $after['builds'],
            $after['categories'],
        ]]
    );

    return 0;
})->purpose('Ensure that BO7 data exists without overwriting a live import by default');

Artisan::command('categories:backfill-game-scope {--dry-run : Show the impact without writing changes}', function () {
    $service = app(CategoryGameScopeBackfillService::class);

    if ((bool) $this->option('dry-run')) {
        $summary = $service->analyze();

        $this->info('Category game-scope backfill dry run');
        $this->table(
            ['global_categories', 'scoped_categories', 'links_to_rewire', 'game_category_pairs'],
            [[$summary['global_categories'], $summary['scoped_categories'], $summary['links_to_rewire'], $summary['game_category_pairs']]]
        );

        return 0;
    }

    $summary = DB::transaction(fn () => $service->backfill());

    $this->info('Category game-scope backfill completed.');
    $this->table(
        ['created_categories', 'rewired_links', 'deleted_global_categories', 'remaining_global_categories'],
        [[$summary['created_categories'], $summary['rewired_links'], $summary['deleted_global_categories'], $summary['remaining_global_categories']]]
    );

    return 0;
})->purpose('Backfill global categories into per-game scoped categories');
