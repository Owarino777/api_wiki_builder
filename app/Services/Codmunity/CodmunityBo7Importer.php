<?php

namespace App\Services\Codmunity;

use App\Models\Build;
use App\Models\BuildProfile;
use App\Models\EquipmentItem;
use App\Models\EquipmentItemSlot;
use App\Models\EquipmentModifier;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;
use Throwable;

class CodmunityBo7Importer
{
    private const GAME_NAME = 'Call of Duty: Black Ops 7';
    private const GAME_SLUG = 'call-of-duty-black-ops-7';
    private const GAME_CODE = 'bo7';
    private const RARITY = 'standard-issue';

    public function import(bool $fresh = false, bool $withBuilds = true, ?int $limit = null): array
    {
        $meta = $this->getJson('https://api.codmunity.gg/website/assets/meta-ranking/one/bo7');
        $attachmentsPayload = $this->getJson('https://api.codmunity.gg/website/assets/attachments-data');
        $weaponStats = $this->getJson('https://api.codmunity.gg/weapon-stats');
        $attachmentStats = $this->getJson('https://api.codmunity.gg/attachment-stats');

        $weapons = collect($attachmentsPayload['weapons'] ?? [])
            ->filter(fn (array $weapon): bool => ($weapon['appGame'] ?? $weapon['game'] ?? null) === self::GAME_CODE)
            ->sortBy('WeaponName')
            ->values();

        if ($limit !== null) {
            $weapons = $weapons->take(max(1, $limit))->values();
        }

        $weaponPopularity = collect($meta['topWeapons'] ?? [])
            ->mapWithKeys(fn (array $row): array => [$row['slug'] => (float) ($row['popularity'] ?? 0)]);

        $bo7Attachments = collect($attachmentsPayload['attachments'] ?? [])
            ->filter(fn (array $attachment): bool => ($attachment['collec'] ?? null) === 'bo7-attachments')
            ->mapWithKeys(function (array $attachment): array {
                $attachmentId = $this->normalizeAttachmentId($attachment['_id'] ?? $attachment['ID'] ?? null);

                if ($attachmentId === null) {
                    return [];
                }

                return [$attachmentId => $attachment];
            });

        $weaponStatsByName = collect($weaponStats)
            ->filter(fn (array $row): bool => ($row['game'] ?? null) === self::GAME_CODE)
            ->keyBy(fn (array $row): string => $this->normalizeLookupKey($row['gun'] ?? ''));

        $attachmentStatsByKey = collect($attachmentStats)
            ->filter(fn (array $row): bool => ($row['game'] ?? null) === self::GAME_CODE)
            ->keyBy(fn (array $row): string => $this->attachmentStatsKey(
                $row['gun'] ?? '',
                $row['attachment'] ?? ''
            ));

        return DB::transaction(function () use (
            $fresh,
            $withBuilds,
            $weapons,
            $weaponPopularity,
            $bo7Attachments,
            $weaponStatsByName,
            $attachmentStatsByKey
        ): array {
            $game = Game::query()->updateOrCreate(
                ['slug' => self::GAME_SLUG],
                ['name' => self::GAME_NAME, 'genre' => 'Arena Shooter']
            );

            if ($fresh) {
                $this->clearGame($game->id);
            }

            $userId = DB::table('users')
                ->where('role', 'admin')
                ->value('id');

            $tagIds = [];
            $profileIds = [];
            $modifierCount = 0;
            $buildCount = 0;
            $skippedBuildImports = 0;

            foreach ($weapons as $weapon) {
                $weaponName = (string) ($weapon['WeaponName'] ?? $weapon['name'] ?? 'Unknown Weapon');
                $weaponSlug = (string) ($weapon['slug'] ?? Str::slug($weaponName));
                $weaponStatsRow = $weaponStatsByName->get($this->normalizeLookupKey($weaponName));
                $detail = $withBuilds
                    ? $this->safeGetJson("https://api.codmunity.gg/website/assets/weapon/bo7/{$weaponSlug}")
                    : null;
                $popularity = (float) $weaponPopularity->get($weaponSlug, 0);

                $playstyles = $this->collectPlaystyles($detail);
                $metrics = $this->mapWeaponMetrics($weapon, $weaponStatsRow, $popularity);

                $item = EquipmentItem::query()->updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'slug' => $weaponSlug,
                    ],
                    [
                        'name' => $weaponName,
                        'external_ref' => (string) ($weapon['_id'] ?? $weaponSlug),
                        'slot' => 'weapon',
                        'rarity' => self::RARITY,
                        'tier_level' => $this->formatTierLevel($weapon['Unlock'] ?? null),
                        'score' => $metrics['score'],
                        'attack' => $metrics['attack'],
                        'defense' => $metrics['defense'],
                        'utility' => $metrics['utility'],
                        'accuracy' => $metrics['accuracy'],
                        'mobility' => $metrics['mobility'],
                        'metadata' => [
                            'seed_origin' => 'codmunity-bo7',
                            'bo7' => [
                                'category' => $this->normalizeTagLabel($weapon['Category'] ?? 'weapon'),
                                'unlock_level' => $weapon['Unlock'] ?? null,
                                'description' => $weapon['Description'] ?? null,
                                'meta_popularity' => $popularity,
                                'share_number' => $weapon['ShareNumber'] ?? null,
                                'playstyles' => $playstyles->values()->all(),
                                'stats' => Arr::only($weaponStatsRow ?? [], [
                                    'avgTtk1',
                                    'avgTtk2',
                                    'avgTtk3',
                                    'avgTtk4',
                                    'ads',
                                    'stf',
                                    'reload',
                                    'bv',
                                    'rpm',
                                    'gun_kick',
                                    'horiz_recoil',
                                    'vert_recoil',
                                    'movement',
                                    'sprint',
                                    'ads_movement',
                                    'crouch_movement',
                                    'mag_size',
                                    'hitscan_range',
                                    'easeScore',
                                ]),
                            ],
                            'source' => [
                                'provider' => 'codmunity',
                                'api_weapon_url' => "https://api.codmunity.gg/website/assets/weapon/bo7/{$weaponSlug}",
                                'page_url' => "https://codmunity.gg/weapon/bo7/{$weaponSlug}",
                                'image_url' => $weapon['Icon'] ?? null,
                                'external_url' => $weapon['Link'] ?? null,
                            ],
                        ],
                    ]
                );

                $slotLabels = array_values(array_unique(array_merge(
                    $weapon['slots'] ?? [],
                    array_keys($weapon['attachments'] ?? [])
                )));

                $slotMap = $this->syncItemSlots($item, $slotLabels);

                $tagIdList = [
                    $this->firstOrCreateTagId($tagIds, 'bo7'),
                    $this->firstOrCreateTagId($tagIds, $this->normalizeTagLabel($weapon['Category'] ?? 'weapon')),
                ];

                foreach ($playstyles as $playstyle) {
                    $tagIdList[] = $this->firstOrCreateTagId($tagIds, $this->normalizeTagLabel($playstyle));
                }

                $item->categories()->syncWithoutDetaching(array_values(array_unique($tagIdList)));

                $modifierIds = [];
                $modifierLookup = [];

                foreach (($weapon['attachments'] ?? []) as $slotLabel => $config) {
                    $slotKey = $this->normalizeSlotKey($slotLabel);
                    $attachmentIds = collect($config['list'] ?? [])
                        ->map(fn (mixed $value): ?string => $this->normalizeAttachmentId($value))
                        ->filter()
                        ->values();

                    foreach ($attachmentIds as $attachmentId) {
                        $attachment = $bo7Attachments->get($attachmentId);

                        if (!$attachment) {
                            continue;
                        }

                        $modifier = $this->upsertModifier(
                            $game->id,
                            $weapon,
                            $attachment,
                            $slotKey,
                            $attachmentStatsByKey->get($this->attachmentStatsKey(
                                $weaponName,
                                (string) ($attachment['Attachmentname'] ?? '')
                            ))
                        );

                        $modifierIds[] = $modifier->id;
                        $modifierCount += 1;

                        foreach ($this->modifierAliases($modifier) as $alias) {
                            $modifierLookup[$alias] = $modifier->id;
                        }
                    }
                }

                $item->compatibleModifiers()->sync(array_values(array_unique($modifierIds)));

                if ($withBuilds && is_array($detail)) {
                    try {
                        $buildCount += $this->importBuildsForItem(
                            $game->id,
                            $item,
                            $detail,
                            $slotMap,
                            $modifierLookup,
                            $profileIds,
                            $userId ? (int) $userId : null
                        );
                    } catch (Throwable) {
                        $skippedBuildImports += 1;
                    }
                }
            }

            return [
                'game' => $game->name,
                'weapons' => $weapons->count(),
                'modifiers' => EquipmentModifier::query()->where('game_id', $game->id)->count(),
                'builds' => $withBuilds ? Build::query()->where('game_id', $game->id)->count() : 0,
                'profiles' => BuildProfile::query()->where('game_id', $game->id)->count(),
                'categories' => Tag::query()->count(),
                'processed_modifier_links' => $modifierCount,
                'processed_build_links' => $buildCount,
                'skipped_build_imports' => $skippedBuildImports,
            ];
        });
    }

    private function clearGame(int $gameId): void
    {
        Build::query()->where('game_id', $gameId)->delete();
        EquipmentItem::query()->where('game_id', $gameId)->delete();
        EquipmentModifier::query()->where('game_id', $gameId)->delete();
        BuildProfile::query()->where('game_id', $gameId)->delete();
    }

    private function getJson(string $url): array
    {
        $response = $this->httpClient()
            ->timeout(30)
            ->connectTimeout(10)
            ->retry(3, 300)
            ->get($url)
            ->throw();

        return $response->json();
    }

    private function httpClient(): PendingRequest
    {
        $curlOptions = [];

        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            $curlOptions[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }

        if (defined('CURLOPT_HTTP_VERSION') && defined('CURL_HTTP_VERSION_1_1')) {
            $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        }

        return Http::acceptJson()
            ->withHeaders([
                'User-Agent' => 'api_wiki_builder/bo7-importer',
            ])
            ->withOptions([
                'verify' => true,
                'curl' => $curlOptions,
            ]);
    }

    private function safeGetJson(string $url): ?array
    {
        try {
            return $this->getJson($url);
        } catch (Throwable) {
            return null;
        }
    }

    private function collectPlaystyles(?array $detail): Collection
    {
        if (!$detail) {
            return collect();
        }

        $metaPlaystyles = collect($detail['loadouts']['bo7-mp'] ?? [])
            ->pluck('Playstyle');

        $creatorPlaystyles = collect($detail['creatorLoadouts']['bo7'] ?? [])
            ->pluck('playstyle');

        return $metaPlaystyles
            ->merge($creatorPlaystyles)
            ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->unique()
            ->values();
    }

    private function syncItemSlots(EquipmentItem $item, array $slotLabels): array
    {
        $slotMap = [];

        foreach (array_values($slotLabels) as $index => $slotLabel) {
            $slot = EquipmentItemSlot::query()->updateOrCreate(
                [
                    'item_id' => $item->id,
                    'slot_key' => $this->normalizeSlotKey($slotLabel),
                ],
                [
                    'slot_label' => (string) $slotLabel,
                    'accepted_modifier_type' => $this->normalizeSlotKey($slotLabel),
                    'sort_order' => $index + 1,
                    'is_required' => false,
                    'metadata' => [
                        'seed_origin' => 'codmunity-bo7',
                        'source_slot_label' => $slotLabel,
                    ],
                ]
            );

            $slotMap[$slot->slot_label] = (int) $slot->id;
            $slotMap[$slot->slot_key] = (int) $slot->id;
        }

        return $slotMap;
    }

    private function firstOrCreateTagId(array &$cache, string $label): int
    {
        $slug = $this->normalizeTagSlug($label);

        if (isset($cache[$slug])) {
            return $cache[$slug];
        }

        $tag = Tag::query()->firstOrCreate(
            ['slug' => $slug],
            ['label' => $label]
        );

        return $cache[$slug] = (int) $tag->id;
    }

    private function upsertModifier(
        int $gameId,
        array $weapon,
        array $attachment,
        string $slotKey,
        ?array $attachmentStats
    ): EquipmentModifier {
        $weaponSlug = (string) ($weapon['slug'] ?? Str::slug($weapon['WeaponName'] ?? 'weapon'));
        $attachmentId = $this->normalizeAttachmentId($attachment['_id'] ?? $attachment['ID'] ?? null)
            ?? Str::slug($attachment['Attachmentname'] ?? 'attachment');
        $attachmentName = (string) ($attachment['Attachmentname'] ?? 'Attachment');
        $displayName = (string) ($attachment['LabelMP'] ?? $attachment['Label'] ?? $attachmentName);
        $deltas = $this->mapAttachmentDeltas($attachmentStats);

        return EquipmentModifier::query()->updateOrCreate(
            [
                'game_id' => $gameId,
                'slug' => $this->trimmedSlug($weaponSlug.'-'.$attachmentName.'-'.$attachmentId, 160, $attachmentId),
            ],
            [
                'name' => $displayName,
                'external_ref' => $weaponSlug.':'.$attachmentId,
                'modifier_type' => $slotKey,
                'description' => $this->buildModifierDescription($weapon['WeaponName'] ?? 'Weapon', $attachmentName, $attachmentStats),
                'delta_score' => $deltas['score'],
                'delta_attack' => $deltas['attack'],
                'delta_defense' => $deltas['defense'],
                'delta_utility' => $deltas['utility'],
                'delta_accuracy' => $deltas['accuracy'],
                'delta_mobility' => $deltas['mobility'],
                'metadata' => [
                    'seed_origin' => 'codmunity-bo7',
                    'codmunity' => [
                        'weapon' => $weapon['WeaponName'] ?? null,
                        'attachment_name' => $attachmentName,
                        'attachment_label' => $attachment['Label'] ?? null,
                        'attachment_label_mp' => $attachment['LabelMP'] ?? null,
                        'slot' => $attachment['Slot'] ?? null,
                        'unlock_level' => $attachment['Unlock Level'] ?? null,
                        'image_url' => $attachment['Icon'] ?? null,
                        'stats' => $attachmentStats ? Arr::only($attachmentStats, [
                            'ads_mod',
                            'ads_movement_mod',
                            'bv_mod',
                            'crouch_movement_mod',
                            'flinch_resistance_mod',
                            'gun_kick_mod',
                            'hipfire_max_mod',
                            'hipfire_min_mod',
                            'horiz_recoil_mod',
                            'mag_size_mod',
                            'movement_mod',
                            'range_mod',
                            'reload_mod',
                            'rpm_mod',
                            'sprint_mod',
                            'stf_mod',
                            'swap_speed_mod',
                            'target_flinch_mod',
                            'vert_recoil_mod',
                        ]) : [],
                    ],
                ],
            ]
        );
    }

    private function modifierAliases(EquipmentModifier $modifier): array
    {
        $metadata = $modifier->metadata['codmunity'] ?? [];

        return collect([
            $modifier->name,
            $metadata['attachment_name'] ?? null,
            $metadata['attachment_label'] ?? null,
            $metadata['attachment_label_mp'] ?? null,
        ])->filter()
            ->map(fn (string $value): string => $this->normalizeLookupKey($value))
            ->unique()
            ->values()
            ->all();
    }

    private function importBuildsForItem(
        int $gameId,
        EquipmentItem $item,
        array $detail,
        array $slotMap,
        array $modifierLookup,
        array &$profileIds,
        ?int $userId
    ): int {
        $count = 0;
        $loadouts = collect($detail['loadouts']['bo7-mp'] ?? [])
            ->filter(fn (array $row): bool => (bool) ($row['active'] ?? true));

        foreach ($loadouts->values() as $index => $loadout) {
            $playstyle = trim((string) ($loadout['Playstyle'] ?? 'Versatile'));
            $profileId = $this->firstOrCreateProfileId($profileIds, $gameId, $playstyle);
            $shareToken = $this->buildShareToken($item->slug, $loadout['LoadoutCode'] ?? null, $index + 1);

            $build = Build::query()->updateOrCreate(
                ['share_token' => $shareToken],
                [
                    'game_id' => $gameId,
                    'user_id' => $userId,
                    'archetype_id' => $profileId,
                    'title' => $this->buildTitle($item->name, $playstyle, $index + 1),
                    'description' => $loadout['Description'] ?? "Imported BO7 {$playstyle} loadout from CODMunity.",
                    'is_public' => true,
                ]
            );

            $build->items()->sync([
                $item->id => ['position' => 1],
            ]);

            DB::table('build_slots')
                ->where('build_id', $build->id)
                ->where('item_id', $item->id)
                ->delete();

            $rows = [];

            foreach ($slotMap as $slotLabel => $itemSlotId) {
                if (is_numeric($slotLabel)) {
                    continue;
                }

                if (!array_key_exists($slotLabel, $loadout)) {
                    continue;
                }

                $attachmentName = $loadout[$slotLabel];

                if (!$attachmentName || $attachmentName === 0) {
                    continue;
                }

                $modifierId = $modifierLookup[$this->normalizeLookupKey((string) $attachmentName)] ?? null;

                if (!$modifierId) {
                    continue;
                }

                $rows[] = [
                    'build_id' => $build->id,
                    'item_id' => $item->id,
                    'item_slot_id' => $itemSlotId,
                    'modifier_id' => $modifierId,
                ];
            }

            if ($rows !== []) {
                DB::table('build_slots')->insert(array_values(
                    collect($rows)
                        ->unique(fn (array $row): string => $row['item_slot_id'].'|'.$row['modifier_id'])
                        ->all()
                ));
            }

            $count += 1;
        }

        return $count;
    }

    private function firstOrCreateProfileId(array &$cache, int $gameId, string $name): int
    {
        $key = $gameId.'|'.$this->normalizeLookupKey($name);

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $profile = BuildProfile::query()->firstOrCreate(
            [
                'game_id' => $gameId,
                'name' => $name,
            ],
            [
                'description' => "Imported BO7 {$name} archetype from CODMunity.",
            ]
        );

        return $cache[$key] = (int) $profile->id;
    }

    private function mapWeaponMetrics(array $weapon, ?array $stats, float $popularity): array
    {
        if (!$stats) {
            $fallback = 55 + min(15, (int) round($popularity));

            return [
                'score' => $this->clamp($fallback, 25, 99),
                'attack' => 55,
                'defense' => 50,
                'utility' => 50,
                'accuracy' => 52,
                'mobility' => 54,
            ];
        }

        $attack = $this->clamp((int) round(
            28
            + max(0, 450 - (float) ($stats['avgTtk1'] ?? 450)) / 5
            + min(60, (float) ($stats['hitscan_range'] ?? 0)) * 0.35
            + min(900, (float) ($stats['rpm'] ?? 0)) * 0.01
        ), 20, 99);

        $defense = $this->clamp((int) round(
            28
            + max(0, 28 - (float) ($stats['gun_kick'] ?? 28)) * 1.1
            + max(0, 18 - (float) ($stats['horiz_recoil'] ?? 18)) * 1.4
            + max(0, 55 - (float) ($stats['vert_recoil'] ?? 55)) * 0.45
        ), 20, 99);

        $utility = $this->clamp((int) round(
            20
            + min(60, (float) ($stats['mag_size'] ?? 0)) * 0.45
            + min(1200, (float) ($stats['bv'] ?? 0)) * 0.025
            + max(0, 3800 - (float) ($stats['reload'] ?? 3800)) / 50
        ), 20, 99);

        $accuracy = $this->clamp((int) round(
            34
            + max(0, 22 - (float) ($stats['horiz_recoil'] ?? 22)) * 1.5
            + max(0, 60 - (float) ($stats['vert_recoil'] ?? 60)) * 0.5
            + max(0, 26 - (float) ($stats['gun_kick'] ?? 26)) * 0.8
        ), 20, 99);

        $mobility = $this->clamp((int) round(
            12
            + (float) ($stats['sprint'] ?? 0) * 4
            + (float) ($stats['movement'] ?? 0) * 7
            + (float) ($stats['ads_movement'] ?? 0) * 7
            + max(0, 320 - (float) ($stats['ads'] ?? 320)) / 7
            + max(0, 260 - (float) ($stats['stf'] ?? 260)) / 7
        ), 20, 99);

        $baseScore = (int) round(($attack + $defense + $utility + $accuracy + $mobility) / 5);
        $score = $this->clamp((int) round($baseScore * 0.82 + min(18, $popularity)), 20, 99);

        return [
            'score' => $score,
            'attack' => $attack,
            'defense' => $defense,
            'utility' => $utility,
            'accuracy' => $accuracy,
            'mobility' => $mobility,
        ];
    }

    private function mapAttachmentDeltas(?array $stats): array
    {
        if (!$stats) {
            return [
                'score' => 0,
                'attack' => 0,
                'defense' => 0,
                'utility' => 0,
                'accuracy' => 0,
                'mobility' => 0,
            ];
        }

        $attack = $this->clamp((int) round(
            $this->effectPoints($stats['range_mod'] ?? null, true, 0.45)
            + $this->effectPoints($stats['bv_mod'] ?? null, true, 0.22)
            + $this->effectPoints($stats['rpm_mod'] ?? null, true, 0.28)
        ), -25, 25);

        $defense = $this->clamp((int) round(
            $this->effectPoints($stats['gun_kick_mod'] ?? null, false, 0.2)
            + $this->effectPoints($stats['horiz_recoil_mod'] ?? null, false, 0.22)
            + $this->effectPoints($stats['vert_recoil_mod'] ?? null, false, 0.16)
            + $this->effectPoints($stats['flinch_resistance_mod'] ?? null, true, 0.2)
        ), -25, 25);

        $utility = $this->clamp((int) round(
            $this->effectPoints($stats['mag_size_mod'] ?? null, true, 0.18)
            + $this->effectPoints($stats['reload_mod'] ?? null, false, 0.2)
            + $this->effectPoints($stats['bv_mod'] ?? null, true, 0.08)
            + $this->effectPoints($stats['range_mod'] ?? null, true, 0.08)
        ), -25, 25);

        $accuracy = $this->clamp((int) round(
            $this->effectPoints($stats['gun_kick_mod'] ?? null, false, 0.18)
            + $this->effectPoints($stats['horiz_recoil_mod'] ?? null, false, 0.28)
            + $this->effectPoints($stats['vert_recoil_mod'] ?? null, false, 0.24)
            + $this->effectPoints($stats['ads_mod'] ?? null, false, 0.08)
        ), -25, 25);

        $mobility = $this->clamp((int) round(
            $this->effectPoints($stats['movement_mod'] ?? null, true, 0.24)
            + $this->effectPoints($stats['sprint_mod'] ?? null, true, 0.24)
            + $this->effectPoints($stats['crouch_movement_mod'] ?? null, true, 0.12)
            + $this->effectPoints($stats['ads_movement_mod'] ?? null, true, 0.22)
            + $this->effectPoints($stats['ads_mod'] ?? null, false, 0.18)
            + $this->effectPoints($stats['stf_mod'] ?? null, false, 0.18)
        ), -25, 25);

        $score = $this->clamp((int) round(($attack + $defense + $utility + $accuracy + $mobility) / 5), -20, 20);

        return [
            'score' => $score,
            'attack' => $attack,
            'defense' => $defense,
            'utility' => $utility,
            'accuracy' => $accuracy,
            'mobility' => $mobility,
        ];
    }

    private function effectPoints(mixed $value, bool $betterWhenHigher, float $scale): float
    {
        if (!is_numeric($value)) {
            return 0.0;
        }

        $numeric = (float) $value;

        if ($numeric === 0.0 || $numeric === 1.0) {
            return 0.0;
        }

        $delta = abs($numeric) <= 3
            ? ($numeric - 1.0) * 100
            : $numeric;

        return ($betterWhenHigher ? 1 : -1) * $delta * $scale;
    }

    private function buildModifierDescription(string $weaponName, string $attachmentName, ?array $stats): string
    {
        if (!$stats) {
            return "Imported {$attachmentName} for {$weaponName}.";
        }

        $parts = collect([
            $this->humanDelta($stats['range_mod'] ?? null, 'range', true),
            $this->humanDelta($stats['bv_mod'] ?? null, 'bullet velocity', true),
            $this->humanDelta($stats['gun_kick_mod'] ?? null, 'gun kick', false),
            $this->humanDelta($stats['horiz_recoil_mod'] ?? null, 'horizontal recoil', false),
            $this->humanDelta($stats['vert_recoil_mod'] ?? null, 'vertical recoil', false),
            $this->humanDelta($stats['ads_mod'] ?? null, 'ADS speed', false),
            $this->humanDelta($stats['movement_mod'] ?? null, 'movement', true),
        ])->filter()->take(3)->values();

        if ($parts->isEmpty()) {
            return "Imported {$attachmentName} for {$weaponName}.";
        }

        return $parts->implode(', ').'.';
    }

    private function humanDelta(mixed $value, string $label, bool $betterWhenHigher): ?string
    {
        if (!is_numeric($value)) {
            return null;
        }

        $numeric = (float) $value;

        if ($numeric === 0.0 || $numeric === 1.0) {
            return null;
        }

        $delta = abs($numeric) <= 3
            ? ($numeric - 1.0) * 100
            : $numeric;

        $beneficial = $betterWhenHigher ? $delta : -$delta;
        $sign = $beneficial >= 0 ? '+' : '';

        return $sign.(int) round($beneficial).'% '.$label;
    }

    private function buildTitle(string $weaponName, string $playstyle, int $position): string
    {
        return $position === 1
            ? "{$weaponName} - {$playstyle}"
            : "{$weaponName} - {$playstyle} {$position}";
    }

    private function buildShareToken(string $itemSlug, mixed $loadoutCode, int $position): string
    {
        $base = $loadoutCode
            ? 'codmunity-bo7-'.$itemSlug.'-'.Str::slug((string) $loadoutCode)
            : 'codmunity-bo7-'.$itemSlug.'-'.$position;

        return substr($base, 0, 64);
    }

    private function attachmentStatsKey(string $weaponName, string $attachmentName): string
    {
        return $this->normalizeLookupKey($weaponName).'|'.$this->normalizeLookupKey($attachmentName);
    }

    private function normalizeAttachmentId(mixed $value): ?string
    {
        if ($value === null || $value === false) {
            return null;
        }

        if (is_string($value) || is_int($value)) {
            $normalized = trim((string) $value);

            return $normalized !== '' ? $normalized : null;
        }

        if (is_object($value)) {
            return $this->normalizeAttachmentId((array) $value);
        }

        if (!is_array($value) || $value === []) {
            return null;
        }

        foreach (['_id', 'id', '$oid', 'value', 'attachmentId', 'attachment_id'] as $key) {
            if (array_key_exists($key, $value)) {
                return $this->normalizeAttachmentId($value[$key]);
            }
        }

        if (array_is_list($value)) {
            foreach ($value as $candidate) {
                $normalized = $this->normalizeAttachmentId($candidate);

                if ($normalized !== null) {
                    return $normalized;
                }
            }
        }

        return null;
    }

    private function normalizeLookupKey(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->squish()
            ->value();
    }

    private function normalizeSlotKey(string $label): string
    {
        $key = Str::slug($label, '_');

        return $key !== '' ? $key : 'slot_'.Str::random(6);
    }

    private function normalizeTagSlug(string $label): string
    {
        $slug = Str::slug($label);

        return $slug !== '' ? $slug : 'tag-'.Str::lower(Str::random(6));
    }

    private function normalizeTagLabel(string $label): string
    {
        return Str::of($label)
            ->lower()
            ->replace(' ', '-')
            ->replace('/', '-')
            ->replace('--', '-')
            ->trim('-')
            ->value();
    }

    private function formatTierLevel(mixed $unlock): ?string
    {
        if (!is_numeric($unlock)) {
            return null;
        }

        return 'L'.(int) $unlock;
    }

    private function trimmedSlug(string $value, int $maxLength, string $suffix): string
    {
        $slug = Str::slug($value);
        $suffix = Str::slug($suffix);

        if (strlen($slug) <= $maxLength) {
            return $slug;
        }

        $reserved = strlen($suffix) + 1;

        return substr($slug, 0, max(1, $maxLength - $reserved)).'-'.$suffix;
    }

    private function clamp(int $value, int $min, int $max): int
    {
        return max($min, min($max, $value));
    }
}
