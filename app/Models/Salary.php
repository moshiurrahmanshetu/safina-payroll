<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'basic_salary',
        'house_rent',
        'medical',
        'transport',
        'food',
        'mobile',
        'other_allowance',
        'festival_bonus',
        'late_fine',
        'absent_deduction',
        'advance_salary',
        'tax',
        'pf',
        'other_deduction',
        'effective_from',
        'salary_increment_reason',
        'remarks',
        'is_current',
        'revision_locked',
        'salary_locked',
        'gross_salary',
        'net_salary',
        'status',
        'created_by',
        'updated_by',
    ];

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
        'effective_from' => 'date',
        'is_current' => 'boolean',
        'revision_locked' => 'boolean',
        'salary_locked' => 'boolean',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
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

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function getTotalSalaryAttribute()
    {
        return $this->basic_salary + $this->house_rent + $this->medical + $this->transport + 
               $this->food + $this->mobile + $this->other_allowance + $this->festival_bonus;
    }

    public function getTotalDeductionAttribute()
    {
        return $this->late_fine + $this->absent_deduction + $this->advance_salary + 
               $this->tax + $this->pf + $this->other_deduction;
    }

    /**
     * Calculate and set gross_salary
     * Gross = Total Salary
     */
    public function calculateGrossSalary()
    {
        $this->gross_salary = $this->total_salary;
    }

    /**
     * Calculate and set net_salary
     * Net = Gross - Total Deduction
     */
    public function calculateNetSalary()
    {
        $this->net_salary = $this->gross_salary - $this->total_deduction;
    }

    /**
     * Auto-calculate gross and net salary before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($salary) {
            $salary->calculateGrossSalary();
            $salary->calculateNetSalary();
        });
    }

    /**
     * Lock salary - prevents any further changes to this salary record
     */
    public function lockSalary()
    {
        $this->salary_locked = true;
        return $this->save();
    }

    /**
     * Unlock salary - allows changes to this salary record
     */
    public function unlockSalary()
    {
        $this->salary_locked = false;
        return $this->save();
    }

    /**
     * Check if salary is locked
     */
    public function isSalaryLocked()
    {
        return $this->salary_locked;
    }

    /**
     * Lock revision - prevents editing this specific revision
     */
    public function lockRevision()
    {
        $this->revision_locked = true;
        return $this->save();
    }

    /**
     * Unlock revision - allows editing this specific revision
     */
    public function unlockRevision()
    {
        $this->revision_locked = false;
        return $this->save();
    }

    /**
     * Check if revision is locked
     */
    public function isRevisionLocked()
    {
        return $this->revision_locked;
    }
}
