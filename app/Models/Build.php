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
        'build_profile_id',
        'title',
        'description',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buildProfile(): BelongsTo
    {
        return $this->belongsTo(BuildProfile::class);
    }

    public function equipmentItems(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentItem::class, 'build_equipment_items', 'build_id', 'equipment_item_id')
            ->withPivot('position');
    }

    public function equipmentModifiers(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentModifier::class,
            'build_equipment_modifiers',
            'build_id',
            'equipment_modifier_id'
        )->withPivot('equipment_item_id');
    }
}
