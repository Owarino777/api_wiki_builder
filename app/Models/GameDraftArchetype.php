<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameDraftArchetype extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_draft_id',
        'name',
        'slug',
        'description',
    ];

    public function gameDraft(): BelongsTo
    {
        return $this->belongsTo(GameDraft::class);
    }
}
