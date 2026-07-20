<?php

namespace App\Services;

use App\Models\FingerprintLog;
use App\Models\FingerprintSession;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class FingerprintProcessingService
{
    public function processBatch()
    {
        $pendingLogs = FingerprintLog::pending()->active()->get();
        $processedCount = 0;
        $skippedCount = 0;

        foreach ($pendingLogs as $log) {
            $result = $this->processEmployee($log);
            if ($result) {
                $processedCount++;
            } else {
                $skippedCount++;
            }
        }

        return [
            'processed' => $processedCount,
            'skipped' => $skippedCount,
        ];
    }

    public function processEmployee(FingerprintLog $log)
    {
        // Match employee code to user
        $user = User::where('employee_id', $log->employee_code)->first();

        if (!$user) {
            // Mark as processed with remark
            $log->update([
                'processed' => true,
                'processed_at' => now(),
                'status' => 'Skipped',
            ]);
            return false;
        }

        // Get employee's shift for the attendance date
        $attendanceDate = $log->punch_datetime->format('Y-m-d');
        $shift = $this->getEmployeeShiftForDate($user->id, $attendanceDate);

        // Group punches by employee and date
        $groupedPunches = $this->groupPunches($user->id, $attendanceDate);

        if (empty($groupedPunches)) {
            return false;
        }

        // Build session
        $sessionData = $this->buildSession($groupedPunches, $user->id, $attendanceDate, $shift);

        // Create or update session
        $this->createOrUpdateSession($sessionData);

        // Mark all logs for this employee/date as processed
        FingerprintLog::where('employee_code', $log->employee_code)
            ->whereDate('punch_datetime', $attendanceDate)
            ->where('processed', false)
            ->update([
                'processed' => true,
                'processed_at' => now(),
            ]);

        return true;
    }

    protected function groupPunches($userId, $attendanceDate)
    {
        $logs = FingerprintLog::where('employee_code', User::find($userId)->employee_id)
            ->whereDate('punch_datetime', $attendanceDate)
            ->orderBy('punch_datetime', 'asc')
            ->get();

        $punches = [];
        foreach ($logs as $log) {
            $punches[] = [
                'datetime' => $log->punch_datetime,
                'type' => $log->punch_type,
            ];
        }

        return $punches;
    }

    protected function buildSession($punches, $userId, $attendanceDate, $shift)
    {
        $firstIn = null;
        $lastOut = null;
        $totalPunch = count($punches);
        $remarks = [];

        if (empty($punches)) {
            return null;
        }

        // Find first IN
        $firstIn = $this->findFirstIn($punches);

        // Find last OUT
        $lastOut = $this->findLastOut($punches);

        // Check for missing IN or OUT
        if ($firstIn === null && $lastOut !== null) {
            $remarks[] = 'Missing IN';
        }
        if ($firstIn !== null && $lastOut === null) {
            $remarks[] = 'Missing OUT';
        }

        return [
            'user_id' => $userId,
            'attendance_date' => $attendanceDate,
            'shift_id' => $shift ? $shift->id : null,
            'first_in' => $firstIn,
            'last_out' => $lastOut,
            'total_punch' => $totalPunch,
            'status' => 'Active',
            'source' => 'Fingerprint',
            'processed' => false,
            'remarks' => implode(', ', $remarks),
        ];
    }

    protected function findFirstIn($punches)
    {
        foreach ($punches as $punch) {
            if ($punch['type'] === 'IN') {
                return $punch['datetime'];
            }
        }
        return null;
    }

    protected function findLastOut($punches)
    {
        $lastOut = null;
        foreach ($punches as $punch) {
            if ($punch['type'] === 'OUT') {
                $lastOut = $punch['datetime'];
            }
        }
        return $lastOut;
    }

    protected function createOrUpdateSession($sessionData)
    {
        if (!$sessionData) {
            return;
        }

        $existingSession = FingerprintSession::where('user_id', $sessionData['user_id'])
            ->where('attendance_date', $sessionData['attendance_date'])
            ->first();

        if ($existingSession) {
            $existingSession->update($sessionData);
        } else {
            FingerprintSession::create($sessionData);
        }
    }

    protected function getEmployeeShiftForDate($userId, $date)
    {
        // Try to get shift from EmployeeShift
        $employeeShift = \App\Models\EmployeeShift::where('user_id', $userId)
            ->where('effective_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('effective_to', '>=', $date)
                    ->orWhereNull('effective_to');
            })
            ->where('status', 'Active')
            ->first();

        if ($employeeShift) {
            return $employeeShift->shift;
        }

        // Fallback to user's default shift
        $user = User::find($userId);
        return $user->shift ?? null;
    }
}
