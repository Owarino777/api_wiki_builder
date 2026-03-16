<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'published_game_id',
        'category_key',
        'template_version',
        'status',
        'current_step',
        'name',
        'slug',
        'description',
        'visibility',
        'completion_percent',
        'last_validated_at',
        'published_at',
        'settings',
        'meta',
    ];

    protected $casts = [
        'completion_percent' => 'integer',
        'last_validated_at' => 'datetime',
        'published_at' => 'datetime',
        'settings' => 'array',
        'meta' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function publishedGame(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'published_game_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(GameDraftStep::class);
    }

    public function archetypes(): HasMany
    {
        return $this->hasMany(GameDraftArchetype::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(GameDraftCategory::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GameDraftItem::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(GameDraftModifier::class);
    }
}
