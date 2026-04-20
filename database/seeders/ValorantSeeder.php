<?php

namespace Database\Seeders;

class ValorantSeeder extends AbstractGameSeeder
{
    protected function gameDefinition(): array
    {
        return [
            'name' => 'Valorant',
            'slug' => 'valorant',
            'genre' => 'Tactical Shooter',
            'creation_category' => 'fps',
        ];
    }

    protected function categoryDefinitions(): array
    {
        return [
            ['label' => 'Sidearm', 'slug' => 'sidearm'],
            ['label' => 'SMG', 'slug' => 'smg'],
            ['label' => 'Shotgun', 'slug' => 'shotgun'],
            ['label' => 'Rifle', 'slug' => 'rifle'],
            ['label' => 'Sniper', 'slug' => 'sniper'],
            ['label' => 'Heavy', 'slug' => 'heavy'],
        ];
    }

    protected function itemDefinitions(): array
    {
        return [
            // ── Rifles ──────────────────────────────────────────────
            $this->item('Vandal', 'weapon', 'Entry Fragger', 'Aggressive duelist playstyle with high kill potential.', 88, [85, 40, 65, 78, 70], [
                $this->mod('Vandal Reflex Sight', 'optic', 'Enhanced target tracking at medium range.', [1, 0, 0, 1, 2, 0]),
                $this->mod('Vandal Stabilizer', 'muzzle', 'Improved first-bullet accuracy on tap fire.', [2, 0, 1, 0, 2, -1]),
                $this->mod('Vandal Ergonomic Grip', 'rear_grip', 'Faster ADS transition speed.', [1, 0, 0, 1, 0, 2]),
            ], ['rifle'], imageUrl: 'https://media.valorant-api.com/weapons/9c82e19d-4575-0200-1a81-3eacf00cf872/displayicon.png'),

            $this->item('Phantom', 'weapon', 'Entry Fragger', 'Aggressive duelist playstyle with high kill potential.', 87, [80, 42, 70, 82, 72], [
                $this->mod('Phantom Silencer', 'muzzle', 'Reduced audio signature and no tracers.', [2, -1, 0, 2, 1, 0]),
                $this->mod('Phantom Extended Mag', 'magazine', '30-round magazine for sustained fights.', [1, 1, 0, 0, 0, -1]),
                $this->mod('Phantom Lightweight Stock', 'stock', 'Faster movement while aiming down sights.', [1, 0, 0, 1, -1, 2]),
            ], ['rifle'], imageUrl: 'https://media.valorant-api.com/weapons/ee8e8d15-496b-07ac-f604-8b8f9b02d233/displayicon.png'),

            $this->item('Bulldog', 'weapon', 'Flex Player', 'Versatile mid-range combatant who adapts to any situation.', 78, [72, 45, 68, 75, 68], [
                $this->mod('Bulldog Burst Module', 'mechanism', 'Improved burst fire accuracy at range.', [1, 1, 0, 0, 2, 0]),
                $this->mod('Bulldog Foregrip', 'underbarrel', 'Reduced horizontal recoil.', [1, 0, 1, 0, 2, -1]),
                $this->mod('Bulldog Quick Sight', 'optic', 'Faster ADS speed for burst engagements.', [1, 0, 0, 1, 1, 1]),
            ], ['rifle'], imageUrl: 'https://media.valorant-api.com/weapons/ae3de142-4d85-2547-dd26-4e90bed35cf7/displayicon.png'),

            $this->item('Guardian', 'weapon', 'Sentinel', 'Defensive angle holder with area denial.', 80, [82, 48, 60, 88, 55], [
                $this->mod('Guardian Precision Lens', 'optic', 'Enhanced zoom clarity for long range.', [2, 1, 0, 0, 3, -2]),
                $this->mod('Guardian Heavy Barrel', 'barrel', 'Increased bullet velocity and range.', [2, 2, 0, 0, 1, -1]),
                $this->mod('Guardian Match Trigger', 'mechanism', 'Crisper semi-auto trigger pull.', [1, 1, 0, 0, 2, 0]),
            ], ['rifle'], imageUrl: 'https://media.valorant-api.com/weapons/4ade7faa-4cf1-8376-95ef-39884480959b/displayicon.png'),

            // ── SMGs ────────────────────────────────────────────────
            $this->item('Spectre', 'weapon', 'Controller', 'Map control and utility-focused playstyle.', 80, [68, 45, 75, 60, 85], [
                $this->mod('Spectre Laser Module', 'laser', 'Tighter hipfire spread while moving.', [1, 0, 0, 1, 1, 1]),
                $this->mod('Spectre Rapid Springs', 'mechanism', 'Increased fire rate for aggressive peeks.', [2, 2, -1, 0, -1, 1]),
                $this->mod('Spectre Foregrip', 'underbarrel', 'Better spray control at range.', [1, 0, 1, 0, 2, -1]),
            ], ['smg'], imageUrl: 'https://media.valorant-api.com/weapons/462080d1-4035-2937-7c09-27aa2a5c27a7/displayicon.png'),

            $this->item('Stinger', 'weapon', 'Flanker', 'Fast-rotating aggressor who exploits gaps in enemy setups.', 72, [60, 38, 65, 52, 90], [
                $this->mod('Stinger Rapid Fire', 'mechanism', 'Maximum fire rate for close-range dominance.', [2, 2, -1, 0, -2, 1]),
                $this->mod('Stinger Laser Sight', 'laser', 'Tighter hipfire for run-and-gun play.', [1, 0, 0, 1, 1, 1]),
                $this->mod('Stinger Light Stock', 'stock', 'Faster movement speed while equipped.', [1, 0, 0, 0, -1, 3]),
            ], ['smg'], imageUrl: 'https://media.valorant-api.com/weapons/f7e1b454-4ad4-1063-ec0a-159e56b58941/displayicon.png'),

            // ── Snipers ─────────────────────────────────────────────
            $this->item('Operator', 'weapon', 'Sentinel', 'Defensive angle holder with area denial.', 82, [99, 30, 45, 95, 35], [
                $this->mod('Operator Precision Lens', 'optic', 'Enhanced zoom clarity and contrast.', [2, 1, 0, 0, 3, -2]),
                $this->mod('Operator Steady Stock', 'stock', 'Reduced scope sway while holding angles.', [1, 0, 1, 0, 2, -1]),
                $this->mod('Operator Quick Bolt', 'mechanism', 'Faster re-chamber time between shots.', [2, 1, 0, 0, -1, 1]),
            ], ['sniper'], imageUrl: 'https://media.valorant-api.com/weapons/a03b24d3-4319-996d-0f8c-94bbfba1dfc7/displayicon.png'),

            $this->item('Marshal', 'weapon', 'Flex Player', 'Versatile mid-range combatant who adapts to any situation.', 76, [85, 35, 50, 88, 65], [
                $this->mod('Marshal Quick Scope', 'optic', 'Faster scope-in time for aggressive peeking.', [1, 0, 0, 1, 1, 1]),
                $this->mod('Marshal Light Bolt', 'mechanism', 'Faster rechamber for follow-up shots.', [2, 1, 0, 0, -1, 1]),
                $this->mod('Marshal Ergonomic Stock', 'stock', 'Better movement speed while scoped.', [1, 0, 0, 0, 0, 2]),
            ], ['sniper'], imageUrl: 'https://media.valorant-api.com/weapons/c4883e50-4494-202c-3ec3-6b8a9284f00b/displayicon.png'),

            $this->item('Outlaw', 'weapon', 'Entry Fragger', 'Aggressive duelist playstyle with high kill potential.', 79, [92, 32, 48, 90, 55], [
                $this->mod('Outlaw Double Barrel', 'barrel', 'Two-round burst for devastating picks.', [2, 3, 0, 0, -1, 0]),
                $this->mod('Outlaw Stabilizer', 'underbarrel', 'Reduced sway between shots.', [1, 0, 1, 0, 2, -1]),
                $this->mod('Outlaw Tactical Scope', 'optic', 'Intermediate zoom for mid-range angles.', [1, 0, 0, 1, 2, 0]),
            ], ['sniper'], imageUrl: 'https://media.valorant-api.com/weapons/5f0aaf7a-4289-3998-d5ff-eb9a5cf7f854/displayicon.png'),

            // ── Shotguns ────────────────────────────────────────────
            $this->item('Judge', 'weapon', 'Controller', 'Map control and utility-focused playstyle.', 70, [78, 42, 55, 35, 65], [
                $this->mod('Judge Auto-Feed', 'mechanism', 'Full-auto fire for close-range dominance.', [2, 2, -1, 0, -1, 0]),
                $this->mod('Judge Laser Pointer', 'laser', 'Improved hipfire accuracy on shotguns.', [1, 0, 0, 1, 1, 0]),
                $this->mod('Judge Extended Tube', 'magazine', 'Extra shells for prolonged fights.', [1, 1, 0, 0, 0, -1]),
            ], ['shotgun'], imageUrl: 'https://media.valorant-api.com/weapons/ec845bf4-4f79-ddda-a3da-0db3774b2794/displayicon.png'),

            $this->item('Bucky', 'weapon', 'Flanker', 'Fast-rotating aggressor who exploits gaps in enemy setups.', 68, [82, 40, 50, 30, 70], [
                $this->mod('Bucky Tight Choke', 'muzzle', 'Right-click fires a tighter slug at range.', [2, 2, 0, 0, 2, 0]),
                $this->mod('Bucky Quick Pump', 'mechanism', 'Faster pump action between shots.', [1, 1, 0, 0, 0, 1]),
                $this->mod('Bucky Shell Holder', 'stock', 'Faster reload from shell holder attachment.', [1, 0, 0, 1, 0, 0]),
            ], ['shotgun'], imageUrl: 'https://media.valorant-api.com/weapons/910be174-449b-c412-ab22-d0873436b21b/displayicon.png'),

            // ── Sidearms ────────────────────────────────────────────
            $this->item('Sheriff', 'weapon', 'Sentinel', 'Defensive angle holder with area denial.', 78, [88, 35, 50, 85, 60], [
                $this->mod('Sheriff Iron Sight Plus', 'optic', 'Cleaner sight picture for precise shots.', [1, 0, 0, 1, 2, 0]),
                $this->mod('Sheriff Heavy Barrel', 'barrel', 'Increased damage falloff range.', [2, 2, 0, 0, 1, -1]),
                $this->mod('Sheriff Match Trigger', 'mechanism', 'Crisper trigger pull for first-shot accuracy.', [1, 1, 0, 0, 2, 0]),
            ], ['sidearm'], imageUrl: 'https://media.valorant-api.com/weapons/e336c6b8-418d-9340-d77f-7a9e4cfe0702/displayicon.png'),

            $this->item('Ghost', 'weapon', 'Flex Player', 'Versatile mid-range combatant who adapts to any situation.', 74, [72, 38, 58, 80, 70], [
                $this->mod('Ghost Suppressor', 'muzzle', 'No tracers and reduced audio signature.', [1, -1, 0, 2, 1, 0]),
                $this->mod('Ghost Extended Mag', 'magazine', 'Extra rounds for pistol rounds.', [1, 1, 0, 0, 0, -1]),
                $this->mod('Ghost Light Trigger', 'mechanism', 'Faster semi-auto fire rate.', [1, 1, 0, 0, 0, 1]),
            ], ['sidearm'], imageUrl: 'https://media.valorant-api.com/weapons/1baa85b4-4c70-1284-64bb-6481dfc3bb4e/displayicon.png'),

            $this->item('Classic', 'weapon', 'Controller', 'Map control and utility-focused playstyle.', 65, [55, 35, 60, 55, 78], [
                $this->mod('Classic Burst Mode', 'mechanism', 'Right-click fires 3-round burst.', [1, 1, 0, 0, -1, 0]),
                $this->mod('Classic Quick Draw', 'rear_grip', 'Faster weapon swap speed.', [1, 0, 0, 0, 0, 2]),
                $this->mod('Classic Laser Pointer', 'laser', 'Better hipfire accuracy on pistol.', [1, 0, 0, 1, 1, 0]),
            ], ['sidearm'], imageUrl: 'https://media.valorant-api.com/weapons/29a0cfab-485b-f5d5-779a-b59f85e204a8/displayicon.png'),

            $this->item('Frenzy', 'weapon', 'Flanker', 'Fast-rotating aggressor who exploits gaps in enemy setups.', 66, [58, 32, 55, 48, 85], [
                $this->mod('Frenzy Auto Fire', 'mechanism', 'Full-auto pistol for spray and pray.', [2, 2, -1, 0, -2, 1]),
                $this->mod('Frenzy Extended Clip', 'magazine', 'More rounds for full-auto bursts.', [1, 1, 0, 0, 0, -1]),
                $this->mod('Frenzy Quick Grip', 'rear_grip', 'Faster equip time from swap.', [1, 0, 0, 0, 0, 2]),
            ], ['sidearm'], imageUrl: 'https://media.valorant-api.com/weapons/44d4e95c-4157-0037-81b2-17841bf2e8e3/displayicon.png'),

            $this->item('Shorty', 'weapon', 'Flanker', 'Fast-rotating aggressor who exploits gaps in enemy setups.', 60, [75, 30, 45, 20, 82], [
                $this->mod('Shorty Quick Draw', 'mechanism', 'Instant equip for surprise encounters.', [1, 0, 0, 1, 0, 2]),
                $this->mod('Shorty Tight Spread', 'muzzle', 'Tighter pellet spread per shot.', [1, 1, 0, 0, 1, 0]),
                $this->mod('Shorty Backup Holster', 'rear_grip', 'Fast swap as secondary weapon.', [1, 0, 0, 0, 0, 2]),
            ], ['sidearm'], imageUrl: 'https://media.valorant-api.com/weapons/42da8ccc-40d5-affc-beec-15aa47b42eda/displayicon.png'),

            // ── Heavy ───────────────────────────────────────────────
            $this->item('Odin', 'weapon', 'Controller', 'Map control and utility-focused playstyle.', 79, [75, 55, 72, 55, 42], [
                $this->mod('Odin Thermal Scope', 'optic', 'Detects heat signatures through visual clutter.', [2, 0, 0, 2, 2, -1]),
                $this->mod('Odin Extended Belt', 'magazine', 'Massive 100-round belt for wall-banging.', [1, 1, 1, 0, 0, -2]),
                $this->mod('Odin Reinforced Barrel', 'barrel', 'Better accuracy during sustained fire.', [1, 0, 1, 0, 2, -1]),
            ], ['heavy'], imageUrl: 'https://media.valorant-api.com/weapons/63e6c2b6-4a8e-869c-3d4c-e38355226584/displayicon.png'),

            $this->item('Ares', 'weapon', 'Controller', 'Map control and utility-focused playstyle.', 75, [70, 50, 68, 48, 48], [
                $this->mod('Ares Wind-Up Motor', 'mechanism', 'Fire rate increases over sustained fire.', [2, 2, 0, 0, -1, 0]),
                $this->mod('Ares Stabilizer Grip', 'underbarrel', 'Reduced recoil during wind-up phase.', [1, 0, 1, 0, 2, -1]),
                $this->mod('Ares Extended Belt', 'magazine', 'Extra ammo for prolonged suppression.', [1, 1, 0, 0, 0, -1]),
            ], ['heavy'], imageUrl: 'https://media.valorant-api.com/weapons/55d8a0f4-4274-ca67-fe2c-06ab45efdf58/displayicon.png'),
        ];
    }
}
