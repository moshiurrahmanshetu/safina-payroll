<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    public function users()
    {
        return $this->belongsToMany(User::class, 'counter_user', 'counter_id', 'user_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'counter_services', 'counter_id', 'service_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
