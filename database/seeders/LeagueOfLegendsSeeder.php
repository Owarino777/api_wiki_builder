<?php

namespace Database\Seeders;

class LeagueOfLegendsSeeder extends AbstractGameSeeder
{
    private const DDRAGON = 'https://ddragon.leagueoflegends.com/cdn/14.24.1/img/champion/';

    protected function gameDefinition(): array
    {
        return [
            'name' => 'League of Legends',
            'slug' => 'league-of-legends',
            'genre' => 'MOBA',
            'creation_category' => 'rpg',
        ];
    }

    protected function categoryDefinitions(): array
    {
        return [
            ['label' => 'Mage', 'slug' => 'mage'],
            ['label' => 'Marksman', 'slug' => 'marksman'],
            ['label' => 'Fighter', 'slug' => 'fighter'],
            ['label' => 'Assassin', 'slug' => 'assassin'],
            ['label' => 'Support', 'slug' => 'support'],
            ['label' => 'Tank', 'slug' => 'tank'],
        ];
    }

    protected function itemDefinitions(): array
    {
        return [
            // ── Marksmen ────────────────────────────────────────────
            $this->item('Jinx', 'weapon', 'Hyper Carry', 'Late-game damage powerhouse that scales with items.', 88, [92, 32, 60, 85, 75], [
                $this->mod('Infinity Edge', 'enchant', 'Massively increases critical strike damage.', [3, 4, 0, 0, 1, 0]),
                $this->mod("Runaan's Hurricane", 'socket', 'Bolts chain to nearby enemies on attack.', [2, 2, 0, 1, 0, 1]),
                $this->mod('Lethal Tempo', 'rune', 'Attack speed ramps up in extended fights.', [2, 3, 0, 0, 0, 1]),
            ], ['marksman'], imageUrl: self::DDRAGON . 'Jinx.png'),

            $this->item("Kai'Sa", 'weapon', 'Hyper Carry', 'Late-game damage powerhouse that scales with items.', 87, [90, 35, 65, 82, 78], [
                $this->mod("Nashor's Tooth", 'enchant', 'On-hit magic damage scaling with AP.', [2, 3, 0, 1, 0, 0]),
                $this->mod("Guinsoo's Rageblade", 'socket', 'Converts crit into on-hit phantom hits.', [3, 3, 0, 0, 0, 1]),
                $this->mod('Hail of Blades', 'rune', 'Rapid burst of three attacks on engage.', [2, 3, 0, 0, 0, 1]),
            ], ['marksman', 'assassin'], imageUrl: self::DDRAGON . 'Kaisa.png'),

            $this->item('Jhin', 'weapon', 'Marksman Utility', 'Long-range ADC with burst damage and crowd control.', 85, [88, 30, 70, 90, 55], [
                $this->mod('Galeforce', 'enchant', 'Active dash for repositioning and burst.', [2, 2, 0, 1, 0, 2]),
                $this->mod('Rapid Firecannon', 'socket', 'Extended attack range on energized shots.', [2, 2, 0, 0, 1, 0]),
                $this->mod('Fleet Footwork', 'rune', 'Energized attacks heal and grant movement speed.', [1, 1, 1, 0, 0, 2]),
            ], ['marksman'], imageUrl: self::DDRAGON . 'Jhin.png'),

            $this->item('Ezreal', 'weapon', 'Poke ADC', 'Safe ranged damage dealer with skillshot-based kit.', 84, [82, 38, 68, 85, 80], [
                $this->mod('Trinity Force LoL', 'enchant', 'Spellblade proc with hybrid offensive stats.', [3, 3, 1, 1, 0, 0]),
                $this->mod('Muramana', 'socket', 'Massive bonus damage from mana scaling.', [3, 4, 0, 0, 0, 0]),
                $this->mod('Conqueror LoL', 'rune', 'Stacking adaptive force in extended combat.', [2, 3, 0, 1, 0, 0]),
            ], ['marksman', 'mage'], imageUrl: self::DDRAGON . 'Ezreal.png'),

            // ── Mages ───────────────────────────────────────────────
            $this->item('Ahri', 'weapon', 'Burst Mage', 'High burst damage and pick potential with mobility.', 85, [80, 38, 78, 82, 72], [
                $this->mod("Rabadon's Deathcap", 'enchant', 'Enormously amplifies ability power.', [3, 5, 0, 1, 0, 0]),
                $this->mod("Luden's Tempest", 'socket', 'Spells echo for bonus magic damage.', [2, 3, 0, 1, 0, 0]),
                $this->mod('Electrocute', 'rune', 'Burst combo triggers bonus adaptive damage.', [2, 3, 0, 0, 1, 0]),
            ], ['mage', 'assassin'], imageUrl: self::DDRAGON . 'Ahri.png'),

            $this->item('Lux', 'weapon', 'Artillery Mage', 'Long-range poke and burst with crowd control.', 82, [78, 32, 80, 88, 50], [
                $this->mod("Luden's Echo", 'enchant', 'Spells deal bonus splash magic damage.', [2, 3, 0, 1, 0, 0]),
                $this->mod('Horizon Focus', 'socket', 'Bonus damage on long-range spells.', [2, 3, 0, 0, 1, 0]),
                $this->mod('Arcane Comet', 'rune', 'Damaging abilities hurl a comet at enemies.', [1, 2, 0, 1, 1, 0]),
            ], ['mage', 'support'], imageUrl: self::DDRAGON . 'Lux.png'),

            $this->item('Viktor', 'weapon', 'Control Mage', 'Zone control and scaling AoE damage dealer.', 84, [85, 35, 75, 80, 48], [
                $this->mod('Hextech Rocketbelt', 'enchant', 'Dash forward with magic damage burst.', [2, 2, 0, 1, 0, 2]),
                $this->mod("Lich Bane", 'socket', 'Empowered auto after ability cast.', [2, 3, 0, 0, 0, 1]),
                $this->mod('First Strike LoL', 'rune', 'Bonus gold and damage when initiating.', [2, 2, 0, 2, 0, 0]),
            ], ['mage'], imageUrl: self::DDRAGON . 'Viktor.png'),

            $this->item('Syndra', 'weapon', 'Burst Mage', 'High burst damage and pick potential with mobility.', 83, [90, 30, 72, 82, 45], [
                $this->mod('Shadowflame', 'enchant', 'Magic pen scaling against shielded targets.', [2, 3, 0, 0, 1, 0]),
                $this->mod("Zhonya's Hourglass", 'socket', 'Stasis active for defensive outplay.', [1, 0, 4, 0, 0, 0]),
                $this->mod('Dark Harvest', 'rune', 'Bonus damage on low-health targets stacks.', [2, 3, 0, 0, 0, 0]),
            ], ['mage'], imageUrl: self::DDRAGON . 'Syndra.png'),

            // ── Assassins ───────────────────────────────────────────
            $this->item('Zed', 'weapon', 'AD Assassin', 'High burst physical damage assassin with shadow play.', 86, [92, 28, 55, 78, 88], [
                $this->mod('Youmuu Ghostblade', 'enchant', 'Lethality and out-of-combat movement speed.', [2, 2, 0, 0, 0, 3]),
                $this->mod('Edge of Night', 'socket', 'Spell shield active blocks one ability.', [1, 1, 3, 0, 0, 0]),
                $this->mod('Electrocute Zed', 'rune', 'Burst combo triggers bonus adaptive damage.', [2, 3, 0, 0, 1, 0]),
            ], ['assassin'], imageUrl: self::DDRAGON . 'Zed.png'),

            $this->item('Akali', 'weapon', 'AP Assassin', 'Mobile assassin with stealth and burst combos.', 85, [88, 32, 60, 75, 90], [
                $this->mod('Hextech Gunblade', 'enchant', 'Omnivamp and active slow for all-ins.', [2, 2, 1, 1, 0, 0]),
                $this->mod('Void Staff', 'socket', 'Massive magic penetration for tanks.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Fleet Footwork Akali', 'rune', 'Sustain and movement speed in lane.', [1, 1, 1, 0, 0, 2]),
            ], ['assassin', 'mage'], imageUrl: self::DDRAGON . 'Akali.png'),

            // ── Fighters ────────────────────────────────────────────
            $this->item('Yasuo', 'weapon', 'Melee Carry', 'High-risk, high-reward skirmisher with outplay potential.', 86, [88, 42, 55, 75, 82], [
                $this->mod('Immortal Shieldbow', 'enchant', 'Lifeline shield triggers when low on health.', [2, 2, 3, 0, 0, 0]),
                $this->mod('Phantom Dancer', 'socket', 'Ghosting and attack speed on critical hits.', [2, 2, 0, 0, 0, 2]),
                $this->mod('Conqueror Yasuo', 'rune', 'Stacking adaptive force in extended combat.', [2, 3, 0, 1, 0, 0]),
            ], ['fighter', 'assassin'], imageUrl: self::DDRAGON . 'Yasuo.png'),

            $this->item('Lee Sin', 'weapon', 'Bruiser', 'Aggressive early-game fighter with high mobility.', 84, [82, 52, 72, 68, 85], [
                $this->mod('Goredrinker', 'enchant', 'AoE heal on active with omnivamp sustain.', [2, 2, 2, 1, 0, 0]),
                $this->mod('Black Cleaver', 'socket', 'Shred armor with consecutive physical hits.', [2, 3, 1, 0, 0, 0]),
                $this->mod('Hail of Blades LeeSin', 'rune', 'Rapid burst of three attacks on engage.', [2, 3, 0, 0, 0, 1]),
            ], ['fighter'], imageUrl: self::DDRAGON . 'LeeSin.png'),

            $this->item('Darius', 'weapon', 'Juggernaut', 'Tanky melee fighter with high sustained damage.', 82, [86, 68, 50, 60, 48], [
                $this->mod('Trinity Force Darius', 'enchant', 'Spellblade proc with hybrid offensive stats.', [3, 3, 1, 1, 0, 0]),
                $this->mod("Dead Man's Plate", 'socket', 'Momentum grants movement speed and bonus damage.', [1, 1, 3, 0, 0, 1]),
                $this->mod('Phase Rush', 'rune', 'Burst of movement speed after three attacks.', [1, 0, 0, 1, 0, 3]),
            ], ['fighter', 'tank'], imageUrl: self::DDRAGON . 'Darius.png'),

            $this->item('Yone', 'weapon', 'Melee Carry', 'High-risk, high-reward skirmisher with outplay potential.', 87, [90, 38, 52, 78, 80], [
                $this->mod('Blade of the Ruined King', 'enchant', 'On-hit percent health damage and lifesteal.', [3, 3, 0, 0, 0, 1]),
                $this->mod("Wit's End", 'socket', 'On-hit magic damage and magic resist.', [2, 2, 2, 0, 0, 0]),
                $this->mod('Lethal Tempo Yone', 'rune', 'Attack speed ramps up in extended fights.', [2, 3, 0, 0, 0, 1]),
            ], ['fighter', 'assassin'], imageUrl: self::DDRAGON . 'Yone.png'),

            // ── Tanks / Supports ────────────────────────────────────
            $this->item('Thresh', 'weapon', 'Utility Tank', 'Crowd control and peel specialist for team fights.', 80, [45, 78, 92, 70, 55], [
                $this->mod('Locket of the Iron Solari', 'enchant', 'Shield nearby allies from burst damage.', [2, 0, 4, 2, 0, 0]),
                $this->mod("Knight's Vow", 'socket', 'Bond with an ally to share incoming damage.', [1, 0, 3, 2, 0, 0]),
                $this->mod('Aftershock', 'rune', 'Bonus resistances after immobilizing enemies.', [2, 0, 4, 0, 0, 0]),
            ], ['support', 'tank'], imageUrl: self::DDRAGON . 'Thresh.png'),

            $this->item('Leona', 'weapon', 'Engage Tank', 'Aggressive tank initiator with heavy crowd control.', 78, [50, 85, 88, 60, 50], [
                $this->mod('Sunfire Aegis', 'enchant', 'Immolate passive burns nearby enemies.', [2, 2, 3, 0, 0, 0]),
                $this->mod('Thornmail', 'socket', 'Reflects damage and applies Grievous Wounds.', [1, 1, 4, 0, 0, 0]),
                $this->mod('Aftershock Leona', 'rune', 'Bonus resistances after immobilizing enemies.', [2, 0, 4, 0, 0, 0]),
            ], ['tank', 'support'], imageUrl: self::DDRAGON . 'Leona.png'),

            $this->item('Nautilus', 'weapon', 'Engage Tank', 'Aggressive tank initiator with heavy crowd control.', 77, [48, 82, 85, 55, 42], [
                $this->mod('Evenshroud', 'enchant', 'Nearby immobilized enemies take more damage.', [2, 1, 3, 1, 0, 0]),
                $this->mod('Frozen Heart', 'socket', 'Reduces nearby enemy attack speed.', [1, 0, 4, 1, 0, 0]),
                $this->mod('Guardian LoL', 'rune', 'Shield nearby allies when they take damage.', [1, 0, 3, 2, 0, 0]),
            ], ['tank', 'support'], imageUrl: self::DDRAGON . 'Nautilus.png'),
        ];
    }
}
