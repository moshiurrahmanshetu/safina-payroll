<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketSale extends Model
{
  protected $primaryKey = 'qr_code';
  public $incrementing = false;
  public $timestamps = true;
  protected $guarded = [];

  protected $casts = [
    'is_used' => 'boolean',
    'used_at' => 'datetime',
    'date' => 'date',
    'price' => 'decimal:2',
    'discount_amount' => 'decimal:2',
    'total_price' => 'decimal:2',
  ];

  public function ticket(){
    return $this->belongsTo(Ticket::class);
  }

  public function gate(){
    return $this->belongsTo(Gate::class);
  }

  public function creator(){
    return $this->belongsTo(User::class, 'created_by');
  }

  /**
   * Generate unique QR code token for this ticket
   */
  public static function generateQrCode()
  {
    return  strtoupper(uniqid() . bin2hex(random_bytes(4)));
  }

  /**
   * Generate unique sale group token for grouped ticket sales
   */
  public static function generateSaleGroupToken()
  {
    return strtoupper(uniqid() . bin2hex(random_bytes(4)));
  }

  /**
   * Mark ticket as used
   */
  public function markAsUsed()
  {
    $this->is_used = true;
    $this->used_at = now();
    return $this->save();
  }
}
