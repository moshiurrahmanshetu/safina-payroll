<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical' => 'decimal:2',
        'transport' => 'decimal:2',
        'food' => 'decimal:2',
        'mobile' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'festival_bonus' => 'decimal:2',
        'late_fine' => 'decimal:2',
        'absent_deduction' => 'decimal:2',
        'advance_salary' => 'decimal:2',
        'tax' => 'decimal:2',
        'pf' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
