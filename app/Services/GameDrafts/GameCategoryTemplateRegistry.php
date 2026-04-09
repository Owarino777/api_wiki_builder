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
                'starter_archetypes' => [
                    ['name' => 'Assaillant', 'slug' => 'assaillant', 'description' => 'Profil polyvalent pour les combats a moyenne distance.'],
                    ['name' => 'Sniper', 'slug' => 'sniper', 'description' => 'Profil precision, controle de ligne et faible mobilite.'],
                    ['name' => 'Support', 'slug' => 'support', 'description' => 'Profil utilitaire avec gadgets et controle d espace.'],
                ],
                'starter_categories' => [
                    ['label' => 'Assault rifle', 'slug' => 'assault-rifle', 'description' => 'Armes polyvalentes et adaptables.'],
                    ['label' => 'SMG', 'slug' => 'smg', 'description' => 'Armes tres mobiles pour le close range.'],
                    ['label' => 'Sniper rifle', 'slug' => 'sniper-rifle', 'description' => 'Armes a fort impact et cadence plus faible.'],
                    ['label' => 'Equipment', 'slug' => 'equipment', 'description' => 'Gadgets, grenades et outils de terrain.'],
                ],
                'suggested_item_slots' => [
                    'weapon' => [
                        ['slot_key' => 'optic', 'slot_label' => 'Optique', 'accepted_modifier_type' => 'optic', 'is_required' => false],
                        ['slot_key' => 'muzzle', 'slot_label' => 'Bouche', 'accepted_modifier_type' => 'muzzle', 'is_required' => false],
                        ['slot_key' => 'barrel', 'slot_label' => 'Canon', 'accepted_modifier_type' => 'barrel', 'is_required' => false],
                        ['slot_key' => 'magazine', 'slot_label' => 'Chargeur', 'accepted_modifier_type' => 'magazine', 'is_required' => false],
                        ['slot_key' => 'stock', 'slot_label' => 'Crosse', 'accepted_modifier_type' => 'stock', 'is_required' => false],
                    ],
                    'secondary' => [
                        ['slot_key' => 'optic', 'slot_label' => 'Optique', 'accepted_modifier_type' => 'optic', 'is_required' => false],
                        ['slot_key' => 'muzzle', 'slot_label' => 'Bouche', 'accepted_modifier_type' => 'muzzle', 'is_required' => false],
                        ['slot_key' => 'laser', 'slot_label' => 'Laser', 'accepted_modifier_type' => 'laser', 'is_required' => false],
                    ],
                    'equipment' => [],
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
                'starter_archetypes' => [
                    ['name' => 'Guerrier', 'slug' => 'guerrier', 'description' => 'Profil melee resistant et stable.'],
                    ['name' => 'Mage', 'slug' => 'mage', 'description' => 'Profil puissance et utilite magique.'],
                    ['name' => 'Voleur', 'slug' => 'voleur', 'description' => 'Profil mobilite, critique et precision.'],
                ],
                'starter_categories' => [
                    ['label' => 'Main weapon', 'slug' => 'main-weapon', 'description' => 'Armes principales du personnage.'],
                    ['label' => 'Armor', 'slug' => 'armor', 'description' => 'Pieces defensives et equipements de protection.'],
                    ['label' => 'Jewelry', 'slug' => 'jewelry', 'description' => 'Objets de bonus et synergies de build.'],
                    ['label' => 'Consumable', 'slug' => 'consumable', 'description' => 'Objets ponctuels et supports temporaires.'],
                ],
                'suggested_item_slots' => [
                    'weapon' => [
                        ['slot_key' => 'socket', 'slot_label' => 'Socket', 'accepted_modifier_type' => 'socket', 'is_required' => false],
                        ['slot_key' => 'enchant', 'slot_label' => 'Enchantement', 'accepted_modifier_type' => 'enchant', 'is_required' => false],
                    ],
                    'helmet' => [
                        ['slot_key' => 'rune', 'slot_label' => 'Rune', 'accepted_modifier_type' => 'rune', 'is_required' => false],
                    ],
                    'armor' => [
                        ['slot_key' => 'gem', 'slot_label' => 'Gemme', 'accepted_modifier_type' => 'gem', 'is_required' => false],
                        ['slot_key' => 'enchant', 'slot_label' => 'Enchantement', 'accepted_modifier_type' => 'enchant', 'is_required' => false],
                    ],
                    'gloves' => [],
                    'boots' => [],
                    'trinket' => [
                        ['slot_key' => 'socket', 'slot_label' => 'Socket', 'accepted_modifier_type' => 'socket', 'is_required' => false],
                    ],
                ],
            ],
        ];
    }
}
