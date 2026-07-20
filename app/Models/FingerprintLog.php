<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'punch_datetime',
        'punch_type',
        'device_id',
        'source',
        'import_batch',
        'processed',
        'processed_at',
        'status',
    ];

    protected $casts = [
        'punch_datetime' => 'datetime',
        'processed_at' => 'datetime',
        'processed' => 'boolean',
    ];

    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }

    public function scopePending($query)
    {
        return $query->where('processed', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeByBatch($query, $batch)
    {
        return $query->where('import_batch', $batch);
    }

    public function scopeByEmployeeCode($query, $code)
    {
        return $query->where('employee_code', $code);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('punch_datetime', [$startDate, $endDate]);
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
