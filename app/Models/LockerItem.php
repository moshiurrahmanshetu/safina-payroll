<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockerItem extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get pricing for this locker
     */
    public function pricing()
    {
        return $this->hasOne(ItemPricing::class, 'item_id')->where('item_type', 'locker');
    }
}
