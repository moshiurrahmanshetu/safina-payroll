<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockerGearCounter extends Model
{
    protected $fillable = ['name', 'status'];

    /**
     * Get users assigned to this counter
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'locker_gear_counter_user', 'locker_gear_counter_id', 'user_id');
    }
    public function lockerGearTickets()
        {
            return $this->hasMany(
                LockerGearTicket::class,
                'locker_gear_counter_id',
                'id'
            );
        }
}
