<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GearItem extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'total_stock' => 'integer',
        'available_stock' => 'integer',
    ];

    /**
     * Get pricing for this gear item
     */
    public function pricing()
    {
        return $this->hasOne(ItemPricing::class, 'item_id')->where('item_type', 'gear');
    }
}
