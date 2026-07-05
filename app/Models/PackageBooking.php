<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageBooking extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'date' => 'date',
        'ticket_data' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function items()
    {
        return $this->hasMany(PackageBookingItem::class);
    }

    // public function counter()
    // {
    //     return $this->belongsTo(Counter::class);
    // }

    public function packageCounter()
    {
        return $this->belongsTo(PackageCounter::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique QR code for this booking
     */
    public static function generateQrCode()
    {
        return 'PKG' . strtoupper(uniqid() . bin2hex(random_bytes(4)));
    }

    /**
     * Mark booking as used
     */
    public function markAsUsed()
    {
        $this->is_used = true;
        $this->used_at = now();
        return $this->save();
    }

    /**
     * Generate unique booking token
     */
    public static function generateBookingToken()
    {
        return 'PKG' . strtoupper(uniqid() . bin2hex(random_bytes(4)));
    }

    /**
     * Generate unique ticket token
     */
    public static function generateTicketToken()
    {
        return 'PKT' . strtoupper(uniqid() . bin2hex(random_bytes(4)));
    }

    /**
     * Generate ticket data JSON for package booking
     */
    public static function generateTicketData($package, $quantity)
    {
        $tickets = [];

        // Generate tickets for package items
        foreach ($package->items as $item) {
            for ($i = 0; $i < $quantity; $i++) {
                $tickets[] = [
                    'ticket_id' => $item->ticket_id,
                    'ticket_name' => $item->ticket->name ?? 'Unknown',
                    'ticket_token' => self::generateTicketToken(),
                    'is_used' => false,
                    'used_at' => null,
                    'source' => 'package'
                ];
            }
        }

        return ['tickets' => $tickets];
    }

    /**
     * Find ticket by token in ticket_data
     */
    public function findTicketByToken($token)
    {
        if (!$this->ticket_data || !isset($this->ticket_data['tickets'])) {
            return null;
        }

        foreach ($this->ticket_data['tickets'] as $index => $ticket) {
            if ($ticket['ticket_token'] === $token) {
                return $ticket;
            }
        }

        return null;
    }

    /**
     * Mark ticket as used by token
     */
    public function markTicketAsUsed($token)
    {
        if (!$this->ticket_data || !isset($this->ticket_data['tickets'])) {
            return false;
        }

        // Assign to temporary variable to avoid indirect modification error
        $ticketData = $this->ticket_data;

        foreach ($ticketData['tickets'] as $index => $ticket) {
            if ($ticket['ticket_token'] === $token) {
                $ticketData['tickets'][$index]['is_used'] = true;
                $ticketData['tickets'][$index]['used_at'] = now()->toDateTimeString();
                // Reassign the full array back to the model
                $this->ticket_data = $ticketData;
                $this->save();
                return true;
            }
        }

        return false;
    }
}
