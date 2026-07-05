<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  /**
   * Get users assigned to this gate (many-to-many)
   */
  public function users(){
    return $this->belongsToMany(User::class, 'user_gates', 'gate_id', 'user_id');
  }

  public function ticketSales(){
    return $this->hasMany(TicketSale::class);
  }

  public function tickets(){
    return $this->belongsToMany(Ticket::class, 'gate_tickets', 'gate_id', 'ticket_id');
  }
}
