<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EquipmentItem extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'game_id',
        'name',
        'slug',
        'external_ref',
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'item_categories', 'item_id', 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->categories();
    }

    public function modifiers(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentModifier::class,
            'item_modifiers',
            'item_id',
            'modifier_id'
        );
    }

    public function compatibleModifiers(): BelongsToMany
    {
        return $this->modifiers();
    }

    public function itemSlots()
    {
        return $this->hasMany(EquipmentItemSlot::class, 'item_id')->orderBy('sort_order');
    }

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_items', 'item_id', 'build_id')
            ->withPivot('position');
    }
}
