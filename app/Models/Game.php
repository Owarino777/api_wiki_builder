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

    public function buildProfiles(): HasMany
    {
        return $this->hasMany(BuildProfile::class);
    }

    public function equipmentItems(): HasMany
    {
        return $this->hasMany(EquipmentItem::class);
    }

    public function equipmentModifiers(): HasMany
    {
        return $this->hasMany(EquipmentModifier::class);
    }

    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }
}
