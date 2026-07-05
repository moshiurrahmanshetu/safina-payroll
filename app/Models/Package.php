<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    public function items()
    {
        return $this->hasMany(PackageItem::class);
    }

    public function bookings()
    {
        return $this->hasMany(PackageBooking::class);
    }

    public function packageCounters()
    {
        return $this->belongsToMany(PackageCounter::class, 'package_counter_packages', 'package_id', 'package_counter_id');
    }
}
