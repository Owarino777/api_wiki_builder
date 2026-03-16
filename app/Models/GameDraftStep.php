<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameDraftStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_draft_id',
        'step_key',
        'status',
        'blocking_errors_count',
        'warnings_count',
        'last_saved_at',
        'last_validated_at',
        'payload_snapshot',
    ];

    protected $casts = [
        'blocking_errors_count' => 'integer',
        'warnings_count' => 'integer',
        'last_saved_at' => 'datetime',
        'last_validated_at' => 'datetime',
        'payload_snapshot' => 'array',
    ];

    public function gameDraft(): BelongsTo
    {
        return $this->belongsTo(GameDraft::class);
    }
}
