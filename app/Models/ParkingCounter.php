<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingCounter extends Model
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
        return $this->belongsToMany(User::class, 'parking_counter_user', 'parking_counter_id', 'user_id');
    }

    /**
     * Get parking tickets created at this counter
     */
    public function parkingTickets()
    {
        return $this->hasMany(ParkingTicket::class);
    }

}
