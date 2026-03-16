<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GameDraftCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_draft_id',
        'label',
        'slug',
        'description',
    ];

    public function gameDraft(): BelongsTo
    {
        return $this->belongsTo(GameDraft::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            GameDraftItem::class,
            'game_draft_item_categories',
            'game_draft_category_id',
            'game_draft_item_id'
        );
    }
}
