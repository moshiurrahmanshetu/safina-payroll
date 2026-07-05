<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function service()
  {
    return $this->belongsTo(Service::class);
  }

  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }
}
