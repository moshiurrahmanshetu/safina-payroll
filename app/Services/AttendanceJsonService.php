<?php

namespace App\Services;

use App\Models\AttendanceMonth;
use App\Models\User;
use Carbon\Carbon;

class AttendanceJsonService
{
    /**
     * Create an empty month structure for a user
     *
     * @param int $userId
     * @param string $attendanceMonth Format: YYYY-MM
     * @param int|null $shiftId
     * @return array
     */
    public function createEmptyMonth($userId, $attendanceMonth, $shiftId = null)
    {
        $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
        $emptyJson = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::parse($attendanceMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
            $dateKey = $date->format('Y-m-d');

            $emptyJson[$dateKey] = [
                'shift_id' => $shiftId,
                'status' => '',
                'check_in' => '',
                'check_out' => '',
                'worked_minutes' => 0,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'is_late' => false,
                'is_auto_checkout' => false,
                'remark' => '',
            ];
        }

        return $emptyJson;
    }

    /**
     * Load attendance month for a user
     * Creates empty structure if not exists
     *
     * @param int $userId
     * @param string $attendanceMonth Format: YYYY-MM
     * @param int|null $shiftId
     * @return AttendanceMonth
     */
    public function loadMonth($userId, $attendanceMonth, $shiftId = null)
    {
        $attendanceMonth = AttendanceMonth::where('user_id', $userId)
                                          ->where('attendance_month', $attendanceMonth)
                                          ->first();

        if (!$attendanceMonth) {
            $emptyJson = $this->createEmptyMonth($userId, $attendanceMonth, $shiftId);

            $attendanceMonth = AttendanceMonth::create([
                'user_id' => $userId,
                'shift_id' => $shiftId,
                'attendance_month' => $attendanceMonth,
                'attendance_json' => $emptyJson,
                'summary_present' => 0,
                'summary_late' => 0,
                'summary_halfday' => 0,
                'summary_absent' => 0,
                'summary_leave' => 0,
                'summary_holiday' => 0,
                'summary_weekly_off' => 0,
                'attendance_locked' => false,
            ]);
        }

        return $attendanceMonth;
    }

    /**
     * Save attendance for a specific day
     *
     * @param AttendanceMonth $attendanceMonth
     * @param string $date Format: YYYY-MM-DD
     * @param array $dayData
     * @return bool
     */
    public function saveDay(AttendanceMonth $attendanceMonth, $date, $dayData)
    {
        if ($attendanceMonth->attendance_locked) {
            return false;
        }

        $attendanceJson = $attendanceMonth->attendance_json ?? [];
        $attendanceJson[$date] = $dayData;
        $attendanceMonth->attendance_json = $attendanceJson;

        $this->recalculateSummary($attendanceMonth);

        return $attendanceMonth->save();
    }

    /**
     * Recalculate summary from attendance_json
     *
     * @param AttendanceMonth $attendanceMonth
     * @return void
     */
    public function recalculateSummary(AttendanceMonth $attendanceMonth)
    {
        $attendanceJson = $attendanceMonth->attendance_json ?? [];

        $summary = [
            'Present' => 0,
            'Late' => 0,
            'Half Day' => 0,
            'Absent' => 0,
            'Leave' => 0,
            'Holiday' => 0,
            'Weekly Off' => 0,
        ];

        foreach ($attendanceJson as $dayData) {
            if (isset($dayData['status']) && isset($summary[$dayData['status']])) {
                $summary[$dayData['status']]++;
            }
        }

        $attendanceMonth->summary_present = $summary['Present'];
        $attendanceMonth->summary_late = $summary['Late'];
        $attendanceMonth->summary_halfday = $summary['Half Day'];
        $attendanceMonth->summary_absent = $summary['Absent'];
        $attendanceMonth->summary_leave = $summary['Leave'];
        $attendanceMonth->summary_holiday = $summary['Holiday'];
        $attendanceMonth->summary_weekly_off = $summary['Weekly Off'];
    }

    /**
     * Lock attendance month
     *
     * @param AttendanceMonth $attendanceMonth
     * @return bool
     */
    public function lockMonth(AttendanceMonth $attendanceMonth)
    {
        $attendanceMonth->attendance_locked = true;
        return $attendanceMonth->save();
    }

    /**
     * Unlock attendance month
     *
     * @param AttendanceMonth $attendanceMonth
     * @return bool
     */
    public function unlockMonth(AttendanceMonth $attendanceMonth)
    {
        $attendanceMonth->attendance_locked = false;
        return $attendanceMonth->save();
    }
}
