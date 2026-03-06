<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'genre',
    ];

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

    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }
}
