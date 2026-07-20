<?php

namespace App\Services;

use App\Models\EmployeeShift;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeShiftService
{
    /**
     * Assign a shift to an employee
     *
     * @param int $userId
     * @param int $shiftId
     * @param string $effectiveFrom
     * @param string|null $effectiveTo
     * @param bool $isDefault
     * @param string|null $remarks
     * @param int $createdBy
     * @return EmployeeShift
     */
    public function assignShift($userId, $shiftId, $effectiveFrom, $effectiveTo = null, $isDefault = false, $remarks = null, $createdBy = null)
    {
        // Validate no overlapping shift assignments
        $this->validateNoOverlap($userId, $effectiveFrom, $effectiveTo);

        // If this is marked as default, remove default flag from other shifts
        if ($isDefault) {
            EmployeeShift::where('user_id', $userId)
                          ->where('status', 'Active')
                          ->update(['is_default' => false]);
        }

        return EmployeeShift::create([
            'user_id' => $userId,
            'shift_id' => $shiftId,
            'effective_from' => $effectiveFrom,
            'effective_to' => $effectiveTo,
            'is_default' => $isDefault,
            'remarks' => $remarks,
            'status' => 'Active',
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Get current shift for an employee
     *
     * @param int $userId
     * @return EmployeeShift|null
     */
    public function getCurrentShift($userId)
    {
        $today = Carbon::now()->toDateString();

        return EmployeeShift::where('user_id', $userId)
                           ->active()
                           ->effectiveOn($today)
                           ->orderBy('is_default', 'desc')
                           ->orderBy('effective_from', 'desc')
                           ->first();
    }

    /**
     * Get shift for a specific date
     *
     * @param int $userId
     * @param string $date
     * @return EmployeeShift|null
     */
    public function getShiftForDate($userId, $date)
    {
        return EmployeeShift::where('user_id', $userId)
                           ->active()
                           ->effectiveOn($date)
                           ->orderBy('is_default', 'desc')
                           ->orderBy('effective_from', 'desc')
                           ->first();
    }

    /**
     * Get shift history for an employee
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getShiftHistory($userId)
    {
        return EmployeeShift::where('user_id', $userId)
                           ->with(['user', 'shift', 'creator', 'updater'])
                           ->orderBy('effective_from', 'desc')
                           ->get();
    }

    /**
     * Validate that there are no overlapping shift assignments
     *
     * @param int $userId
     * @param string $effectiveFrom
     * @param string|null $effectiveTo
     * @return void
     * @throws \Exception
     */
    protected function validateNoOverlap($userId, $effectiveFrom, $effectiveTo)
    {
        $query = EmployeeShift::where('user_id', $userId)
                             ->active();

        if ($effectiveTo) {
            // Check for overlaps when both dates are specified
            $overlaps = $query->where(function ($q) use ($effectiveFrom, $effectiveTo) {
                $q->where(function ($subQ) use ($effectiveFrom, $effectiveTo) {
                    // New period starts before existing period ends
                    $subQ->where('effective_from', '<=', $effectiveTo)
                         ->where(function ($innerQ) use ($effectiveFrom) {
                             $innerQ->whereNull('effective_to')
                                    ->orWhere('effective_to', '>=', $effectiveFrom);
                         });
                });
            })->exists();

            if ($overlaps) {
                throw new \Exception('Shift assignment overlaps with existing shift history. Please adjust the effective dates.');
            }
        } else {
            // Check for overlaps when effective_to is null (ongoing)
            $overlaps = $query->where(function ($q) use ($effectiveFrom) {
                $q->where('effective_from', '>=', $effectiveFrom)
                  ->orWhere(function ($subQ) use ($effectiveFrom) {
                      $subQ->where('effective_from', '<=', $effectiveFrom)
                            ->whereNull('effective_to');
                  });
            })->exists();

            if ($overlaps) {
                throw new \Exception('Shift assignment overlaps with existing shift history. Please set effective_to for the previous shift or adjust effective_from.');
            }
        }
    }

    /**
     * Update an existing shift assignment
     *
     * @param EmployeeShift $employeeShift
     * @param array $data
     * @param int $updatedBy
     * @return EmployeeShift
     */
    public function updateShift(EmployeeShift $employeeShift, array $data, $updatedBy = null)
    {
        // If effective dates are changing, validate no overlaps
        if (isset($data['effective_from']) || isset($data['effective_to'])) {
            $effectiveFrom = $data['effective_from'] ?? $employeeShift->effective_from;
            $effectiveTo = $data['effective_to'] ?? $employeeShift->effective_to;

            // Exclude current record from overlap check
            $existingOverlaps = EmployeeShift::where('user_id', $employeeShift->user_id)
                                            ->where('id', '!=', $employeeShift->id)
                                            ->active()
                                            ->where(function ($q) use ($effectiveFrom, $effectiveTo) {
                                                if ($effectiveTo) {
                                                    $q->where('effective_from', '<=', $effectiveTo)
                                                      ->where(function ($subQ) use ($effectiveFrom) {
                                                          $subQ->whereNull('effective_to')
                                                                ->orWhere('effective_to', '>=', $effectiveFrom);
                                                      });
                                                } else {
                                                    $q->where('effective_from', '>=', $effectiveFrom)
                                                      ->orWhere(function ($subQ) use ($effectiveFrom) {
                                                          $subQ->where('effective_from', '<=', $effectiveFrom)
                                                                ->whereNull('effective_to');
                                                      });
                                                }
                                            })->exists();

            if ($existingOverlaps) {
                throw new \Exception('Shift assignment overlaps with existing shift history.');
            }
        }

        // If marking as default, remove default flag from other shifts
        if (isset($data['is_default']) && $data['is_default']) {
            EmployeeShift::where('user_id', $employeeShift->user_id)
                          ->where('id', '!=', $employeeShift->id)
                          ->where('status', 'Active')
                          ->update(['is_default' => false]);
        }

        $employeeShift->update(array_merge($data, ['updated_by' => $updatedBy]));

        return $employeeShift->fresh();
    }

    /**
     * Soft delete a shift assignment (mark as inactive)
     *
     * @param EmployeeShift $employeeShift
     * @param int $updatedBy
     * @return bool
     */
    public function destroyShift(EmployeeShift $employeeShift, $updatedBy = null)
    {
        return $employeeShift->update([
            'status' => 'Inactive',
            'updated_by' => $updatedBy,
        ]);
    }
}
