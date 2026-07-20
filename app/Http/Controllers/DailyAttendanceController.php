<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\AttendanceMonth;
use App\Services\AttendanceCalculationService;
use App\Services\AttendanceJsonService;
use App\Services\EmployeeShiftService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyAttendanceController extends Controller
{
    protected $attendanceCalculationService;
    protected $attendanceJsonService;
    protected $employeeShiftService;

    public function __construct(
        AttendanceCalculationService $attendanceCalculationService,
        AttendanceJsonService $attendanceJsonService,
        EmployeeShiftService $employeeShiftService
    ) {
         parent::__construct();
        $this->attendanceCalculationService = $attendanceCalculationService;
        $this->attendanceJsonService = $attendanceJsonService;
        $this->employeeShiftService = $employeeShiftService;
    }

    /**
     * Display daily attendance entry form
     */
    public function index(Request $request)
    {
        // Get eligible employees (salary_processing=1 and status=1)
        $users = User::where('salary_processing', 1)
                     ->where('status', '1')
                     ->pluck('name', 'id');

        // Get shifts
        $shifts = Shift::pluck('name', 'id');

        return view('admin.daily_attendance.index', compact('users', 'shifts'));
    }

    /**
     * Get assigned shift for an employee on a specific date
     */
    public function getAssignedShift(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
        ]);

        $userId = $validated['user_id'];
        $date = $validated['attendance_date'];

        // Try to get shift from EmployeeShiftService (employee_shifts table)
        $employeeShift = $this->employeeShiftService->getShiftForDate($userId, $date);

        if ($employeeShift && $employeeShift->shift) {
            return response()->json([
                'shift_id' => $employeeShift->shift_id,
                'shift_name' => $employeeShift->shift->name,
                'source' => 'employee_shifts'
            ]);
        }

        // Fallback to users.shift_id
        $user = User::find($userId);
        if ($user && $user->shift_id) {
            $shift = Shift::find($user->shift_id);
            if ($shift) {
                return response()->json([
                    'shift_id' => $shift->id,
                    'shift_name' => $shift->name,
                    'source' => 'user_default'
                ]);
            }
        }

        // No shift assigned
        return response()->json([
            'shift_id' => null,
            'shift_name' => 'No Shift Assigned',
            'source' => 'none'
        ]);
    }

    /**
     * Load specific day's attendance data for daily entry
     */
    public function loadDay(Request $request)
    {
        $data = request()->all();

        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'user_id' => 'required',
            'attendance_month' => 'required|date_format:Y-m',
            'attendance_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Get user
        $user = User::find($data['user_id']);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if attendance month already exists
        $attendanceMonth = AttendanceMonth::where('user_id', $data['user_id'])
                                          ->where('attendance_month', $data['attendance_month'])
                                          ->first();

        $dayKey = date('Y-m-d', strtotime($data['attendance_date']));
        $dayData = null;
        $isLocked = false;

        if ($attendanceMonth) {
            $isLocked = $attendanceMonth->attendance_locked;
            $attendanceJson = $attendanceMonth->attendance_json ?? [];
            $dayData = $attendanceJson[$dayKey] ?? null;
        }

        return response()->json([
            'employee_name' => $user->name,
            'day_data' => $dayData,
            'attendance_locked' => $isLocked,
            'shift_id' => $dayData['shift_id'] ?? '',
            'manual_status' => $dayData['manual_status'] ?? '',
            'status' => $dayData['status'] ?? '',
            'late_minutes' => $dayData['late_minutes'] ?? '',
            'worked_minutes' => $dayData['worked_minutes'] ?? '',
            'system_remark' => $dayData['system_remark'] ?? ''
        ]);
    }

    /**
     * Store daily attendance (updates single day JSON node)
     */
    public function store(Request $request)
    {
        $data = request()->all();

        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'user_id' => 'required',
            'attendance_month' => 'required|date_format:Y-m',
            'attendance_date' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
            'manual_status' => 'nullable|in:Holiday,Weekly Off,Leave',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if attendance month already exists
        $attendanceMonth = AttendanceMonth::where('user_id', $data['user_id'])
                                          ->where('attendance_month', $data['attendance_month'])
                                          ->first();

        // Check if attendance is locked
        if ($attendanceMonth && $attendanceMonth->attendance_locked) {
            return redirect()->back()->withErrors(['attendance_locked' => 'Locked attendance cannot be edited'])->withInput();
        }

        // Calculate attendance using AttendanceCalculationService
        $shift = isset($data['shift_id']) ? Shift::find($data['shift_id']) : null;
        $calculatedData = $this->attendanceCalculationService->calculateDayAttendance(
            $shift,
            $data['attendance_date'],
            isset($data['check_in']) ? $data['attendance_date'] . ' ' . $data['check_in'] : null,
            isset($data['check_out']) ? $data['attendance_date'] . ' ' . $data['check_out'] : null,
            $data['manual_status'] ?? null
        );

        // Prepare day data with calculated values
        $dayKey = date('Y-m-d', strtotime($data['attendance_date']));
        $dayData = [
            'status' => $calculatedData['status'] ?? '',
            'check_in' => $data['check_in'] ?? '',
            'check_out' => $data['check_out'] ?? '',
            'shift_id' => $data['shift_id'] ?? '',
            'manual_status' => $data['manual_status'] ?? '',
            'late_minutes' => $calculatedData['late_minutes'] ?? '',
            'worked_minutes' => $calculatedData['worked_minutes'] ?? '',
            'system_remark' => $calculatedData['system_remark'] ?? '',
            'remarks' => $data['remarks'] ?? ''
        ];

        if ($attendanceMonth) {
            // Update existing month
            $attendanceJson = $attendanceMonth->attendance_json ?? [];
            $attendanceJson[$dayKey] = $dayData;
            $attendanceMonth->attendance_json = $attendanceJson;
            $attendanceMonth->recalculateTotals();
            $attendanceMonth->updated_by = \Illuminate\Support\Facades\Auth::user()->id;
            $attendanceMonth->save();

        } else {
            // Create new month
            $daysInMonth = date('t', strtotime($data['attendance_month'] . '-01'));
            $blankJson = [];
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $blankDayKey = date('Y-m-d', strtotime($data['attendance_month'] . '-' . str_pad($day, 2, '0', STR_PAD_LEFT)));
                $blankJson[$blankDayKey] = [
                    'status' => '',
                    'check_in' => '',
                    'check_out' => '',
                    'shift_id' => '',
                    'manual_status' => '',
                    'late_minutes' => '',
                    'worked_minutes' => '',
                    'system_remark' => '',
                    'remarks' => ''
                ];
            }
            
            // Set the specific day's data
            $blankJson[$dayKey] = $dayData;

            $attendanceMonth = new AttendanceMonth();
            $attendanceMonth->user_id = $data['user_id'];
            $attendanceMonth->attendance_month = $data['attendance_month'];
            $attendanceMonth->attendance_json = $blankJson;
            $attendanceMonth->recalculateTotals();
            $attendanceMonth->attendance_locked = 0;
            $attendanceMonth->created_by = \Illuminate\Support\Facades\Auth::user()->id;
            $attendanceMonth->updated_by = \Illuminate\Support\Facades\Auth::user()->id;
            $attendanceMonth->save();
        }

        return redirect()->route('daily_attendance.index')->with('flash_success', 'Daily attendance saved successfully');
    }

    /**
     * Load attendance data for a specific date for multiple employees
     */
    protected function loadAttendanceForDate($employees, $date)
    {
        $attendanceData = [];
        $attendanceMonth = Carbon::parse($date)->format('Y-m');

        foreach ($employees as $employee) {
            $attendanceMonthRecord = AttendanceMonth::where('user_id', $employee->id)
                                                   ->where('attendance_month', $attendanceMonth)
                                                   ->first();

            if ($attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
                $dayData = $attendanceJson[$date] ?? null;
            } else {
                $dayData = null;
            }

            $attendanceData[$employee->id] = [
                'employee' => $employee,
                'attendance' => $dayData,
                'shift' => $employee->shift ?? null,
            ];
        }

        return $attendanceData;
    }

    /**
     * Save daily attendance for a specific date
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.shift_id' => 'nullable|exists:shifts,id',
            'attendances.*.check_in' => 'nullable|date_format:H:i:s',
            'attendances.*.check_out' => 'nullable|date_format:H:i:s',
            'attendances.*.manual_status' => 'nullable|in:Holiday,Weekly Off,Leave',
            'attendances.*.hr_remark' => 'nullable|string',
        ]);

        $date = $validated['date'];
        $attendances = $validated['attendances'];
        $attendanceMonth = Carbon::parse($date)->format('Y-m');
        $currentUserId = auth()->id();
        $now = Carbon::now();

        foreach ($attendances as $attendance) {
            $userId = $attendance['user_id'];
            $shiftId = $attendance['shift_id'] ?? null;
            $checkIn = $attendance['check_in'] ?? null;
            $checkOut = $attendance['check_out'] ?? null;
            $manualStatus = $attendance['manual_status'] ?? null;
            $hrRemark = $attendance['hr_remark'] ?? null;

            // Get shift
            $shift = $shiftId ? Shift::find($shiftId) : null;

            if (!$shift) {
                // If no shift assigned, skip
                continue;
            }

            // Validate check-out is not earlier than check-in
            if ($checkIn && $checkOut) {
                $checkInTime = Carbon::parse($date . ' ' . $checkIn);
                $checkOutTime = Carbon::parse($date . ' ' . $checkOut);
                if ($checkOutTime->lte($checkInTime)) {
                    return redirect()->back()
                                    ->withInput()
                                    ->with('error', "Check out time cannot be earlier than check in time for employee ID: {$userId}");
                }
            }

            // Check if month is locked
            $attendanceMonthRecord = AttendanceMonth::where('user_id', $userId)
                                                   ->where('attendance_month', $attendanceMonth)
                                                   ->first();
            if ($attendanceMonthRecord && $attendanceMonthRecord->attendance_locked) {
                return redirect()->back()
                                ->withInput()
                                ->with('error', "Cannot save attendance for employee ID: {$userId}. Month is locked.");
            }

            // Calculate attendance using AttendanceCalculationService
            $calculatedData = $this->attendanceCalculationService->calculateDayAttendance(
                $shift,
                $date,
                $checkIn ? $date . ' ' . $checkIn : null,
                $checkOut ? $date . ' ' . $checkOut : null,
                $manualStatus
            );

            // Apply manual status rules
            if (in_array($manualStatus, ['Holiday', 'Leave', 'Weekly Off'])) {
                $calculatedData['check_in'] = '';
                $calculatedData['check_out'] = '';
                $calculatedData['worked_minutes'] = 0;
                $calculatedData['late_minutes'] = 0;
                $calculatedData['early_leave_minutes'] = 0;
            }

            // Add HR remark
            $calculatedData['hr_remark'] = $hrRemark;

            // Load or create attendance month using AttendanceJsonService
            $attendanceMonthRecord = $this->attendanceJsonService->loadMonth($userId, $attendanceMonth, $shiftId);

            // Check if day already exists for audit trail
            $existingAttendance = null;
            if ($attendanceMonthRecord && $attendanceMonthRecord->attendance_json) {
                $existingAttendance = $attendanceMonthRecord->attendance_json[$date] ?? null;
            }

            // Add audit trail
            if ($existingAttendance) {
                // Update existing record
                $calculatedData['edited_by'] = $currentUserId;
                $calculatedData['edited_at'] = $now->toDateTimeString();
                $calculatedData['created_by'] = $existingAttendance['created_by'] ?? $currentUserId;
                $calculatedData['created_at'] = $existingAttendance['created_at'] ?? $now->toDateTimeString();
            } else {
                // New record
                $calculatedData['created_by'] = $currentUserId;
                $calculatedData['created_at'] = $now->toDateTimeString();
                $calculatedData['edited_by'] = null;
                $calculatedData['edited_at'] = null;
            }

            // Save only the specific date using AttendanceJsonService
            $this->attendanceJsonService->saveDay($attendanceMonthRecord, $date, $calculatedData);
        }

        return redirect()->route('daily_attendance.index', ['date' => $date])
                        ->with('success', 'Attendance saved successfully.');
    }

    /**
     * Load attendance data via AJAX for filtering
     */
    public function load(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $departmentId = $request->input('department_id');
        $shiftId = $request->input('shift_id');
        $status = $request->input('status');

        // Get eligible employees
        $query = User::where('salary_processing', 1)
                     ->where('status', 'Active');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->get();

        // Load attendance data
        $attendanceData = $this->loadAttendanceForDate($employees, $date);

        // Filter by status if provided
        if ($status) {
            $attendanceData = array_filter($attendanceData, function($data) use ($status) {
                return $data['attendance'] && $data['attendance']['status'] === $status;
            });
        }

        return response()->json([
            'attendance_data' => $attendanceData,
        ]);
    }

    /**
     * Calculate attendance via AJAX for live updates
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'manual_status' => 'nullable|in:Holiday,Weekly Off,Leave',
        ]);

        \Log::info('DailyAttendanceController::calculate called', $validated);

        $userId = $validated['user_id'];
        $date = $validated['attendance_date'];
        $shiftId = $validated['shift_id'] ?? null;
        $checkIn = $validated['check_in'] ?? null;
        $checkOut = $validated['check_out'] ?? null;
        $manualStatus = $validated['manual_status'] ?? null;

        \Log::info('Parsed values', compact('userId', 'date', 'shiftId', 'checkIn', 'checkOut', 'manualStatus'));

        // Get shift
        $shift = $shiftId ? Shift::find($shiftId) : null;

        if (!$shift) {
            \Log::warning('Shift not found', ['shiftId' => $shiftId]);
            // If no shift, return default values
            return response()->json([
                'status' => '',
                'late_minutes' => '',
                'worked_minutes' => '',
                'system_remark' => 'No shift assigned'
            ]);
        }

        \Log::info('Shift found', ['shift' => $shift->name]);

        // Validate check-out is not earlier than check-in
        if ($checkIn && $checkOut) {
            $checkInTime = Carbon::parse($date . ' ' . $checkIn);
            $checkOutTime = Carbon::parse($date . ' ' . $checkOut);
            if ($checkOutTime->lte($checkInTime)) {
                \Log::warning('Check out earlier than check in');
                return response()->json([
                    'error' => 'Check out time cannot be earlier than check in time'
                ], 400);
            }
        }

        // Calculate attendance using AttendanceCalculationService
        \Log::info('Calling AttendanceCalculationService');
        $calculatedData = $this->attendanceCalculationService->calculateDayAttendance(
            $shift,
            $date,
            $checkIn ? $date . ' ' . $checkIn : null,
            $checkOut ? $date . ' ' . $checkOut : null,
            $manualStatus
        );

        \Log::info('AttendanceCalculationService returned', $calculatedData);

        // Check if month is locked
        $attendanceMonth = Carbon::parse($date)->format('Y-m');
        $attendanceMonthRecord = AttendanceMonth::where('user_id', $userId)
                                               ->where('attendance_month', $attendanceMonth)
                                               ->first();
        $isLocked = $attendanceMonthRecord ? $attendanceMonthRecord->attendance_locked : false;

        \Log::info('Month locked status', ['isLocked' => $isLocked]);

        return response()->json([
            'status' => $calculatedData['status'] ?? '',
            'late_minutes' => $calculatedData['late_minutes'] ?? '',
            'worked_minutes' => $calculatedData['worked_minutes'] ?? '',
            'system_remark' => $calculatedData['system_remark'] ?? '',
            'is_locked' => $isLocked
        ]);
    }
}
