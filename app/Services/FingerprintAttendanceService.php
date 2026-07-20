<?php

namespace App\Services;

use App\Models\FingerprintSession;
use App\Models\AttendanceMonth;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class FingerprintAttendanceService
{
    protected $attendanceCalculationService;
    protected $attendanceJsonService;
    protected $employeeShiftService;

    public function __construct(
        AttendanceCalculationService $attendanceCalculationService,
        AttendanceJsonService $attendanceJsonService,
        EmployeeShiftService $employeeShiftService
    ) {
        $this->attendanceCalculationService = $attendanceCalculationService;
        $this->attendanceJsonService = $attendanceJsonService;
        $this->employeeShiftService = $employeeShiftService;
    }

    public function generateAttendance($userId = null, $date = null)
    {
        $query = FingerprintSession::pending()->orderBy('attendance_date', 'asc');

        if ($userId) {
            $query->byEmployee($userId);
        }

        if ($date) {
            $query->byDate($date);
        }

        $processed = 0;
        $skipped = 0;
        $failed = 0;

        // Batch processing - chunk 100 at a time
        $query->chunk(100, function ($sessions) use (&$processed, &$skipped, &$failed) {
            foreach ($sessions as $session) {
                $result = $this->processSession($session);
                if ($result === 'success') {
                    $processed++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                } else {
                    $failed++;
                }
            }
        });

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }

    protected function processSession(FingerprintSession $session)
    {
        // Check if attendance month is locked
        $attendanceMonth = Carbon::parse($session->attendance_date)->format('Y-m');
        $attendanceMonthRecord = AttendanceMonth::where('user_id', $session->user_id)
            ->where('attendance_month', $attendanceMonth)
            ->first();

        if ($attendanceMonthRecord && $attendanceMonthRecord->attendance_locked) {
            $session->update([
                'processed' => true,
                'processed_at' => now(),
                'status' => 'Skipped',
                'remarks' => 'Attendance Locked',
            ]);
            return 'skipped';
        }

        // Find employee shift
        $shift = $this->findEmployeeShift($session->user_id, $session->attendance_date);

        if (!$shift) {
            $session->update([
                'processed' => true,
                'processed_at' => now(),
                'status' => 'Skipped',
                'remarks' => 'No Shift Assigned',
            ]);
            return 'skipped';
        }

        // Build attendance data
        $attendanceData = $this->buildAttendance($session, $shift);

        if (!$attendanceData) {
            return 'failed';
        }

        // Save using AttendanceJsonService
        try {
            $attendanceMonthRecord = $this->attendanceJsonService->loadMonth(
                $session->user_id,
                $attendanceMonth,
                $shift->id
            );

            // Preserve existing audit trail and HR remark
            $existingAttendance = null;
            if ($attendanceMonthRecord && $attendanceMonthRecord->attendance_json) {
                $existingAttendance = $attendanceMonthRecord->attendance_json[$session->attendance_date] ?? null;
            }

            $currentUserId = auth()->id();
            $now = Carbon::now();

            if ($existingAttendance) {
                // Update existing record
                $attendanceData['edited_by'] = $currentUserId;
                $attendanceData['edited_at'] = $now->toDateTimeString();
                $attendanceData['created_by'] = $existingAttendance['created_by'] ?? $currentUserId;
                $attendanceData['created_at'] = $existingAttendance['created_at'] ?? $now->toDateTimeString();
                $attendanceData['hr_remark'] = $existingAttendance['hr_remark'] ?? null;
            } else {
                // New record
                $attendanceData['created_by'] = $currentUserId;
                $attendanceData['created_at'] = $now->toDateTimeString();
                $attendanceData['edited_by'] = null;
                $attendanceData['edited_at'] = null;
            }

            $this->attendanceJsonService->saveDay(
                $attendanceMonthRecord,
                $session->attendance_date,
                $attendanceData
            );

            // Mark session as processed
            $this->markCompleted($session);

            return 'success';
        } catch (\Exception $e) {
            $session->update([
                'remarks' => 'Attendance Save Failed: ' . $e->getMessage(),
            ]);
            return 'failed';
        }
    }

    protected function findEmployeeShift($userId, $date)
    {
        // Use EmployeeShiftService to get shift for date
        $employeeShift = $this->employeeShiftService->getForDate($userId, $date);

        if ($employeeShift) {
            return $employeeShift->shift;
        }

        // Fallback to user's default shift
        $user = User::find($userId);
        return $user->shift ?? null;
    }

    protected function buildAttendance(FingerprintSession $session, Shift $shift)
    {
        if (!$session->first_in) {
            return null;
        }

        // Call AttendanceCalculationService
        $calculatedData = $this->attendanceCalculationService->calculateDayAttendance(
            $shift,
            $session->attendance_date,
            $session->first_in,
            $session->last_out,
            null // Manual status is null for fingerprint
        );

        return $calculatedData;
    }

    protected function markCompleted(FingerprintSession $session)
    {
        $session->update([
            'processed' => true,
            'processed_at' => now(),
            'status' => 'Completed',
            'remarks' => null,
        ]);
    }

    public function getStats()
    {
        return [
            'pending' => FingerprintSession::pending()->count(),
            'completed' => FingerprintSession::processed()->count(),
            'total' => FingerprintSession::count(),
        ];
    }

    public function getHistory($userId = null, $startDate = null, $endDate = null)
    {
        $query = FingerprintSession::with(['user', 'shift'])->orderBy('attendance_date', 'desc');

        if ($userId) {
            $query->byEmployee($userId);
        }

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        return $query->paginate(50);
    }
}
