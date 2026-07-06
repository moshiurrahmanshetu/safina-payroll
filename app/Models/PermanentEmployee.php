<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PermanentEmployee extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'employment_status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->employee_id = $employee->generateEmployeeId();
            }
        });
    }

    /**
     * Generate unique employee ID
     * Format: EMP + YYYY + 4-digit sequential number
     */
    public function generateEmployeeId()
    {
        $year = now()->format('Y');
        $prefix = 'EMP' . $year;
        
        // Get the last employee ID for this year
        $lastEmployee = self::where('employee_id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $employeeId = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (self::where('employee_id', $employeeId)->exists()) {
            $newNumber++;
            $employeeId = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
        
        return $employeeId;
    }

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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
