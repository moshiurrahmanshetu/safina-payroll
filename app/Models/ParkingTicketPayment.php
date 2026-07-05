<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingTicketPayment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the parking ticket that owns the payment.
     */
    public function parkingTicket()
    {
        return $this->belongsTo(ParkingTicket::class, 'parking_ticket_number', 'ticket_number');
    }

    /**
     * Get the user who created the payment record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parking counter where the payment was collected.
     */
    public function parkingCounter()
    {
        return $this->belongsTo(ParkingCounter::class, 'parking_counter_id');
    }
}
