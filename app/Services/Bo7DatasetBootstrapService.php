<?php

namespace App\Services;

use App\Services\Codmunity\CodmunityBo7Importer;
use Database\Seeders\Bo7OfflineSeeder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class Bo7DatasetBootstrapService
{
    private const GAME_SLUG = 'call-of-duty-black-ops-7';

    public function inspect(): array
    {
        $gameId = DB::table('games')
            ->where('slug', self::GAME_SLUG)
            ->value('id');

        if ($gameId === null) {
            return [
                'game_exists' => false,
                'game_id' => null,
                'dataset_flavor' => 'none',
                'items' => 0,
                'modifiers' => 0,
                'builds' => 0,
                'categories' => 0,
            ];
        }

        $gameId = (int) $gameId;
        $items = (int) DB::table('items')->where('game_id', $gameId)->count();

        return [
            'game_exists' => true,
            'game_id' => $gameId,
            'dataset_flavor' => $this->detectDatasetFlavor($gameId, $items),
            'items' => $items,
            'modifiers' => (int) DB::table('modifiers')->where('game_id', $gameId)->count(),
            'builds' => (int) DB::table('builds')->where('game_id', $gameId)->count(),
            'categories' => (int) DB::table('categories')->where('game_id', $gameId)->count(),
        ];
    }

    public function ensure(string $mode = 'offline', bool $force = false, ?string $source = null): array
    {
        $mode = $this->normalizeMode($mode);
        $source = $this->normalizeSource($source ?? (string) env('BOOTSTRAP_BO7_SOURCE', 'prefer-snapshot'));
        $before = $this->inspect();

        if ($mode === 'skip') {
            return [
                'action' => 'skipped',
                'reason' => 'mode_skip',
                'mode' => $mode,
                'source' => $source,
                'before' => $before,
                'after' => $before,
            ];
        }

        if (!$force && !$this->shouldBootstrap($mode, $before)) {
            return [
                'action' => 'skipped',
                'reason' => 'already_satisfied',
                'mode' => $mode,
                'source' => $source,
                'before' => $before,
                'after' => $before,
            ];
        }

        if ($mode === 'offline') {
            app(Bo7OfflineSeeder::class)->run();

            return [
                'action' => 'seeded_offline',
                'mode' => $mode,
                'source' => $source,
                'before' => $before,
                'after' => $this->inspect(),
            ];
        }

        try {
            $summary = app(CodmunityBo7Importer::class)->import(
                fresh: true,
                withBuilds: true,
                limit: null,
                source: $source,
            );

            return [
                'action' => 'imported_live',
                'mode' => $mode,
                'source' => $summary['source'] ?? $source,
                'before' => $before,
                'after' => $this->inspect(),
                'summary' => $summary,
            ];
        } catch (Throwable $exception) {
            if ($mode === 'live-strict') {
                throw $exception;
            }

            app(Bo7OfflineSeeder::class)->run();

            return [
                'action' => 'fallback_offline',
                'mode' => $mode,
                'source' => $source,
                'before' => $before,
                'after' => $this->inspect(),
                'error' => $exception->getMessage(),
            ];
        }
    }

    private function shouldBootstrap(string $mode, array $status): bool
    {
        if (($status['dataset_flavor'] ?? 'none') === 'none') {
            return true;
        }

        if ($mode === 'offline') {
            return false;
        }

        return ($status['dataset_flavor'] ?? 'unknown') === 'offline';
    }

    private function detectDatasetFlavor(int $gameId, int $itemCount): string
    {
        if ($itemCount === 0) {
            return 'none';
        }

        $seedOrigins = DB::table('items')
            ->where('game_id', $gameId)
            ->orderBy('id')
            ->limit(5)
            ->pluck('metadata')
            ->map(fn (mixed $metadata): ?string => $this->extractSeedOrigin($metadata))
            ->filter()
            ->unique()
            ->values();

        if ($seedOrigins->isEmpty()) {
            return 'unknown';
        }

        if ($seedOrigins->contains('codmunity-bo7')) {
            return 'live';
        }

        if ($seedOrigins->count() === 1 && $seedOrigins->first() === 'bo7-offline') {
            return 'offline';
        }

        return 'unknown';
    }

    private function extractSeedOrigin(mixed $metadata): ?string
    {
        if (is_array($metadata)) {
            return isset($metadata['seed_origin']) && is_string($metadata['seed_origin'])
                ? $metadata['seed_origin']
                : null;
        }

        if (!is_string($metadata) || trim($metadata) === '') {
            return null;
        }

        try {
            $decoded = json_decode($metadata, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return isset($decoded['seed_origin']) && is_string($decoded['seed_origin'])
            ? $decoded['seed_origin']
            : null;
    }

    private function normalizeMode(string $mode): string
    {
        $normalized = trim(strtolower($mode));

        if (!in_array($normalized, ['skip', 'offline', 'live', 'live-strict'], true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid BO7 bootstrap mode "%s". Expected skip, offline, live or live-strict.', $mode)
            );
        }

        return $normalized;
    }

    private function normalizeSource(string $source): string
    {
        $normalized = trim(strtolower($source));

        if (!in_array($normalized, ['live', 'snapshot', 'prefer-snapshot'], true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid BO7 import source "%s". Expected live, snapshot or prefer-snapshot.', $source)
            );
        }

        return $normalized;
    }
}
