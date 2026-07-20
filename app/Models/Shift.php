<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_cross_day',
        'late_grace_minutes',
        'early_leave_grace_minutes',
        'auto_checkout_after_minutes',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_cross_day' => 'boolean',
        'late_grace_minutes' => 'integer',
        'early_leave_grace_minutes' => 'integer',
        'auto_checkout_after_minutes' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
