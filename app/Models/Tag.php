<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
    ];

    public function equipmentItems(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentItem::class, 'equipment_item_tags', 'tag_id', 'equipment_item_id');
    }
}
