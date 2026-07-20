<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');
    protected $fillable = [
        'user_id',
        'salary_id',
        'payroll_month',
        'generated_salary',
        'attendance_adjustment',
        'bonus',
        'deduction',
        'net_salary',
        'status',
        'remarks',
        'approval_status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'returned_at',
        'returned_by',
        'approval_remark',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'generated_salary' => 'decimal:2',
        'attendance_adjustment' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'status' => 'integer',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function attendanceMonth()
    {
        return $this->belongsTo(AttendanceMonth::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returner()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function disbursement()
    {
        return $this->hasOne(SalaryDisbursement::class);
    }

    /**
     * Get payment status attribute
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->disbursement) {
            return $this->disbursement->payment_status;
        }
        
        // If approval_status is 'Paid', return 'Paid'
        if ($this->approval_status === 'Paid') {
            return 'Paid';
        }
        
        // If approved but no disbursement, return 'Pending'
        if ($this->approval_status === 'approved') {
            return 'Pending';
        }
        
        // Otherwise return the approval status
        return $this->approval_status;
    }

    /**
     * Check if payroll is paid
     */
    public function isPaid()
    {
        return $this->approval_status === 'Paid' || ($this->disbursement && $this->disbursement->isPaid());
    }
}
