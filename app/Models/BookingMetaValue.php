<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingMetaValue extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function booking(){
    return $this->belongsTo(Booking::class);
  }
}
