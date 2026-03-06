<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentItemSlot extends Model
{
    use HasFactory;

    protected $table = 'item_slots';

    protected $fillable = [
        'item_id',
        'slot_key',
        'slot_label',
        'accepted_modifier_type',
        'sort_order',
        'is_required',
        'metadata',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'metadata' => 'array',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(EquipmentItem::class, 'item_id');
    }
}
