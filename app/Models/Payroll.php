<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'generated_salary' => 'decimal:2',
        'attendance_adjustment' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
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
