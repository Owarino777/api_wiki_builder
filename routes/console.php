<?php

use App\Services\Codmunity\CodmunityBo7Importer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('codmunity:import-bo7 {--fresh : Reset imported BO7 game data before import} {--skip-builds : Import weapons and attachments only} {--limit= : Limit the number of weapons for a dry run}', function () {
    $summary = app(CodmunityBo7Importer::class)->import(
        fresh: (bool) $this->option('fresh'),
        withBuilds: !$this->option('skip-builds'),
        limit: $this->option('limit') !== null ? (int) $this->option('limit') : null,
    );

    $this->info('CODMunity BO7 import completed.');
    $this->line("Game: {$summary['game']}");
    $this->line("Weapons: {$summary['weapons']}");
    $this->line("Modifiers: {$summary['modifiers']}");
    $this->line("Profiles: {$summary['profiles']}");
    $this->line("Builds: {$summary['builds']}");

    if (($summary['skipped_build_imports'] ?? 0) > 0) {
        $this->warn("Skipped build imports: {$summary['skipped_build_imports']}");
    }

    return 0;
})->purpose('Import Black Ops 7 weapons, attachments and public loadouts from CODMunity');
