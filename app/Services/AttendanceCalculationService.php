<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

class AttendanceCalculationService
{
    /**
     * Calculate day attendance based on shift and check-in/out times
     *
     * @param Shift $shift
     * @param string $attendanceDate Format: Y-m-d
     * @param string|null $checkIn Format: Y-m-d H:i:s or H:i:s
     * @param string|null $checkOut Format: Y-m-d H:i:s or H:i:s
     * @param string|null $manualStatus Manual status override (Holiday, Weekly Off, Leave)
     * @return array
     */
    public function calculateDayAttendance(Shift $shift, $attendanceDate, $checkIn = null, $checkOut = null, $manualStatus = null)
    {
        // Parse attendance date
        $date = Carbon::parse($attendanceDate);

        // Parse check-in and check-out times
        $checkInTime = $checkIn ? Carbon::parse($checkIn) : null;
        $checkOutTime = $checkOut ? Carbon::parse($checkOut) : null;

        // Calculate scheduled shift times
        $shiftStart = $this->calculateShiftStart($shift, $date);
        $shiftEnd = $this->calculateShiftEnd($shift, $date);

        // Handle auto checkout if check_out is null
        $isAutoCheckout = false;
        if ($checkOutTime === null && $checkInTime !== null && $shift->auto_checkout_after_minutes) {
            $checkOutTime = $checkInTime->copy()->addMinutes($shift->auto_checkout_after_minutes);
            $isAutoCheckout = true;
        }

        // Calculate worked minutes
        $workedMinutes = $this->calculateWorkedMinutes($checkInTime, $checkOutTime);

        // Calculate late minutes
        $lateMinutes = $this->calculateLateMinutes($checkInTime, $shiftStart, $shift->late_grace_minutes);

        // Calculate early leave minutes
        $earlyLeaveMinutes = $this->calculateEarlyLeaveMinutes($checkOutTime, $shiftEnd, $shift->early_leave_grace_minutes);

        // Calculate shift duration in minutes
        $shiftDuration = $this->calculateShiftDuration($shift);

        // Determine attendance status
        $status = $this->calculateStatus(
            $manualStatus,
            $checkInTime,
            $checkOutTime,
            $workedMinutes,
            $lateMinutes,
            $shiftDuration
        );

        // Generate remark if auto checkout
        $remark = '';
        if ($isAutoCheckout) {
            $remark = 'Did not checkout';
        }

        // Return calculated array matching attendance_json structure
        return [
            'shift_id' => $shift->id,
            'status' => $status,
            'check_in' => $checkInTime ? $checkInTime->format('H:i:s') : '',
            'check_out' => $checkOutTime ? $checkOutTime->format('H:i:s') : '',
            'worked_minutes' => $workedMinutes,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'is_late' => $lateMinutes > 0,
            'is_auto_checkout' => $isAutoCheckout,
            'remark' => $remark,
        ];
    }

    /**
     * Calculate shift start time for a given date
     *
     * @param Shift $shift
     * @param Carbon $date
     * @return Carbon
     */
    protected function calculateShiftStart(Shift $shift, Carbon $date)
    {
        return $date->copy()->setTimeFromTimeString($shift->start_time);
    }

    /**
     * Calculate shift end time for a given date
     * Handles cross-day shifts
     *
     * @param Shift $shift
     * @param Carbon $date
     * @return Carbon
     */
    protected function calculateShiftEnd(Shift $shift, Carbon $date)
    {
        $shiftEnd = $date->copy()->setTimeFromTimeString($shift->end_time);

        // If cross-day shift, move end time to next day
        if ($shift->is_cross_day) {
            $shiftEnd->addDay();
        }

        return $shiftEnd;
    }

    /**
     * Calculate worked minutes between check-in and check-out
     *
     * @param Carbon|null $checkIn
     * @param Carbon|null $checkOut
     * @return int
     */
    protected function calculateWorkedMinutes($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            return 0;
        }

        if ($checkOut->lte($checkIn)) {
            return 0;
        }

        return $checkIn->diffInMinutes($checkOut);
    }

    /**
     * Calculate late minutes
     * Late = Check In - Shift Start
     * Only counts if late > grace minutes
     *
     * @param Carbon|null $checkIn
     * @param Carbon $shiftStart
     * @param int $graceMinutes
     * @return int
     */
    protected function calculateLateMinutes($checkIn, Carbon $shiftStart, $graceMinutes = 0)
    {
        if (!$checkIn) {
            return 0;
        }

        if ($checkIn->lte($shiftStart)) {
            return 0;
        }

        $lateMinutes = $shiftStart->diffInMinutes($checkIn);

        // Only count late if exceeds grace period
        if ($lateMinutes <= $graceMinutes) {
            return 0;
        }

        return $lateMinutes;
    }

    /**
     * Calculate early leave minutes
     * Early Leave = Shift End - Check Out
     * Only counts if > grace minutes
     *
     * @param Carbon|null $checkOut
     * @param Carbon $shiftEnd
     * @param int $graceMinutes
     * @return int
     */
    protected function calculateEarlyLeaveMinutes($checkOut, Carbon $shiftEnd, $graceMinutes = 0)
    {
        if (!$checkOut) {
            return 0;
        }

        if ($checkOut->gte($shiftEnd)) {
            return 0;
        }

        $earlyLeaveMinutes = $checkOut->diffInMinutes($shiftEnd);

        // Only count early leave if exceeds grace period
        if ($earlyLeaveMinutes <= $graceMinutes) {
            return 0;
        }

        return $earlyLeaveMinutes;
    }

    /**
     * Calculate shift duration in minutes
     * Handles cross-day shifts
     *
     * @param Shift $shift
     * @return int
     */
    protected function calculateShiftDuration(Shift $shift)
    {
        $startTime = Carbon::parse($shift->start_time);
        $endTime = Carbon::parse($shift->end_time);

        if ($shift->is_cross_day) {
            $endTime->addDay();
        }

        return $startTime->diffInMinutes($endTime);
    }

    /**
     * Calculate attendance status based on priority rules
     * Priority: Holiday > Weekly Off > Leave > Absent > Half Day > Late > Present
     *
     * @param string|null $manualStatus
     * @param Carbon|null $checkIn
     * @param Carbon|null $checkOut
     * @param int $workedMinutes
     * @param int $lateMinutes
     * @param int $shiftDuration
     * @return string
     */
    protected function calculateStatus($manualStatus, $checkIn, $checkOut, $workedMinutes, $lateMinutes, $shiftDuration)
    {
        // Priority 1: Manual status (Holiday, Weekly Off, Leave)
        if ($manualStatus && in_array($manualStatus, ['Holiday', 'Weekly Off', 'Leave'])) {
            return $manualStatus;
        }

        // Priority 2: No check-in and no check-out
        if (!$checkIn && !$checkOut) {
            return 'Absent';
        }

        // Priority 3: Worked minutes <= 0
        if ($workedMinutes <= 0) {
            return 'Absent';
        }

        // Priority 4: Worked minutes less than 50% of shift duration
        if ($shiftDuration > 0 && $workedMinutes < ($shiftDuration * 0.5)) {
            return 'Half Day';
        }

        // Priority 5: Late minutes > 0
        if ($lateMinutes > 0) {
            return 'Late';
        }

        // Priority 6: Present
        return 'Present';
    }
}
