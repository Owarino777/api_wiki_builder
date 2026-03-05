<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EquipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'slot',
        'rarity',
        'tier_level',
        'score',
        'attack',
        'defense',
        'utility',
        'accuracy',
        'mobility',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'equipment_item_tags', 'equipment_item_id', 'tag_id');
    }

    public function compatibleModifiers(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentModifier::class,
            'equipment_item_compatible_modifiers',
            'equipment_item_id',
            'equipment_modifier_id'
        );
    }

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_equipment_items', 'equipment_item_id', 'build_id')
            ->withPivot('position');
    }
}
