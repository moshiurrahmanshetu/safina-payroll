<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockerGearTicket extends Model
{
    protected $table = 'locker_gear_tickets';
    protected $primaryKey = 'ticket_number';
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];
    protected $keyType = 'string';

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'extra_amount' => 'decimal:2',
        'extra_collected_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get items associated with this ticket
     */
    public function items()
    {
        return $this->hasMany(LockerGearTicketItem::class, 'ticket_number');
    }

    /**
     * Get the user who created this ticket
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the water park counter for this ticket
     */
    public function waterParkCounter()
    {
        return $this->belongsTo(WaterParkCounter::class, 'water_park_counter_id');
    }

    /**
     * Get the locker & gear counter for this ticket
     */
    public function lockerGearCounter()
    {
        return $this->belongsTo(LockerGearCounter::class, 'locker_gear_counter_id');
    }

    /**
     * Get the user who collected extra payment at checkout
     */
    public function extraCollector()
    {
        return $this->belongsTo(User::class, 'extra_collected_by');
    }

    /**
     * Get the counter where extra payment was collected
     */
    public function extraCollectedCounter()
    {
        return $this->belongsTo(LockerGearCounter::class, 'extra_collected_counter_id');
    }
}
