<?php

namespace App\Services\GameDrafts;

class GameCategoryTemplateRegistry
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return array_values($this->templates());
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $key): ?array
    {
        $templates = $this->templates();

        return $templates[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(string $key): array
    {
        $template = $this->find($key);

        if ($template === null) {
            return [];
        }

        return [
            'supports_modifiers' => $template['supports_modifiers'],
            'supports_archetypes' => $template['supports_archetypes'],
            'allowed_item_slots' => $template['allowed_item_slots'],
            'allowed_modifier_types' => $template['allowed_modifier_types'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function templates(): array
    {
        return [
            'fps' => [
                'key' => 'fps',
                'label' => 'FPS',
                'description' => 'Template axe sur un shooter modulaire avec armes, archetypes et accessoires.',
                'version' => '1.0',
                'supports_modifiers' => true,
                'supports_archetypes' => true,
                'allowed_item_slots' => ['weapon', 'secondary', 'equipment'],
                'allowed_modifier_types' => [
                    'optic',
                    'muzzle',
                    'barrel',
                    'underbarrel',
                    'magazine',
                    'rear_grip',
                    'stock',
                    'laser',
                    'ammunition',
                    'mechanism',
                ],
                'minimums' => [
                    'archetypes' => 1,
                    'categories' => 2,
                    'items' => 4,
                    'modifiers' => 3,
                ],
            ],
            'rpg' => [
                'key' => 'rpg',
                'label' => 'RPG cadre',
                'description' => 'Template pour un jeu d equipement type RPG, limite au modele de stats actuel.',
                'version' => '1.0',
                'supports_modifiers' => true,
                'supports_archetypes' => true,
                'allowed_item_slots' => ['weapon', 'helmet', 'armor', 'gloves', 'boots', 'trinket'],
                'allowed_modifier_types' => ['socket', 'rune', 'enchant', 'gem'],
                'minimums' => [
                    'archetypes' => 1,
                    'categories' => 2,
                    'items' => 6,
                    'modifiers' => 3,
                ],
            ],
        ];
    }
}
