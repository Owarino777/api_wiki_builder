<?php

namespace Database\Seeders;

class BattlefieldSeeder extends AbstractGameSeeder
{
    protected function gameDefinition(): array
    {
        return [
            'name' => 'Battlefield 2042',
            'slug' => 'battlefield-2042',
            'genre' => 'First-Person Shooter',
            'creation_category' => 'fps',
        ];
    }

    protected function categoryDefinitions(): array
    {
        return [
            ['label' => 'Assault Rifle', 'slug' => 'assault-rifle'],
            ['label' => 'SMG', 'slug' => 'smg'],
            ['label' => 'DMR', 'slug' => 'dmr'],
            ['label' => 'Sniper Rifle', 'slug' => 'sniper-rifle'],
            ['label' => 'LMG', 'slug' => 'lmg'],
            ['label' => 'Shotgun', 'slug' => 'shotgun'],
            ['label' => 'Sidearm', 'slug' => 'sidearm'],
        ];
    }

    protected function itemDefinitions(): array
    {
        $img = fn (string $path) => "https://static.wikia.nocookie.net/battlefield/images/{$path}";

        return [
            // ── Assault Rifles ──────────────────────────────────────
            $this->item('M5A3', 'weapon', 'Assault Specialist', 'Balanced frontline combatant with versatile loadout.', 83, [79, 54, 67, 77, 64], [
                $this->mod('Kobra Sight', 'optic', 'Red dot sight for fast target acquisition.', [1, 0, 0, 1, 2, 0]),
                $this->mod('Compensator', 'muzzle', 'Reduces vertical recoil during sustained fire.', [2, 0, 1, 0, 2, -1]),
                $this->mod('Stubby Grip', 'underbarrel', 'Improves accuracy under sustained fire.', [1, 0, 1, 0, 2, -1]),
            ], ['assault-rifle'], imageUrl: $img('8/8b/Battlefield_2042_M5A3.png/revision/latest?cb=20220131201455')),

            $this->item('AK-24', 'weapon', 'Assault Specialist', 'Balanced frontline combatant with versatile loadout.', 80, [84, 52, 60, 72, 58], [
                $this->mod('M11 6x Scope', 'optic', 'Medium range magnified optic for precision.', [1, 1, 0, 0, 3, -1]),
                $this->mod('Extended Barrel BF', 'barrel', 'Increases effective range and bullet velocity.', [2, 2, 0, 1, 1, -1]),
                $this->mod('Drum Magazine', 'magazine', 'Extended magazine capacity for sustained engagements.', [1, 1, 0, 0, 0, -2]),
            ], ['assault-rifle'], imageUrl: $img('d/d9/Battlefield_2042_AK-24.png/revision/latest?cb=20220202193230')),

            $this->item('SFAR-M GL', 'weapon', 'Combat Engineer', 'Versatile combatant with explosive support options.', 81, [82, 55, 70, 74, 60], [
                $this->mod('SFAR Hybrid Sight', 'optic', 'Quick-switch between 1x and 3x.', [1, 0, 0, 1, 2, 0]),
                $this->mod('SFAR Muzzle Brake', 'muzzle', 'Reduces first-shot recoil kick.', [1, 0, 1, 0, 2, 0]),
                $this->mod('SFAR Angled Grip', 'underbarrel', 'Faster ADS from sprint.', [1, 0, 0, 1, 1, 1]),
            ], ['assault-rifle'], imageUrl: $img('9/9c/Battlefield_2042_SFAR-M_GL.png/revision/latest?cb=20220202193829')),

            $this->item('AC-42', 'weapon', 'Assault Specialist', 'Balanced frontline combatant with versatile loadout.', 79, [78, 50, 65, 80, 62], [
                $this->mod('AC42 Burst Tuning', 'mechanism', 'Optimized burst delay for mid range.', [1, 1, 0, 0, 2, 0]),
                $this->mod('AC42 Long Barrel', 'barrel', 'Increased velocity for ranged burst hits.', [2, 2, 0, 0, 1, -1]),
                $this->mod('AC42 Vertical Grip', 'underbarrel', 'Tighter burst grouping at range.', [1, 0, 1, 0, 2, -1]),
            ], ['assault-rifle'], imageUrl: $img('4/4d/Battlefield_2042_AC-42.png/revision/latest?cb=20220202194315')),

            // ── SMGs ────────────────────────────────────────────────
            $this->item('PP-29', 'weapon', 'Flanker', 'Aggressive close-range operator focused on mobility.', 82, [72, 48, 70, 65, 88], [
                $this->mod('Laser Sight BF', 'laser', 'Tighter hipfire spread for close encounters.', [1, 0, 0, 1, 1, 1]),
                $this->mod('Short Barrel', 'barrel', 'Faster handling and ADS speed.', [1, 1, 0, 0, -1, 2]),
                $this->mod('Light Grip', 'underbarrel', 'Improved ADS movement speed.', [1, 0, 0, 1, 0, 2]),
            ], ['smg'], imageUrl: $img('f/fb/Battlefield_2042_PP-29.png/revision/latest?cb=20220202195532')),

            $this->item('K30', 'weapon', 'Flanker', 'Aggressive close-range operator focused on mobility.', 78, [68, 42, 62, 55, 92], [
                $this->mod('K30 Rapid Fire', 'mechanism', 'Extreme fire rate for CQB dominance.', [2, 2, -1, 0, -2, 1]),
                $this->mod('K30 Laser Unit', 'laser', 'Maximum hipfire accuracy while sprinting.', [1, 0, 0, 1, 1, 1]),
                $this->mod('K30 Drum Mag', 'magazine', 'Extended ammo for sustained spray.', [1, 1, 0, 0, 0, -2]),
            ], ['smg'], imageUrl: $img('1/1a/Battlefield_2042_K30.png/revision/latest?cb=20220317060020')),

            $this->item('MP9', 'weapon', 'Flanker', 'Aggressive close-range operator focused on mobility.', 76, [65, 40, 60, 58, 90], [
                $this->mod('MP9 Suppressor', 'muzzle', 'Silent shots and hidden minimap signature.', [1, -1, 0, 2, 0, 0]),
                $this->mod('MP9 Extended Mag', 'magazine', 'More rounds for flanking maneuvers.', [1, 1, 0, 0, 0, -1]),
                $this->mod('MP9 Light Stock', 'stock', 'Maximum mobility while equipped.', [1, 0, 0, 0, -1, 3]),
            ], ['smg'], imageUrl: $img('2/24/Battlefield_2042_MP9.png/revision/latest?cb=20220202200129')),

            // ── DMRs ────────────────────────────────────────────────
            $this->item('DM7', 'weapon', 'Recon Expert', 'Long-range reconnaissance and precision shooting.', 79, [82, 55, 62, 88, 52], [
                $this->mod('BKS 8x Scope', 'optic', 'High-magnification optic for long-range engagements.', [2, 1, 0, 0, 4, -2]),
                $this->mod('Heavy Barrel', 'barrel', 'Maximum effective range and velocity.', [2, 2, 0, 0, 2, -2]),
                $this->mod('Bipod', 'underbarrel', 'Eliminates sway when deployed prone.', [2, 0, 2, 0, 3, -3]),
            ], ['dmr'], imageUrl: $img('3/39/Battlefield_2042_DM7.png/revision/latest?cb=20220203193057')),

            $this->item('SVK', 'weapon', 'Recon Expert', 'Long-range reconnaissance and precision shooting.', 80, [85, 50, 58, 85, 48], [
                $this->mod('SVK Precision Scope', 'optic', 'Clear long-range optic with thin reticle.', [2, 1, 0, 0, 3, -1]),
                $this->mod('SVK Match Barrel', 'barrel', 'Improved bullet grouping at range.', [2, 2, 0, 0, 2, -1]),
                $this->mod('SVK Cheek Rest', 'stock', 'Reduced scope sway while ADS.', [1, 0, 1, 0, 2, 0]),
            ], ['dmr'], imageUrl: $img('c/cb/Battlefield_2042_SVK.png/revision/latest?cb=20220203193555')),

            // ── Sniper Rifles ───────────────────────────────────────
            $this->item('SWS-10', 'weapon', 'Marksman', 'Precision long-range operator with one-shot potential.', 77, [95, 40, 50, 92, 38], [
                $this->mod('Variable Zoom 6-12x', 'optic', 'Variable zoom for extreme range engagements.', [2, 1, 0, 0, 4, -3]),
                $this->mod('Suppressor BF', 'muzzle', 'Hides muzzle flash and reduces sound signature.', [1, -1, 1, 2, 0, 0]),
                $this->mod('Tactical Stock', 'stock', 'Faster transition between stances.', [1, 0, 1, 0, 1, 1]),
            ], ['sniper-rifle'], imageUrl: $img('2/26/Battlefield_2042_SWS-10.png/revision/latest?cb=20220203194228')),

            $this->item('NTW-50', 'weapon', 'Marksman', 'Precision long-range operator with one-shot potential.', 75, [99, 35, 45, 95, 25], [
                $this->mod('NTW Fixed 10x', 'optic', 'Fixed high-power scope for extreme range.', [2, 1, 0, 0, 4, -3]),
                $this->mod('NTW Anti-Materiel Round', 'ammunition', 'Penetrates light vehicle armor.', [3, 3, 0, 0, 0, -2]),
                $this->mod('NTW Bipod', 'underbarrel', 'Essential stability for heavy anti-materiel.', [2, 0, 2, 0, 3, -4]),
            ], ['sniper-rifle'], imageUrl: $img('5/56/Battlefield_2042_NTW-50.png/revision/latest?cb=20220402191921')),

            // ── LMGs ────────────────────────────────────────────────
            $this->item('LCMG', 'weapon', 'Support Gunner', 'Sustained fire suppression and area denial.', 78, [75, 58, 72, 60, 45], [
                $this->mod('LCMG Extended Belt', 'magazine', '200-round belt for maximum suppression.', [1, 1, 1, 0, 0, -2]),
                $this->mod('LCMG Thermal', 'optic', 'Thermal imaging for target detection.', [2, 0, 0, 2, 2, -1]),
                $this->mod('LCMG Stabilizer', 'underbarrel', 'Reduced recoil during sustained fire.', [1, 0, 1, 0, 2, -1]),
            ], ['lmg'], imageUrl: $img('c/cf/Battlefield_2042_LCMG.png/revision/latest?cb=20220203195534')),

            $this->item('PKP-BP', 'weapon', 'Support Gunner', 'Sustained fire suppression and area denial.', 80, [80, 60, 68, 55, 40], [
                $this->mod('PKP Heavy Barrel', 'barrel', 'Increased damage at extended ranges.', [2, 2, 0, 0, 1, -2]),
                $this->mod('PKP Drum Mag', 'magazine', 'Extended belt capacity for area control.', [1, 1, 0, 0, 0, -1]),
                $this->mod('PKP Bipod', 'underbarrel', 'Maximum stability when deployed.', [2, 0, 2, 0, 3, -3]),
            ], ['lmg'], imageUrl: $img('8/8a/Battlefield_2042_PKP-BP.png/revision/latest?cb=20220203200011')),

            // ── Shotguns ────────────────────────────────────────────
            $this->item('MCS-880', 'weapon', 'Breacher', 'Close-quarters specialist clearing rooms and objectives.', 75, [90, 45, 55, 40, 72], [
                $this->mod('Holographic Sight', 'optic', 'Wide field of view for CQB.', [1, 0, 0, 1, 1, 0]),
                $this->mod('Slug Round', 'ammunition', 'Single slug projectile for longer range.', [2, 2, 0, 0, 3, -1]),
                $this->mod('Choke BF', 'muzzle', 'Tighter pellet spread on shotguns.', [1, 1, 0, 0, 2, 0]),
            ], ['shotgun'], imageUrl: $img('c/c4/Battlefield_2042_MCS-880.png/revision/latest?cb=20220203201414')),

            $this->item('12M Auto', 'weapon', 'Breacher', 'Close-quarters specialist clearing rooms and objectives.', 73, [82, 42, 52, 35, 78], [
                $this->mod('12M Laser Module', 'laser', 'Hipfire precision for rapid engagements.', [1, 0, 0, 1, 1, 1]),
                $this->mod('12M Extended Tube', 'magazine', 'Extra shells for sustained CQB.', [1, 1, 0, 0, 0, -1]),
                $this->mod('12M Buckshot Plus', 'ammunition', 'Tighter spread pattern per shell.', [1, 1, 0, 0, 2, 0]),
            ], ['shotgun'], imageUrl: $img('3/36/Battlefield_2042_12M_Auto.png/revision/latest?cb=20220207185353')),
        ];
    }
}
