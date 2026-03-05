<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EquipmentModifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'modifier_type',
        'description',
        'delta_score',
        'delta_attack',
        'delta_defense',
        'delta_utility',
        'delta_accuracy',
        'delta_mobility',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function compatibleItems(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentItem::class,
            'equipment_item_compatible_modifiers',
            'equipment_modifier_id',
            'equipment_item_id'
        );
    }

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_equipment_modifiers', 'equipment_modifier_id', 'build_id')
            ->withPivot('equipment_item_id');
    }
}
