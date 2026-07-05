<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCounter extends Model
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
        return $this->belongsToMany(User::class, 'package_counter_user', 'package_counter_id', 'user_id');
    }

    /**
     * Get packages assigned to this counter
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_counter_packages', 'package_counter_id', 'package_id');
    }

    /**
     * Get package bookings created at this counter
     */
    public function packageBookings()
    {
        return $this->hasMany(PackageBooking::class);
    }
}
