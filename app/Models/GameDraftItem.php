<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameDraftItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_draft_id',
        'name',
        'slug',
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

    public function gameDraft(): BelongsTo
    {
        return $this->belongsTo(GameDraft::class);
    }

    public function itemSlots(): HasMany
    {
        return $this->hasMany(GameDraftItemSlot::class)->orderBy('sort_order');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            GameDraftCategory::class,
            'game_draft_item_categories',
            'game_draft_item_id',
            'game_draft_category_id'
        );
    }

    public function modifiers(): BelongsToMany
    {
        return $this->belongsToMany(
            GameDraftModifier::class,
            'game_draft_item_modifiers',
            'game_draft_item_id',
            'game_draft_modifier_id'
        );
    }
}
