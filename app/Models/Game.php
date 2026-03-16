<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'name',
        'slug',
        'genre',
        'creation_category',
        'source_type',
        'visibility',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function archetypes(): HasMany
    {
        return $this->hasMany(BuildProfile::class);
    }

    public function buildProfiles(): HasMany
    {
        return $this->archetypes();
    }

    public function items(): HasMany
    {
        return $this->hasMany(EquipmentItem::class);
    }

    public function equipmentItems(): HasMany
    {
        return $this->items();
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(EquipmentModifier::class);
    }

    public function equipmentModifiers(): HasMany
    {
        return $this->modifiers();
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }

    public function publishedDrafts(): HasMany
    {
        return $this->hasMany(GameDraft::class, 'published_game_id');
    }
}
