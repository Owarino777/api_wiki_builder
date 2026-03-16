<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'game_id',
        'label',
        'slug',
        'source_type',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentItem::class, 'item_categories', 'category_id', 'item_id');
    }

    public function equipmentItems(): BelongsToMany
    {
        return $this->items();
    }
}
