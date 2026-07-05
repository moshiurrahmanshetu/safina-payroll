<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockerGearTicketItem extends Model
{
    protected $table = 'locker_gear_ticket_items';
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'quantity' => 'integer',
        'item_type' => 'string',
    ];

    /**
     * Get the ticket
     */
    public function ticket()
    {
        return $this->belongsTo(LockerGearTicket::class, 'ticket_number');
    }

    /**
     * Get the actual item (locker or gear)
     */
        public function locker()
        {
            return $this->belongsTo(LockerItem::class, 'item_id');
        }

        public function gear()
        {
            return $this->belongsTo(GearItem::class, 'item_id');
        }

}
