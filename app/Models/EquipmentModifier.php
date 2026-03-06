<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EquipmentModifier extends Model
{
    use HasFactory;

    protected $table = 'modifiers';

    protected $fillable = [
        'game_id',
        'name',
        'slug',
        'external_ref',
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

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentItem::class,
            'item_modifiers',
            'modifier_id',
            'item_id'
        );
    }

    public function compatibleItems(): BelongsToMany
    {
        return $this->items();
    }

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_slots', 'modifier_id', 'build_id')
            ->withPivot('item_id', 'item_slot_id');
    }
}
