<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ParkingTicket extends Model
{
  public $timestamps = true;
  protected $primaryKey = 'ticket_number';
  public $incrementing = false;
  protected $guarded = [];

  protected $casts = [
    'entry_time' => 'datetime',
    'exit_time' => 'datetime',
    'base_price' => 'decimal:2',
    'hourly_rate' => 'decimal:2',
    'total_amount' => 'decimal:2',
    'paid_amount' => 'decimal:2',
    'extra_amount' => 'decimal:2',
    'slot_multiplier' => 'integer',
    'total_hours' => 'integer',
    'total_minutes' => 'integer',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($parkingTicket) {
      if (empty($parkingTicket->ticket_number)) {
        $parkingTicket->ticket_number = $parkingTicket->generateTicketNumber();
      }
    });
  }

  /**
   * Generate unique parking ticket number
   * Format: PRK-YYYYMMDD-XXXX (PRK = Parking)
   */
  public function generateTicketNumber()
  {
    
    $date = time(); // YYYYMMDD
    $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $ticketNumber =  $random . $date;

    // Ensure uniqueness
    while (self::where('ticket_number', $ticketNumber)->exists()) {
      $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
      $ticketNumber =  $random . $date;
    }

    return $ticketNumber;
  }

  public function creator(){
    return $this->belongsTo(User::class, 'created_by');
  }

  public function vehicle(){
    return $this->belongsTo(Vehicle::class);
  }

  public function parkingCounter(){
    return $this->belongsTo(ParkingCounter::class);
  }
}
