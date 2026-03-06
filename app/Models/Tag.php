<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'label',
        'slug',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentItem::class, 'item_categories', 'category_id', 'item_id');
    }

    public function equipmentItems(): BelongsToMany
    {
        return $this->items();
    }
}
