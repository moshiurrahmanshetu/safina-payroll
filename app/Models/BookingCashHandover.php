<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCashHandover extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'business_date' => 'date',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the user who requested the handover.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the counter where the handover originated.
     */
    public function counter()
    {
        return $this->belongsTo(Counter::class, 'counter_id');
    }

    /**
     * Get the receiver user (manager).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    /**
     * Get the user who approved the handover.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the handover.
     */
    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
