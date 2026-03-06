<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Build extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'archetype_id',
        'title',
        'description',
        'share_token',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function archetype(): BelongsTo
    {
        return $this->belongsTo(BuildProfile::class, 'archetype_id');
    }

    public function buildProfile(): BelongsTo
    {
        return $this->archetype();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentItem::class, 'build_items', 'build_id', 'item_id')
            ->withPivot('position');
    }

    public function equipmentItems(): BelongsToMany
    {
        return $this->items();
    }

    public function modifiers(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentModifier::class,
            'build_slots',
            'build_id',
            'modifier_id'
        )->withPivot('item_id', 'item_slot_id');
    }

    public function equipmentModifiers(): BelongsToMany
    {
        return $this->modifiers();
    }
}
