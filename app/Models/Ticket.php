<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($ticket) {
      if (empty($ticket->ticket_number)) {
        $ticket->ticket_number = $ticket->generateTicketNumber();
      }
    });
  }

  /**
   * Generate unique 16-digit ticket number
   * Format: YYYYMMDDHHMMSS + random 4 digits
   */
  public function generateTicketNumber()
  {
    $dateTime = now()->format('YmdHis'); // 14 digits: YYYYMMDDHHMMSS
    $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // 4 digits
    $ticketNumber = $dateTime . $random; // Total 18 digits

    // Ensure uniqueness
    while (self::where('ticket_number', $ticketNumber)->exists()) {
      $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
      $ticketNumber = $dateTime . $random;
    }

    return $ticketNumber;
  }

  public function gate(){
    return $this->belongsTo(Gate::class);
  }

  public function gates(){
    return $this->belongsToMany(Gate::class, 'gate_tickets', 'ticket_id', 'gate_id');
  }
}
