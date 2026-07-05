<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function service_category(){
    return $this->belongsTo(ServiceCategory::class);
  }

  public function bookings(){
    return $this->hasMany(Booking::class);
  }

  public function timeSlots(){
    return $this->hasMany(TimeSlot::class);
  }

  public function counters(){
    return $this->belongsToMany(Counter::class, 'counter_services', 'service_id', 'counter_id');
  }

  // Statistics Methods
  public function getTotalBookingsAttribute()
  {
    return $this->bookings()->count();
  }

  public function getConfirmedBookingsAttribute()
  {
    return $this->bookings()->where('status', 1)->count();
  }

  public function getPendingBookingsAttribute()
  {
    return $this->bookings()->where('status', 0)->count();
  }

  public function getCancelledBookingsAttribute()
  {
    return $this->bookings()->where('status', 2)->count();
  }

  public function getCompletedBookingsAttribute()
  {
    return $this->bookings()->where('status', 3)->count();
  }

  public function getTotalRevenueAttribute()
  {
    return $this->bookings()->where('status', 1)->sum('final_price');
  }

  public function getUpcomingBookingsAttribute()
  {
    return $this->bookings()
      ->where('status', 1)
      ->where('check_in_date', '>=', now()->format('Y-m-d'))
      ->count();
  }

  public function getAverageBookingValueAttribute()
  {
    $confirmedBookings = $this->bookings()->where('status', 1)->count();
    if ($confirmedBookings === 0) {
      return 0;
    }
    return $this->bookings()->where('status', 1)->avg('final_price');
  }

  public function getMostRecentBookingAttribute()
  {
    return $this->bookings()->orderBy('created_at', 'desc')->first();
  }

  public function getStatusSummaryAttribute()
  {
    return [
      'total' => $this->total_bookings,
      'confirmed' => $this->confirmed_bookings,
      'pending' => $this->pending_bookings,
      'cancelled' => $this->cancelled_bookings,
      'completed' => $this->completed_bookings,
    ];
  }

  // Availability Scope - filter services that have at least one available time slot for a given date
  public function scopeAvailableForDate($query, $date)
  {
    return $query->whereHas('timeSlots', function($slotQuery) use ($date) {
      $slotQuery->where('status', 1)
        ->whereDoesntHave('bookings', function($bookingQuery) use ($date) {
          $bookingQuery->where('check_in_date', $date)
            ->where('status', 1); // Only count confirmed bookings
        });
    });
  }
}
