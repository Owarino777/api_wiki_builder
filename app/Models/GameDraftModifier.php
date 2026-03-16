<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GameDraftModifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_draft_id',
        'name',
        'slug',
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

    public function gameDraft(): BelongsTo
    {
        return $this->belongsTo(GameDraft::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            GameDraftItem::class,
            'game_draft_item_modifiers',
            'game_draft_modifier_id',
            'game_draft_item_id'
        );
    }
}
