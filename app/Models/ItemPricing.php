<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPricing extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'duration_minutes' => 'integer',
        'base_price' => 'decimal:2',
        'extra_unit_minutes' => 'integer',
        'extra_unit_price' => 'decimal:2',
    ];

    /**
     * Get the item (locker or gear)
     */
    public function item()
    {
        if ($this->item_type === 'locker') {
            return $this->belongsTo(LockerItem::class, 'item_id');
        }
        return $this->belongsTo(GearItem::class, 'item_id');
    }
}
