<?php

namespace Database\Seeders;

class NewWorldSeeder extends AbstractGameSeeder
{
    protected function gameDefinition(): array
    {
        return [
            'name' => 'New World',
            'slug' => 'new-world',
            'genre' => 'MMORPG',
            'creation_category' => 'rpg',
        ];
    }

    protected function categoryDefinitions(): array
    {
        return [
            ['label' => 'Melee', 'slug' => 'melee'],
            ['label' => 'Ranged', 'slug' => 'ranged'],
            ['label' => 'Magic', 'slug' => 'magic'],
            ['label' => 'Tank Role', 'slug' => 'tank-role'],
            ['label' => 'DPS Role', 'slug' => 'dps-role'],
            ['label' => 'Healer Role', 'slug' => 'healer-role'],
        ];
    }

    protected function itemDefinitions(): array
    {
        $img = fn (string $path) => "https://static.wikia.nocookie.net/new-world2651/images/{$path}";

        return [
            // ── Melee Weapons ───────────────────────────────────────
            $this->item('Sword and Shield', 'weapon', 'Tank', 'Frontline defender absorbing damage and controlling enemies.', 80, [60, 90, 78, 55, 50], [
                $this->mod('Carnelian Gem', 'gem', 'Generates increased threat from all attacks.', [2, 0, 3, 2, 0, 0]),
                $this->mod('Sturdy Shield Enchant', 'enchant', 'Bonus fortify on successful blocks.', [1, 0, 4, 1, 0, 0]),
                $this->mod('Rally Socket', 'socket', 'Grants nearby allies a defensive buff.', [1, 0, 3, 2, 0, 0]),
            ], ['melee', 'tank-role']),

            $this->item('Great Axe', 'weapon', 'Berserker', 'All-out attacker maximising raw damage output.', 85, [92, 45, 48, 60, 65], [
                $this->mod('Onyx Gem', 'gem', 'Bonus damage against full-health targets.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Keen Enchant', 'enchant', 'Increases critical hit chance on attacks.', [2, 3, 0, 0, 1, 0]),
                $this->mod('Gravity Socket', 'socket', 'Pull enemies closer on ability hit.', [2, 1, 0, 2, 0, 0]),
            ], ['melee', 'dps-role'], imageUrl: $img('e/e1/Great_Axe.jpg/revision/latest?cb=20210605161212')),

            $this->item('War Hammer', 'weapon', 'Bruiser', 'Heavy crowd control with devastating stagger combos.', 78, [80, 72, 65, 55, 45], [
                $this->mod('Amber Gem WH', 'gem', 'Convert portion of damage to nature scaling.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Sundering Enchant', 'enchant', 'Rend armor on heavy attack hits.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Stagger Socket', 'socket', 'Extends crowd control duration on abilities.', [2, 1, 0, 2, 0, 0]),
            ], ['melee', 'tank-role'], imageUrl: $img('5/5d/War_Hammer.png/revision/latest?cb=20210605161811')),

            $this->item('Hatchet', 'weapon', 'Berserker', 'All-out attacker maximising raw damage output.', 82, [88, 48, 52, 58, 72], [
                $this->mod('Ruby Gem', 'gem', 'Bonus damage based on max health.', [2, 2, 1, 0, 0, 0]),
                $this->mod('Berserk Enchant', 'enchant', 'Increased damage below 50% health.', [3, 3, -1, 0, 0, 0]),
                $this->mod('Leech Socket', 'socket', 'Heal for portion of damage dealt.', [2, 1, 2, 0, 0, 0]),
            ], ['melee', 'dps-role'], imageUrl: $img('e/ef/Hatchet.png/revision/latest?cb=20210605160801')),

            $this->item('Spear', 'weapon', 'Duelist', 'Precise strikes with reach advantage and debuffs.', 79, [75, 50, 60, 78, 68], [
                $this->mod('Emerald Gem', 'gem', 'Bonus damage vs low-health targets.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Rend Enchant', 'enchant', 'Reduce target armor on successive hits.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Sweep Socket', 'socket', 'Wider AoE on sweep abilities.', [2, 1, 0, 1, 0, 0]),
            ], ['melee', 'dps-role'], imageUrl: $img('0/0b/Spear.jpg/revision/latest?cb=20210605153728')),

            $this->item('Rapier', 'weapon', 'Duelist', 'Precise strikes with reach advantage and debuffs.', 81, [78, 42, 55, 72, 88], [
                $this->mod('Diamond Gem Rapier', 'gem', 'Bonus damage at full health.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Flurry Enchant', 'enchant', 'Quick-strike combo deals escalating damage.', [2, 3, 0, 0, 0, 1]),
                $this->mod('Evade Socket', 'socket', 'Increased invulnerability frames on dodge.', [1, 0, 2, 0, 0, 2]),
            ], ['melee', 'dps-role'], imageUrl: $img('f/fe/Rapier.jpg/revision/latest?cb=20210605155540')),

            $this->item('Greatsword', 'weapon', 'Bruiser', 'Heavy crowd control with devastating stagger combos.', 83, [85, 55, 58, 62, 55], [
                $this->mod('Topaz Gem GS', 'gem', 'Convert portion of damage to lightning.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Relentless Enchant', 'enchant', 'Stacking damage buff on successive hits.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Onslaught Socket', 'socket', 'Gain grit during heavy attacks.', [2, 1, 2, 0, 0, 0]),
            ], ['melee', 'dps-role'], imageUrl: $img('3/3e/Greatsword1.png/revision/latest?cb=20230926195249')),

            // ── Ranged Weapons ──────────────────────────────────────
            $this->item('Bow', 'weapon', 'Ranger', 'Sustained ranged damage from safe distance.', 82, [85, 35, 55, 88, 70], [
                $this->mod('Sapphire Gem Bow', 'gem', 'Bonus mana regeneration on hit.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Enchanted Arrows', 'enchant', 'Elemental damage applied to all shots.', [2, 3, 0, 0, 1, 0]),
                $this->mod('Penetrating Socket', 'socket', 'Arrows pierce through first target.', [2, 2, 0, 0, 1, 0]),
            ], ['ranged', 'dps-role'], imageUrl: $img('8/85/Bow.jpg/revision/latest?cb=20210605154119')),

            $this->item('Musket', 'weapon', 'Sharpshooter', 'Extreme range precision with high single-shot damage.', 77, [90, 30, 48, 95, 42], [
                $this->mod('Opal Gem Musket', 'gem', 'Damage bonus when stamina is not full.', [2, 2, 0, 0, 0, 0]),
                $this->mod('Empowering Headshot', 'enchant', 'Increased critical multiplier on headshots.', [3, 4, 0, 0, 1, 0]),
                $this->mod('Trapper Socket', 'socket', 'Traps slow enemies in area of effect.', [1, 0, 0, 2, 0, 0]),
            ], ['ranged', 'dps-role'], imageUrl: $img('b/be/Musket.jpg/revision/latest?cb=20210605154611')),

            $this->item('Blunderbuss', 'weapon', 'Brawler', 'Mid-range hybrid with mobility and burst damage.', 79, [82, 40, 52, 58, 78], [
                $this->mod('Aquamarine Gem BB', 'gem', 'Bonus ice damage on attacks.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Net Shot Enchant', 'enchant', 'Slow enemies with spread shot.', [1, 1, 0, 2, 0, 0]),
                $this->mod('Blast Socket', 'socket', 'Wider spread for close-range engagements.', [2, 2, 0, 1, -1, 0]),
            ], ['ranged', 'dps-role']),

            // ── Magic Weapons ───────────────────────────────────────
            $this->item('Fire Staff', 'weapon', 'Mage DPS', 'Ranged magical damage dealer with elemental spells.', 84, [88, 30, 50, 80, 58], [
                $this->mod('Ruby Gem FS', 'gem', 'Increased fire damage scaling.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Scorching Enchant', 'enchant', 'Burn effect ticks for bonus damage over time.', [2, 3, 0, 0, 0, 0]),
                $this->mod('Fireball Socket', 'socket', 'Increased explosion radius on Fireball.', [2, 2, 0, 1, 0, 0]),
            ], ['magic', 'dps-role'], imageUrl: $img('1/10/Fire_Staff.png/revision/latest?cb=20210605162342')),

            $this->item('Ice Gauntlet', 'weapon', 'Controller', 'Zone control and crowd control with ice magic.', 81, [72, 42, 65, 78, 52], [
                $this->mod('Sapphire Gem IG', 'gem', 'Enhanced ice damage and mana recovery.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Frost Enchant', 'enchant', 'Slow and root effects last longer.', [1, 1, 1, 2, 0, 0]),
                $this->mod('Storm Socket', 'socket', 'Ice Storm area persists longer.', [2, 1, 0, 2, 0, 0]),
            ], ['magic', 'dps-role'], imageUrl: $img('1/16/Ice_Gauntlets.jpg/revision/latest?cb=20210605151634')),

            $this->item('Void Gauntlet', 'weapon', 'Debuffer', 'Hybrid melee/ranged support with debuffs and heals.', 80, [70, 48, 72, 68, 55], [
                $this->mod('Amethyst Gem VG', 'gem', 'Increased void damage.', [2, 2, 0, 1, 0, 0]),
                $this->mod('Decay Enchant', 'enchant', 'Debuffs reduce enemy healing received.', [1, 2, 0, 2, 0, 0]),
                $this->mod('Oblivion Socket', 'socket', 'Void zone buffs allies and debuffs enemies.', [2, 1, 1, 2, 0, 0]),
            ], ['magic', 'healer-role']),

            $this->item('Life Staff', 'weapon', 'Healer', 'Primary group healer keeping allies alive in combat.', 76, [40, 55, 92, 65, 48], [
                $this->mod('Amber Gem LS', 'gem', 'Nature damage scaling for threat balance.', [2, 1, 1, 1, 0, 0]),
                $this->mod('Blessed Enchant', 'enchant', 'Increases all healing output by a percentage.', [1, 0, 4, 1, 0, 0]),
                $this->mod('Sacred Ground Socket', 'socket', 'Healing zone persists longer and heals more.', [1, 0, 4, 1, 0, 0]),
            ], ['magic', 'healer-role'], imageUrl: $img('5/5b/LifeStaff.png/revision/latest?cb=20210605153104')),
        ];
    }
}
