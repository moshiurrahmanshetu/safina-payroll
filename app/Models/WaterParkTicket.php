<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterParkTicket extends Model
{
    protected $primaryKey = 'ticket_number';
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'price' => 'decimal:2',
        'extra_amount' => 'decimal:2',
    ];

    /**
     * Get the water park counter where ticket was issued
     */
    public function waterParkCounter()
    {
        return $this->belongsTo(WaterParkCounter::class, 'water_park_counter_id');
    }

    /**
     * Get the user who created this ticket
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
