<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMonth extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'attendance_json' => 'array',
        'attendance_locked' => 'boolean',
        'summary_present' => 'integer',
        'summary_late' => 'integer',
        'summary_halfday' => 'integer',
        'summary_absent' => 'integer',
        'summary_leave' => 'integer',
        'summary_holiday' => 'integer',
        'summary_weekly_off' => 'integer',
    ];

    protected $fillable = [
        'user_id',
        'shift_id',
        'attendance_month',
        'attendance_json',
        'summary_present',
        'summary_late',
        'summary_halfday',
        'summary_absent',
        'summary_leave',
        'summary_holiday',
        'summary_weekly_off',
        'attendance_locked',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Shift
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Relationship to creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Update attendance for a specific day
     * This method automatically recalculates all totals from JSON
     *
     * @param string $day Day in format '01' to '31'
     * @param array $data Attendance data for the day
     * @return bool
     */
    public function updateDayAttendance($day, $data)
    {
        if ($this->attendance_locked) {
            return false; // Cannot update locked attendance
        }

        $attendanceJson = $this->attendance_json ?? [];
        $attendanceJson[$day] = $data;
        $this->attendance_json = $attendanceJson;

        // Recalculate all totals from JSON
        $this->recalculateTotals();

        return $this->save();
    }

    /**
     * Recalculate all totals from attendance_json
     * This ensures totals always match the JSON data
     *
     * @return void
     */
    public function recalculateTotals()
    {
        $attendanceJson = $this->attendance_json ?? [];

        $totals = [
            'Present' => 0,
            'Late' => 0,
            'Half Day' => 0,
            'Absent' => 0,
            'Leave' => 0,
            'Holiday' => 0,
            'Weekly Off' => 0,
        ];

        foreach ($attendanceJson as $dayData) {
            if (isset($dayData['status']) && isset($totals[$dayData['status']])) {
                $totals[$dayData['status']]++;
            }
        }

        $this->summary_present = $totals['Present'];
        $this->summary_late = $totals['Late'];
        $this->summary_halfday = $totals['Half Day'];
        $this->summary_absent = $totals['Absent'];
        $this->summary_leave = $totals['Leave'];
        $this->summary_holiday = $totals['Holiday'];
        $this->summary_weekly_off = $totals['Weekly Off'];
    }

    /**
     * Get attendance for a specific day
     *
     * @param string $day Day in format '01' to '31'
     * @return array|null
     */
    public function getDayAttendance($day)
    {
        $attendanceJson = $this->attendance_json ?? [];
        return $attendanceJson[$day] ?? null;
    }

    /**
     * Lock attendance for this month
     *
     * @return bool
     */
    public function lock()
    {
        $this->attendance_locked = true;
        return $this->save();
    }

    /**
     * Unlock attendance for this month
     *
     * @return bool
     */
    public function unlock()
    {
        $this->attendance_locked = false;
        return $this->save();
    }

    /**
     * Check if attendance is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->attendance_locked;
    }
}
