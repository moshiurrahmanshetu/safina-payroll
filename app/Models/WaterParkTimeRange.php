<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterParkTimeRange extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get tickets using this time range
     */
    public function tickets()
    {
        return $this->hasMany(WaterParkTicket::class, 'time_range_id');
    }
}
