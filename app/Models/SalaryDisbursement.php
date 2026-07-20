<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDisbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'payment_date',
        'payment_method',
        'reference_number',
        'amount',
        'payment_status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the payroll that owns the disbursement.
     */
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    /**
     * Get the employee (user) that owns the disbursement.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the user who created the disbursement.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the disbursement.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include paid disbursements.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    /**
     * Scope a query to only include pending disbursements.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'Pending');
    }

    /**
     * Scope a query to only include cancelled disbursements.
     */
    public function scopeCancelled($query)
    {
        return $query->where('payment_status', 'Cancelled');
    }

    /**
     * Scope a query to filter by payment method.
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Mark disbursement as paid.
     */
    public function markAsPaid()
    {
        $this->payment_status = 'Paid';
        $this->save();
    }

    /**
     * Mark disbursement as pending.
     */
    public function markAsPending()
    {
        $this->payment_status = 'Pending';
        $this->save();
    }

    /**
     * Mark disbursement as cancelled.
     */
    public function markAsCancelled()
    {
        $this->payment_status = 'Cancelled';
        $this->save();
    }

    /**
     * Check if disbursement is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'Paid';
    }

    /**
     * Check if disbursement is pending.
     */
    public function isPending()
    {
        return $this->payment_status === 'Pending';
    }

    /**
     * Check if disbursement is cancelled.
     */
    public function isCancelled()
    {
        return $this->payment_status === 'Cancelled';
    }
}
