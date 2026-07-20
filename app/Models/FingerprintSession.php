<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'shift_id',
        'first_in',
        'last_out',
        'total_punch',
        'status',
        'source',
        'processed',
        'processed_at',
        'remarks',
    ];

    protected $casts = [
        'first_in' => 'datetime',
        'last_out' => 'datetime',
        'processed_at' => 'datetime',
        'processed' => 'boolean',
        'attendance_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }

    public function scopePending($query)
    {
        return $query->where('processed', false);
    }

    public function scopeByEmployee($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    public function markAsProcessed()
    {
        $this->update([
            'processed' => true,
            'processed_at' => now(),
        ]);
    }

    public function getProcessedBadgeAttribute()
    {
        if ($this->processed) {
            return '<span class="badge badge-success">Processed</span>';
        }
        return '<span class="badge badge-warning">Pending</span>';
    }
}
