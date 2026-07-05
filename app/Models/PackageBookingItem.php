<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PackageBookingItem - Represents a ticket item in a package sale
 *
 * Note: service_id column stores ticket_id (sellable tickets)
 * This is a POS ticket sale item reference - NOT a booking service
 * source = 'package' for included tickets, 'extra' for additional tickets
 */
class PackageBookingItem extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    public function packageBooking()
    {
        return $this->belongsTo(PackageBooking::class);
    }

    /**
     * Ticket relationship
     * service_id column = ticket_id (reference to sellable tickets)
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'service_id');
    }

    /**
     * Get ticket_id (accessor for clarity)
     */
    public function getTicketIdAttribute()
    {
        return $this->service_id;
    }
}
