<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContractWorker extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'contract_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($worker) {
            if (empty($worker->contract_worker_id)) {
                $worker->contract_worker_id = $worker->generateContractWorkerId();
            }
        });
    }

    /**
     * Generate unique contract worker ID
     * Format: CW + YYYY + 4-digit sequential number
     */
    public function generateContractWorkerId()
    {
        $year = now()->format('Y');
        $prefix = 'CW' . $year;
        
        // Get the last contract worker ID for this year
        $lastWorker = self::where('contract_worker_id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastWorker) {
            $lastNumber = intval(substr($lastWorker->contract_worker_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $contractWorkerId = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (self::where('contract_worker_id', $contractWorkerId)->exists()) {
            $newNumber++;
            $contractWorkerId = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
        
        return $contractWorkerId;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workArea()
    {
        return $this->belongsTo(WorkArea::class);
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
