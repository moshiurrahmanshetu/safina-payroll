<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_default' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'shift_id',
        'effective_from',
        'effective_to',
        'is_default',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Shift
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Relationship to creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active records
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to get only default shifts
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get shifts effective on a specific date
     */
    public function scopeEffectiveOn($query, $date)
    {
        return $query->where('effective_from', '<=', $date)
                     ->where(function ($q) use ($date) {
                         $q->whereNull('effective_to')
                           ->orWhere('effective_to', '>=', $date);
                     });
    }

    /**
     * Check if shift is currently active based on today's date
     */
    public function isCurrent()
    {
        $today = now()->toDateString();
        return $this->effective_from <= $today &&
               ($this->effective_to === null || $this->effective_to >= $today);
    }

    /**
     * Check if shift is in future
     */
    public function isFuture()
    {
        $today = now()->toDateString();
        return $this->effective_from > $today;
    }

    /**
     * Check if shift is expired
     */
    public function isExpired()
    {
        if ($this->effective_to === null) {
            return false;
        }
        $today = now()->toDateString();
        return $this->effective_to < $today;
    }

    /**
     * Get current status badge
     */
    public function getCurrentStatusBadge()
    {
        if ($this->status !== 'Active') {
            return '<span class="badge badge-secondary">Inactive</span>';
        }

        if ($this->isCurrent()) {
            return '<span class="badge badge-success">Current</span>';
        }

        if ($this->isFuture()) {
            return '<span class="badge badge-info">Future</span>';
        }

        if ($this->isExpired()) {
            return '<span class="badge badge-light">Expired</span>';
        }

        return '<span class="badge badge-light">-</span>';
    }
}
