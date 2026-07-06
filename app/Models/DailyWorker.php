<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyWorker extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'joining_date' => 'date',
        'daily_wage' => 'decimal:2',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($worker) {
            if (empty($worker->worker_id)) {
                $worker->worker_id = $worker->generateWorkerId();
            }
        });
    }

    /**
     * Generate unique worker ID
     * Format: DW + YYYY + 4-digit sequential number
     */
    public function generateWorkerId()
    {
        $year = now()->format('Y');
        $prefix = 'DW' . $year;
        
        // Get the last worker ID for this year
        $lastWorker = self::where('worker_id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastWorker) {
            $lastNumber = intval(substr($lastWorker->worker_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $workerId = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (self::where('worker_id', $workerId)->exists()) {
            $newNumber++;
            $workerId = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
        
        return $workerId;
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
