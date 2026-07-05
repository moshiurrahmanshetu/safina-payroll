<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterParkCounter extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Get users assigned to this counter
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'water_park_counter_user', 'water_park_counter_id', 'user_id');
    }

    /**
     * Get tickets issued from this counter
     */
    public function tickets()
    {
        return $this->hasMany(WaterParkTicket::class, 'water_park_counter_id');
    }
}
