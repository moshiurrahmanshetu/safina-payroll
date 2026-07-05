<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  protected $casts = [
    'hourly_rate' => 'decimal:2',
    'base_price' => 'decimal:2',
  ];

  public function parkingTickets()
  {
    return $this->hasMany(ParkingTicket::class);
  }
}
