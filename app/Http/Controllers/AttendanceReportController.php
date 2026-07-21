<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceMonth;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Shift;
use App\Services\EmployeeShiftService;
use App\Models\SiteSetting;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    /**
     * Display attendance reports index
     */
    public function index()
    {
        return view('admin.attendance_reports.index');
    }
    
    protected $employeeShiftService;
        public function __construct(EmployeeShiftService $employeeShiftService)
        {
            parent::__construct();
            $this->employeeShiftService = $employeeShiftService;
        }
        
    /**
     * Employee Daily Attendance Report
     * Filter: Employee, Date
     * Output: One employee, one day
     */
    public function employeeDaily(Request $request)
    {
        $data = [
            'employees' => User::where('status', '1')->orderBy('name')->get(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('employee_id') && $request->has('attendance_date')) {
            $employee = User::find($request->employee_id);
            $assignedShift = $this->employeeShiftService->getShiftForDate($employee->id, $request->attendance_date);
            $attendanceDate = $request->attendance_date;
            $attendanceMonth = substr($attendanceDate, 0, 7); // Extract YYYY-MM from YYYY-MM-DD

            $attendanceMonthRecord = AttendanceMonth::where('user_id', $request->employee_id)
                                                   ->where('attendance_month', $attendanceMonth)
                                                   ->first();

            if ($attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
                $dayData = $attendanceJson[$attendanceDate] ?? null;

                $data['employee'] = $employee;
                $data['attendanceDate'] = $attendanceDate;
                $data['attendanceMonthRecord'] = $attendanceMonthRecord;
                $data['dayData'] = $dayData;
                $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
                $data['reportTitle'] = 'Employee Daily Attendance Report';
                $data['generatedBy'] = auth()->user()->name ?? 'System';
                $data['generatedDate'] = now()->format('Y-m-d H:i:s');
                $data['assignedShift'] = $assignedShift;
            } else {
                $data['employee'] = $employee;
                $data['attendanceDate'] = $attendanceDate;
                $data['dayData'] = null;
                $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
                $data['reportTitle'] = 'Employee Daily Attendance Report';
                $data['generatedBy'] = auth()->user()->name ?? 'System';
                $data['generatedDate'] = now()->format('Y-m-d H:i:s');
                $data['assignedShift'] = $assignedShift;
            }
        }

        return view('admin.attendance_reports.employee_daily', $data);
    }


    /**
     * Employee Daily Attendance Report - Print
     */
    public function employeeDailyPrint(Request $request)
    {
        $employee = User::find($request->employee_id);
        $assignedShift = $this->employeeShiftService->getShiftForDate($employee->id, $request->attendance_date);
        $attendanceDate = $request->attendance_date;
        $attendanceMonth = substr($attendanceDate, 0, 7);

        if (!$employee) {
            abort(404);
        }

        $attendanceMonthRecord = AttendanceMonth::where('user_id', $request->employee_id)
                                               ->where('attendance_month', $attendanceMonth)
                                               ->first();

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();

        if ($attendanceMonthRecord) {
            $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
            $dayData = $attendanceJson[$attendanceDate] ?? null;
        } else {
            $dayData = null;
        }

        $data = [
            'employee' => $employee,
            'attendanceDate' => $attendanceDate,
            'attendanceMonthRecord' => $attendanceMonthRecord,
            'dayData' => $dayData,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'reportTitle' => 'Employee Daily Attendance Report',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
            'assignedShift' => $assignedShift,
        ];

        return view('admin.attendance_reports.employee_daily_print', $data);
    }

    /**
     * Employee Monthly Attendance Report
     * Filter: Employee, Month
     * Output: Complete monthly attendance, summary, daily records
     */
    public function employeeMonthly(Request $request)
    {
        $data = [
            'employees' => User::where('status', '1')->orderBy('name')->get(),
            'departments' => Department::all(),
            'designations' => Designation::all(),
            'shifts' => Shift::all(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('employee_id') && $request->has('attendance_month')) {
            $employee = User::find($request->employee_id);
            $firstDate = $request->attendance_month . '-01';
            $assignedShift = $this->employeeShiftService->getShiftForDate($employee->id, $firstDate);
            $data['assignedShift'] = $assignedShift;
            $attendanceMonth = AttendanceMonth::where('user_id', $request->employee_id)
                                               ->where('attendance_month', $request->attendance_month)
                                               ->first();

            if ($attendanceMonth) {
                $totalHolidays = $attendanceMonth->summary_holiday ?? 0;
                $totalWeeklyOff = $attendanceMonth->summary_weekly_off ?? 0;
                $daysInMonth = Carbon::parse($attendanceMonth->attendance_month . '-01')->daysInMonth;
                $expectedWorkingDays = $daysInMonth - $totalHolidays - $totalWeeklyOff;
                $presentDays = ($attendanceMonth->summary_present ?? 0) + ($attendanceMonth->summary_late ?? 0) + ($attendanceMonth->summary_halfday ?? 0);
                $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentDays / $expectedWorkingDays) * 100, 2) : 0;

                $data['employee'] = $employee;
                $data['attendanceMonth'] = $attendanceMonth;
                $data['attendanceJson'] = $attendanceMonth->attendance_json ?? [];
                $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
                $data['reportTitle'] = 'Employee Monthly Attendance Report';
                $data['totalHolidays'] = $totalHolidays;
                $data['totalWeeklyOff'] = $totalWeeklyOff;
                $data['expectedWorkingDays'] = $expectedWorkingDays;
                $data['attendancePercentage'] = $attendancePercentage;
                $data['generatedBy'] = auth()->user()->name ?? 'System';
                $data['generatedDate'] = now()->format('Y-m-d H:i:s');
                $data['assignedShift'] = $assignedShift;
            }
        }

        return view('admin.attendance_reports.employee_monthly', $data);
    }

    /**
     * Employee Monthly Attendance Report - Print
     */
    public function employeeMonthlyPrint(Request $request)
    {
        $employee = User::find($request->employee_id);
        $firstDate = $request->attendance_month . '-01';
        $assignedShift = $this->employeeShiftService->getShiftForDate($employee->id, $firstDate);
        $data['assignedShift'] = $assignedShift;
        $attendanceMonth = AttendanceMonth::where('user_id', $request->employee_id)
                                           ->where('attendance_month', $request->attendance_month)
                                           ->first();

        if (!$employee || !$attendanceMonth) {
            abort(404);
        }

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $totalHolidays = $attendanceMonth->summary_holiday ?? 0;
        $totalWeeklyOff = $attendanceMonth->summary_weekly_off ?? 0;
        $daysInMonth = Carbon::parse($attendanceMonth->attendance_month . '-01')->daysInMonth;
        $expectedWorkingDays = $daysInMonth - $totalHolidays - $totalWeeklyOff;
        $presentDays = ($attendanceMonth->summary_present ?? 0) + ($attendanceMonth->summary_late ?? 0) + ($attendanceMonth->summary_halfday ?? 0);
        $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentDays / $expectedWorkingDays) * 100, 2) : 0;

        $data = [
            'employee' => $employee,
            'attendanceMonth' => $attendanceMonth,
            'attendanceJson' => $attendanceMonth->attendance_json ?? [],
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'reportTitle' => 'Employee Monthly Attendance Report',
            'totalHolidays' => $totalHolidays,
            'totalWeeklyOff' => $totalWeeklyOff,
            'expectedWorkingDays' => $expectedWorkingDays,
            'attendancePercentage' => $attendancePercentage,
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
            'assignedShift' => $assignedShift,
        ];

        return view('admin.attendance_reports.employee_monthly_print', $data);
    }

    /**
     * Daily Attendance Register
     * Filter: Date, Department (optional), Shift (optional), Status (optional)
     * Output: All employees for one day
     */
    public function dailyRegister(Request $request)
    {
        $data = [
            'departments' => Department::orderBy('name')->get(),
            'shifts' => Shift::where('status', 1)->orderBy('name')->get(),
            'statuses' => [
                '' => 'All',
                'Present' => 'Present',
                'Late' => 'Late',
                'Half Day' => 'Half Day',
                'Absent' => 'Absent',
                'Leave' => 'Leave',
                'Holiday' => 'Holiday',
                'Weekly Off' => 'Weekly Off',
            ],
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('attendance_date')) {
            $attendanceDate = $request->attendance_date;
            $attendanceMonth = substr($attendanceDate, 0, 7);

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->orderBy('name');

            // Apply department filter
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            $employees = $query->get();

            // Load attendance data for all employees in one query
            $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get()
                                                     ->keyBy('user_id');

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Build attendance data array
            $attendanceData = [];
            $summary = [
                'total' => 0,
                'present' => 0,
                'late' => 0,
                'halfDay' => 0,
                'absent' => 0,
                'leave' => 0,
                'holiday' => 0,
                'weeklyOff' => 0,
            ];

            foreach ($employees as $employee) {
                $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);
                $dayData = null;

                if ($attendanceMonthRecord) {
                    $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
                    $dayData = $attendanceJson[$attendanceDate] ?? null;
                }

                // Get assigned shift using EmployeeShiftService
                $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceDate);
                $assignedShift = $employeeShift ? $employeeShift->shift : null;

                // Apply status filter
                if ($request->has('status') && $request->status != '') {
                    if (!$dayData || ($dayData['status'] ?? '') != $request->status) {
                        continue;
                    }
                }

                $status = $dayData['status'] ?? '';
                $summary['total']++;

                // Update summary counts
                if ($status == 'Present') {
                    $summary['present']++;
                } elseif ($status == 'Late') {
                    $summary['late']++;
                } elseif ($status == 'Half Day') {
                    $summary['halfDay']++;
                } elseif ($status == 'Absent') {
                    $summary['absent']++;
                } elseif ($status == 'Leave') {
                    $summary['leave']++;
                } elseif ($status == 'Holiday') {
                    $summary['holiday']++;
                } elseif ($status == 'Weekly Off') {
                    $summary['weeklyOff']++;
                }

                $attendanceData[] = [
                    'employee' => $employee,
                    'attendanceMonthRecord' => $attendanceMonthRecord,
                    'dayData' => $dayData,
                    'assignedShift' => $assignedShift,
                ];
            }

            // Calculate attendance percentage
            $expectedEmployees = $summary['total'];
            $presentCount = $summary['present'] + $summary['late'] + $summary['halfDay'];
            $attendancePercentage = $expectedEmployees > 0 ? round(($presentCount / $expectedEmployees) * 100, 2) : 0;
            $summary['attendancePercentage'] = $attendancePercentage;

            $data['attendanceDate'] = $attendanceDate;
            $data['attendanceData'] = $attendanceData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Daily Attendance Register';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.daily_register', $data);
    }

    /**
     * Daily Attendance Register - Print
     */
    public function dailyRegisterPrint(Request $request)
    {
        $attendanceDate = $request->attendance_date;
        $attendanceMonth = substr($attendanceDate, 0, 7);

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->orderBy('name');

        // Apply department filter
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->get();

        // Load attendance data for all employees in one query
        $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get()
                                                 ->keyBy('user_id');

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Build attendance data array
        $attendanceData = [];
        $summary = [
            'total' => 0,
            'present' => 0,
            'late' => 0,
            'halfDay' => 0,
            'absent' => 0,
            'leave' => 0,
            'holiday' => 0,
            'weeklyOff' => 0,
        ];

        foreach ($employees as $employee) {
            $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);
            $dayData = null;

            if ($attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
                $dayData = $attendanceJson[$attendanceDate] ?? null;
            }

            // Get assigned shift using EmployeeShiftService
            $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceDate);
            $assignedShift = $employeeShift ? $employeeShift->shift : null;

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                if (!$dayData || ($dayData['status'] ?? '') != $request->status) {
                    continue;
                }
            }

            $status = $dayData['status'] ?? '';
            $summary['total']++;

            // Update summary counts
            if ($status == 'Present') {
                $summary['present']++;
            } elseif ($status == 'Late') {
                $summary['late']++;
            } elseif ($status == 'Half Day') {
                $summary['halfDay']++;
            } elseif ($status == 'Absent') {
                $summary['absent']++;
            } elseif ($status == 'Leave') {
                $summary['leave']++;
            } elseif ($status == 'Holiday') {
                $summary['holiday']++;
            } elseif ($status == 'Weekly Off') {
                $summary['weeklyOff']++;
            }

            $attendanceData[] = [
                'employee' => $employee,
                'attendanceMonthRecord' => $attendanceMonthRecord,
                'dayData' => $dayData,
                'assignedShift' => $assignedShift,
            ];
        }

        // Calculate attendance percentage
        $expectedEmployees = $summary['total'];
        $presentCount = $summary['present'] + $summary['late'] + $summary['halfDay'];
        $attendancePercentage = $expectedEmployees > 0 ? round(($presentCount / $expectedEmployees) * 100, 2) : 0;
        $summary['attendancePercentage'] = $attendancePercentage;

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();
        $shifts = Shift::where('status', 1)->orderBy('name')->get();

        $data = [
            'attendanceDate' => $attendanceDate,
            'attendanceData' => $attendanceData,
            'summary' => $summary,
            'departments' => $departments,
            'shifts' => $shifts,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Daily Attendance Register',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.daily_register_print', $data);
    }

    /**
     * Monthly Attendance Register
     * Filter: Month, Department, Status
     * Output: All employees for one month, summary
     */
    public function monthlyRegister(Request $request)
    {
        $data = [
            'departments' => Department::orderBy('name')->get(),
            'statuses' => [
                '' => 'All',
                'Present' => 'Present',
                'Late' => 'Late',
                'Half Day' => 'Half Day',
                'Absent' => 'Absent',
                'Leave' => 'Leave',
                'Holiday' => 'Holiday',
                'Weekly Off' => 'Weekly Off',
            ],
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('attendance_month')) {
            $attendanceMonth = $request->attendance_month;

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->orderBy('name');

            // Apply department filter
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            $employees = $query->get();

            // Load attendance data for all employees in one query
            $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get()
                                                     ->keyBy('user_id');

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Build attendance data array
            $attendanceData = [];
            $summary = [
                'totalEmployees' => 0,
                'totalPresent' => 0,
                'totalLate' => 0,
                'totalHalfDay' => 0,
                'totalAbsent' => 0,
                'totalLeave' => 0,
                'totalHoliday' => 0,
                'totalWeeklyOff' => 0,
                'averageAttendancePercentage' => 0,
            ];

            $attendancePercentages = [];

            foreach ($employees as $employee) {
                $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);

                $present = $attendanceMonthRecord->summary_present ?? 0;
                $late = $attendanceMonthRecord->summary_late ?? 0;
                $halfDay = $attendanceMonthRecord->summary_halfday ?? 0;
                $absent = $attendanceMonthRecord->summary_absent ?? 0;
                $leave = $attendanceMonthRecord->summary_leave ?? 0;
                $holiday = $attendanceMonthRecord->summary_holiday ?? 0;
                $weeklyOff = $attendanceMonthRecord->summary_weekly_off ?? 0;

                // Calculate expected working days
                $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
                $expectedWorkingDays = $daysInMonth - $holiday - $weeklyOff;

                // Calculate attendance percentage
                $presentCount = $present + $late + $halfDay;
                $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / $expectedWorkingDays) * 100, 2) : 0;

                // Get assigned shift using EmployeeShiftService (use first day of month)
                $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
                $assignedShift = $employeeShift ? $employeeShift->shift : null;

                // Apply status filter
                if ($request->has('status') && $request->status != '') {
                    $statusFilter = $request->status;
                    $shouldInclude = false;

                    if ($statusFilter == 'Present' && $present > 0) {
                        $shouldInclude = true;
                    } elseif ($statusFilter == 'Late' && $late > 0) {
                        $shouldInclude = true;
                    } elseif ($statusFilter == 'Half Day' && $halfDay > 0) {
                        $shouldInclude = true;
                    } elseif ($statusFilter == 'Absent' && $absent > 0) {
                        $shouldInclude = true;
                    } elseif ($statusFilter == 'Leave' && $leave > 0) {
                        $shouldInclude = true;
                    } elseif ($statusFilter == 'Holiday' && $holiday > 0) {
                        $shouldInclude = true;
                    } elseif ($statusFilter == 'Weekly Off' && $weeklyOff > 0) {
                        $shouldInclude = true;
                    }

                    if (!$shouldInclude) {
                        continue;
                    }
                }

                $summary['totalEmployees']++;
                $summary['totalPresent'] += $present;
                $summary['totalLate'] += $late;
                $summary['totalHalfDay'] += $halfDay;
                $summary['totalAbsent'] += $absent;
                $summary['totalLeave'] += $leave;
                $summary['totalHoliday'] += $holiday;
                $summary['totalWeeklyOff'] += $weeklyOff;
                $attendancePercentages[] = $attendancePercentage;

                $attendanceData[] = [
                    'employee' => $employee,
                    'attendanceMonthRecord' => $attendanceMonthRecord,
                    'assignedShift' => $assignedShift,
                    'present' => $present,
                    'late' => $late,
                    'halfDay' => $halfDay,
                    'absent' => $absent,
                    'leave' => $leave,
                    'holiday' => $holiday,
                    'weeklyOff' => $weeklyOff,
                    'attendancePercentage' => $attendancePercentage,
                    'locked' => $attendanceMonthRecord->locked ?? 0,
                ];
            }

            // Calculate average attendance percentage
            if (count($attendancePercentages) > 0) {
                $summary['averageAttendancePercentage'] = round(array_sum($attendancePercentages) / count($attendancePercentages), 2);
            }

            $data['attendanceMonth'] = $attendanceMonth;
            $data['attendanceData'] = $attendanceData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Monthly Attendance Register';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.monthly_register', $data);
    }

    /**
     * Monthly Attendance Register - Print
     */
    public function monthlyRegisterPrint(Request $request)
    {
        $attendanceMonth = $request->attendance_month;

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->orderBy('name');

        // Apply department filter
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->get();

        // Load attendance data for all employees in one query
        $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get()
                                                 ->keyBy('user_id');

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Build attendance data array
        $attendanceData = [];
        $summary = [
            'totalEmployees' => 0,
            'totalPresent' => 0,
            'totalLate' => 0,
            'totalHalfDay' => 0,
            'totalAbsent' => 0,
            'totalLeave' => 0,
            'totalHoliday' => 0,
            'totalWeeklyOff' => 0,
            'averageAttendancePercentage' => 0,
        ];

        $attendancePercentages = [];

        foreach ($employees as $employee) {
            $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);

            $present = $attendanceMonthRecord->summary_present ?? 0;
            $late = $attendanceMonthRecord->summary_late ?? 0;
            $halfDay = $attendanceMonthRecord->summary_halfday ?? 0;
            $absent = $attendanceMonthRecord->summary_absent ?? 0;
            $leave = $attendanceMonthRecord->summary_leave ?? 0;
            $holiday = $attendanceMonthRecord->summary_holiday ?? 0;
            $weeklyOff = $attendanceMonthRecord->summary_weekly_off ?? 0;

            // Calculate expected working days
            $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
            $expectedWorkingDays = $daysInMonth - $holiday - $weeklyOff;

            // Calculate attendance percentage
            $presentCount = $present + $late + $halfDay;
            $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / $expectedWorkingDays) * 100, 2) : 0;

            // Get assigned shift using EmployeeShiftService (use first day of month)
            $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
            $assignedShift = $employeeShift ? $employeeShift->shift : null;

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                $statusFilter = $request->status;
                $shouldInclude = false;

                if ($statusFilter == 'Present' && $present > 0) {
                    $shouldInclude = true;
                } elseif ($statusFilter == 'Late' && $late > 0) {
                    $shouldInclude = true;
                } elseif ($statusFilter == 'Half Day' && $halfDay > 0) {
                    $shouldInclude = true;
                } elseif ($statusFilter == 'Absent' && $absent > 0) {
                    $shouldInclude = true;
                } elseif ($statusFilter == 'Leave' && $leave > 0) {
                    $shouldInclude = true;
                } elseif ($statusFilter == 'Holiday' && $holiday > 0) {
                    $shouldInclude = true;
                } elseif ($statusFilter == 'Weekly Off' && $weeklyOff > 0) {
                    $shouldInclude = true;
                }

                if (!$shouldInclude) {
                    continue;
                }
            }

            $summary['totalEmployees']++;
            $summary['totalPresent'] += $present;
            $summary['totalLate'] += $late;
            $summary['totalHalfDay'] += $halfDay;
            $summary['totalAbsent'] += $absent;
            $summary['totalLeave'] += $leave;
            $summary['totalHoliday'] += $holiday;
            $summary['totalWeeklyOff'] += $weeklyOff;
            $attendancePercentages[] = $attendancePercentage;

            $attendanceData[] = [
                'employee' => $employee,
                'attendanceMonthRecord' => $attendanceMonthRecord,
                'assignedShift' => $assignedShift,
                'present' => $present,
                'late' => $late,
                'halfDay' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'holiday' => $holiday,
                'weeklyOff' => $weeklyOff,
                'attendancePercentage' => $attendancePercentage,
                'locked' => $attendanceMonthRecord->locked ?? 0,
            ];
        }

        // Calculate average attendance percentage
        if (count($attendancePercentages) > 0) {
            $summary['averageAttendancePercentage'] = round(array_sum($attendancePercentages) / count($attendancePercentages), 2);
        }

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();

        $data = [
            'attendanceMonth' => $attendanceMonth,
            'attendanceData' => $attendanceData,
            'summary' => $summary,
            'departments' => $departments,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Monthly Attendance Register',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.monthly_register_print', $data);
    }

    /**
     * Late Attendance Report
     * Filter: From Date, To Date, Department, Employee
     * Output: Late employees only, late minutes
     */
    public function lateReport(Request $request)
    {
        $data = [
            'employees' => User::where('salary_processing', 1)->where('status', 1)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->orderBy('name');

            // Apply department filter
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            // Apply employee filter
            if ($request->has('employee_id') && $request->employee_id != '') {
                $query->where('id', $request->employee_id);
            }

            $employees = $query->get();

            // Get all unique months in the date range
            $months = [];
            $current = Carbon::parse($fromDate);
            $end = Carbon::parse($toDate);
            while ($current->lte($end)) {
                $monthKey = $current->format('Y-m');
                if (!in_array($monthKey, $months)) {
                    $months[] = $monthKey;
                }
                $current->addDay();
            }

            // Load attendance data for all months in one query
            $attendanceMonthRecords = AttendanceMonth::whereIn('attendance_month', $months)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get();

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Build late attendance data array
            $lateData = [];
            $summary = [
                'totalLateRecords' => 0,
                'totalEmployees' => 0,
                'totalLateMinutes' => 0,
                'averageLateMinutes' => 0,
            ];

            $employeeIdsWithLate = [];

            foreach ($employees as $employee) {
                $employeeAttendanceMonths = $attendanceMonthRecords->where('user_id', $employee->id);

                foreach ($employeeAttendanceMonths as $attendanceMonthRecord) {
                    $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];

                    foreach ($attendanceJson as $date => $dayData) {
                        // Check if date is within range
                        if ($date < $fromDate || $date > $toDate) {
                            continue;
                        }

                        // Check if status is Late
                        if (($dayData['status'] ?? '') != 'Late') {
                            continue;
                        }

                        // Get assigned shift using EmployeeShiftService
                        $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $date);
                        $assignedShift = $employeeShift ? $employeeShift->shift : null;

                        $lateMinutes = $dayData['late_minutes'] ?? 0;

                        $summary['totalLateRecords']++;
                        $summary['totalLateMinutes'] += $lateMinutes;

                        if (!in_array($employee->id, $employeeIdsWithLate)) {
                            $employeeIdsWithLate[] = $employee->id;
                        }

                        $lateData[] = [
                            'employee' => $employee,
                            'assignedShift' => $assignedShift,
                            'attendanceDate' => $date,
                            'checkIn' => $dayData['check_in'] ?? '-',
                            'lateMinutes' => $lateMinutes,
                            'systemRemark' => $dayData['system_remark'] ?? '-',
                            'hrRemark' => $dayData['remarks'] ?? '-',
                        ];
                    }
                }
            }

            $summary['totalEmployees'] = count($employeeIdsWithLate);
            $summary['averageLateMinutes'] = $summary['totalLateRecords'] > 0 ? round($summary['totalLateMinutes'] / $summary['totalLateRecords'], 2) : 0;

            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;
            $data['lateData'] = $lateData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Late Attendance Report';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.late_report', $data);
    }

    /**
     * Late Attendance Report - Print
     */
    public function lateReportPrint(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->orderBy('name');

        // Apply department filter
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        // Apply employee filter
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('id', $request->employee_id);
        }

        $employees = $query->get();

        // Get all unique months in the date range
        $months = [];
        $current = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);
        while ($current->lte($end)) {
            $monthKey = $current->format('Y-m');
            if (!in_array($monthKey, $months)) {
                $months[] = $monthKey;
            }
            $current->addDay();
        }

        // Load attendance data for all months in one query
        $attendanceMonthRecords = AttendanceMonth::whereIn('attendance_month', $months)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get();

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Build late attendance data array
        $lateData = [];
        $summary = [
            'totalLateRecords' => 0,
            'totalEmployees' => 0,
            'totalLateMinutes' => 0,
            'averageLateMinutes' => 0,
        ];

        $employeeIdsWithLate = [];

        foreach ($employees as $employee) {
            $employeeAttendanceMonths = $attendanceMonthRecords->where('user_id', $employee->id);

            foreach ($employeeAttendanceMonths as $attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];

                foreach ($attendanceJson as $date => $dayData) {
                    // Check if date is within range
                    if ($date < $fromDate || $date > $toDate) {
                        continue;
                    }

                    // Check if status is Late
                    if (($dayData['status'] ?? '') != 'Late') {
                        continue;
                    }

                    // Get assigned shift using EmployeeShiftService
                    $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $date);
                    $assignedShift = $employeeShift ? $employeeShift->shift : null;

                    $lateMinutes = $dayData['late_minutes'] ?? 0;

                    $summary['totalLateRecords']++;
                    $summary['totalLateMinutes'] += $lateMinutes;

                    if (!in_array($employee->id, $employeeIdsWithLate)) {
                        $employeeIdsWithLate[] = $employee->id;
                    }

                    $lateData[] = [
                        'employee' => $employee,
                        'assignedShift' => $assignedShift,
                        'attendanceDate' => $date,
                        'checkIn' => $dayData['check_in'] ?? '-',
                        'lateMinutes' => $lateMinutes,
                        'systemRemark' => $dayData['system_remark'] ?? '-',
                        'hrRemark' => $dayData['remarks'] ?? '-',
                    ];
                }
            }
        }

        $summary['totalEmployees'] = count($employeeIdsWithLate);
        $summary['averageLateMinutes'] = $summary['totalLateRecords'] > 0 ? round($summary['totalLateMinutes'] / $summary['totalLateRecords'], 2) : 0;

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();
        $allEmployees = User::where('salary_processing', 1)->where('status', 1)->orderBy('name')->get();

        $data = [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'lateData' => $lateData,
            'summary' => $summary,
            'departments' => $departments,
            'employees' => $allEmployees,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Late Attendance Report',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.late_report_print', $data);
    }

    /**
     * Department Attendance Report
     * Filter: Department, Month
     * Output: All employees under department
     */
    public function departmentReport(Request $request)
    {
        $data = [
            'departments' => Department::orderBy('name')->get(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('department_id') && $request->has('attendance_month')) {
            $departmentId = $request->department_id;
            $attendanceMonth = $request->attendance_month;

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->where('department_id', $departmentId)
                          ->orderBy('name');

            $employees = $query->get();

            // Load attendance data for all employees in one query
            $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get()
                                                     ->keyBy('user_id');

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Build attendance data array
            $attendanceData = [];
            $summary = [
                'totalEmployees' => 0,
                'totalPresent' => 0,
                'totalLate' => 0,
                'totalAbsent' => 0,
                'averageAttendancePercentage' => 0,
            ];

            $attendancePercentages = [];

            foreach ($employees as $employee) {
                $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);

                $present = $attendanceMonthRecord->summary_present ?? 0;
                $late = $attendanceMonthRecord->summary_late ?? 0;
                $halfDay = $attendanceMonthRecord->summary_halfday ?? 0;
                $absent = $attendanceMonthRecord->summary_absent ?? 0;
                $leave = $attendanceMonthRecord->summary_leave ?? 0;
                $holiday = $attendanceMonthRecord->summary_holiday ?? 0;
                $weeklyOff = $attendanceMonthRecord->summary_weekly_off ?? 0;

                // Calculate expected working days
                $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
                $expectedWorkingDays = $daysInMonth - $holiday - $weeklyOff;

                // Calculate attendance percentage
                $presentCount = $present + $late + $halfDay;
                $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / $expectedWorkingDays) * 100, 2) : 0;

                // Get assigned shift using EmployeeShiftService (use first day of month)
                $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
                $assignedShift = $employeeShift ? $employeeShift->shift : null;

                $summary['totalEmployees']++;
                $summary['totalPresent'] += $present;
                $summary['totalLate'] += $late;
                $summary['totalAbsent'] += $absent;
                $attendancePercentages[] = $attendancePercentage;

                $attendanceData[] = [
                    'employee' => $employee,
                    'attendanceMonthRecord' => $attendanceMonthRecord,
                    'assignedShift' => $assignedShift,
                    'present' => $present,
                    'late' => $late,
                    'halfDay' => $halfDay,
                    'absent' => $absent,
                    'leave' => $leave,
                    'holiday' => $holiday,
                    'weeklyOff' => $weeklyOff,
                    'attendancePercentage' => $attendancePercentage,
                ];
            }

            // Calculate average attendance percentage
            if (count($attendancePercentages) > 0) {
                $summary['averageAttendancePercentage'] = round(array_sum($attendancePercentages) / count($attendancePercentages), 2);
            }

            $data['departmentId'] = $departmentId;
            $data['attendanceMonth'] = $attendanceMonth;
            $data['attendanceData'] = $attendanceData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Department Attendance Report';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.department_report', $data);
    }

    /**
     * Department Attendance Report - Print
     */
    public function departmentReportPrint(Request $request)
    {
        $departmentId = $request->department_id;
        $attendanceMonth = $request->attendance_month;

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->where('department_id', $departmentId)
                      ->orderBy('name');

        $employees = $query->get();

        // Load attendance data for all employees in one query
        $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get()
                                                 ->keyBy('user_id');

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Build attendance data array
        $attendanceData = [];
        $summary = [
            'totalEmployees' => 0,
            'totalPresent' => 0,
            'totalLate' => 0,
            'totalAbsent' => 0,
            'averageAttendancePercentage' => 0,
        ];

        $attendancePercentages = [];

        foreach ($employees as $employee) {
            $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);

            $present = $attendanceMonthRecord->summary_present ?? 0;
            $late = $attendanceMonthRecord->summary_late ?? 0;
            $halfDay = $attendanceMonthRecord->summary_halfday ?? 0;
            $absent = $attendanceMonthRecord->summary_absent ?? 0;
            $leave = $attendanceMonthRecord->summary_leave ?? 0;
            $holiday = $attendanceMonthRecord->summary_holiday ?? 0;
            $weeklyOff = $attendanceMonthRecord->summary_weekly_off ?? 0;

            // Calculate expected working days
            $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
            $expectedWorkingDays = $daysInMonth - $holiday - $weeklyOff;

            // Calculate attendance percentage
            $presentCount = $present + $late + $halfDay;
            $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / $expectedWorkingDays) * 100, 2) : 0;

            // Get assigned shift using EmployeeShiftService (use first day of month)
            $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
            $assignedShift = $employeeShift ? $employeeShift->shift : null;

            $summary['totalEmployees']++;
            $summary['totalPresent'] += $present;
            $summary['totalLate'] += $late;
            $summary['totalAbsent'] += $absent;
            $attendancePercentages[] = $attendancePercentage;

            $attendanceData[] = [
                'employee' => $employee,
                'attendanceMonthRecord' => $attendanceMonthRecord,
                'assignedShift' => $assignedShift,
                'present' => $present,
                'late' => $late,
                'halfDay' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'holiday' => $holiday,
                'weeklyOff' => $weeklyOff,
                'attendancePercentage' => $attendancePercentage,
            ];
        }

        // Calculate average attendance percentage
        if (count($attendancePercentages) > 0) {
            $summary['averageAttendancePercentage'] = round(array_sum($attendancePercentages) / count($attendancePercentages), 2);
        }

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();

        $data = [
            'departmentId' => $departmentId,
            'attendanceMonth' => $attendanceMonth,
            'attendanceData' => $attendanceData,
            'summary' => $summary,
            'departments' => $departments,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Department Attendance Report',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.department_report_print', $data);
    }

    /**
     * Absent Attendance Report
     * Filter: From Date, To Date, Department, Employee
     * Output: Absent employees only
     */
    public function absentReport(Request $request)
    {
        $data = [
            'employees' => User::where('salary_processing', 1)->where('status', 1)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->orderBy('name');

            // Apply department filter
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            // Apply employee filter
            if ($request->has('employee_id') && $request->employee_id != '') {
                $query->where('id', $request->employee_id);
            }

            $employees = $query->get();

            // Get all unique months in the date range
            $months = [];
            $current = Carbon::parse($fromDate);
            $end = Carbon::parse($toDate);
            while ($current->lte($end)) {
                $monthKey = $current->format('Y-m');
                if (!in_array($monthKey, $months)) {
                    $months[] = $monthKey;
                }
                $current->addDay();
            }

            // Load attendance data for all months in one query
            $attendanceMonthRecords = AttendanceMonth::whereIn('attendance_month', $months)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get();

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Build absent attendance data array
            $absentData = [];
            $summary = [
                'totalAbsentRecords' => 0,
                'totalEmployees' => 0,
                'totalAbsentDays' => 0,
                'averageAbsentDays' => 0,
            ];

            $employeeIdsWithAbsent = [];

            foreach ($employees as $employee) {
                $employeeAttendanceMonths = $attendanceMonthRecords->where('user_id', $employee->id);

                foreach ($employeeAttendanceMonths as $attendanceMonthRecord) {
                    $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];

                    foreach ($attendanceJson as $date => $dayData) {
                        // Check if date is within range
                        if ($date < $fromDate || $date > $toDate) {
                            continue;
                        }

                        // Check if status is Absent
                        if (($dayData['status'] ?? '') != 'Absent') {
                            continue;
                        }

                        // Get assigned shift using EmployeeShiftService
                        $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $date);
                        $assignedShift = $employeeShift ? $employeeShift->shift : null;

                        $summary['totalAbsentRecords']++;
                        $summary['totalAbsentDays']++;

                        if (!in_array($employee->id, $employeeIdsWithAbsent)) {
                            $employeeIdsWithAbsent[] = $employee->id;
                        }

                        $absentData[] = [
                            'employee' => $employee,
                            'assignedShift' => $assignedShift,
                            'attendanceDate' => $date,
                            'status' => 'Absent',
                            'systemRemark' => $dayData['system_remark'] ?? '-',
                            'hrRemark' => $dayData['remarks'] ?? '-',
                        ];
                    }
                }
            }

            $summary['totalEmployees'] = count($employeeIdsWithAbsent);
            $summary['averageAbsentDays'] = $summary['totalEmployees'] > 0 ? round($summary['totalAbsentDays'] / $summary['totalEmployees'], 2) : 0;

            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;
            $data['absentData'] = $absentData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Absent Attendance Report';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.absent_report', $data);
    }

    /**
     * Absent Attendance Report - Print
     */
    public function absentReportPrint(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->orderBy('name');

        // Apply department filter
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        // Apply employee filter
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('id', $request->employee_id);
        }

        $employees = $query->get();

        // Get all unique months in the date range
        $months = [];
        $current = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);
        while ($current->lte($end)) {
            $monthKey = $current->format('Y-m');
            if (!in_array($monthKey, $months)) {
                $months[] = $monthKey;
            }
            $current->addDay();
        }

        // Load attendance data for all months in one query
        $attendanceMonthRecords = AttendanceMonth::whereIn('attendance_month', $months)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get();

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Build absent attendance data array
        $absentData = [];
        $summary = [
            'totalAbsentRecords' => 0,
            'totalEmployees' => 0,
            'totalAbsentDays' => 0,
            'averageAbsentDays' => 0,
        ];

        $employeeIdsWithAbsent = [];

        foreach ($employees as $employee) {
            $employeeAttendanceMonths = $attendanceMonthRecords->where('user_id', $employee->id);

            foreach ($employeeAttendanceMonths as $attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];

                foreach ($attendanceJson as $date => $dayData) {
                    // Check if date is within range
                    if ($date < $fromDate || $date > $toDate) {
                        continue;
                    }

                    // Check if status is Absent
                    if (($dayData['status'] ?? '') != 'Absent') {
                        continue;
                    }

                    // Get assigned shift using EmployeeShiftService
                    $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $date);
                    $assignedShift = $employeeShift ? $employeeShift->shift : null;

                    $summary['totalAbsentRecords']++;
                    $summary['totalAbsentDays']++;

                    if (!in_array($employee->id, $employeeIdsWithAbsent)) {
                        $employeeIdsWithAbsent[] = $employee->id;
                    }

                    $absentData[] = [
                        'employee' => $employee,
                        'assignedShift' => $assignedShift,
                        'attendanceDate' => $date,
                        'status' => 'Absent',
                        'systemRemark' => $dayData['system_remark'] ?? '-',
                        'hrRemark' => $dayData['remarks'] ?? '-',
                    ];
                }
            }
        }

        $summary['totalEmployees'] = count($employeeIdsWithAbsent);
        $summary['averageAbsentDays'] = $summary['totalEmployees'] > 0 ? round($summary['totalAbsentDays'] / $summary['totalEmployees'], 2) : 0;

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();
        $allEmployees = User::where('salary_processing', 1)->where('status', 1)->orderBy('name')->get();

        $data = [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'absentData' => $absentData,
            'summary' => $summary,
            'departments' => $departments,
            'employees' => $allEmployees,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Absent Attendance Report',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.absent_report_print', $data);
    }

    /**
     * Leave Attendance Report
     * Filter: From Date, To Date, Department, Employee
     * Output: Leave employees only
     */
    public function leaveReport(Request $request)
    {
        $data = [
            'employees' => User::where('salary_processing', 1)->where('status', 1)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->orderBy('name');

            // Apply department filter
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            // Apply employee filter
            if ($request->has('employee_id') && $request->employee_id != '') {
                $query->where('id', $request->employee_id);
            }

            $employees = $query->get();

            // Get all unique months in the date range
            $months = [];
            $current = Carbon::parse($fromDate);
            $end = Carbon::parse($toDate);
            while ($current->lte($end)) {
                $monthKey = $current->format('Y-m');
                if (!in_array($monthKey, $months)) {
                    $months[] = $monthKey;
                }
                $current->addDay();
            }

            // Load attendance data for all months in one query
            $attendanceMonthRecords = AttendanceMonth::whereIn('attendance_month', $months)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get();

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Build leave attendance data array
            $leaveData = [];
            $summary = [
                'totalLeaveRecords' => 0,
                'totalEmployees' => 0,
                'paidLeave' => 0,
                'unpaidLeave' => 0,
            ];

            $employeeIdsWithLeave = [];

            foreach ($employees as $employee) {
                $employeeAttendanceMonths = $attendanceMonthRecords->where('user_id', $employee->id);

                foreach ($employeeAttendanceMonths as $attendanceMonthRecord) {
                    $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];

                    foreach ($attendanceJson as $date => $dayData) {
                        // Check if date is within range
                        if ($date < $fromDate || $date > $toDate) {
                            continue;
                        }

                        // Check if status is Leave
                        if (($dayData['status'] ?? '') != 'Leave') {
                            continue;
                        }

                        // Get assigned shift using EmployeeShiftService
                        $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $date);
                        $assignedShift = $employeeShift ? $employeeShift->shift : null;

                        // Determine leave type (paid/unpaid based on leave_type field if available)
                        $leaveType = $dayData['leave_type'] ?? 'Paid';
                        if ($leaveType == 'Paid') {
                            $summary['paidLeave']++;
                        } else {
                            $summary['unpaidLeave']++;
                        }

                        $summary['totalLeaveRecords']++;

                        if (!in_array($employee->id, $employeeIdsWithLeave)) {
                            $employeeIdsWithLeave[] = $employee->id;
                        }

                        $leaveData[] = [
                            'employee' => $employee,
                            'assignedShift' => $assignedShift,
                            'attendanceDate' => $date,
                            'leaveType' => $leaveType,
                            'status' => 'Leave',
                            'systemRemark' => $dayData['system_remark'] ?? '-',
                            'hrRemark' => $dayData['remarks'] ?? '-',
                        ];
                    }
                }
            }

            $summary['totalEmployees'] = count($employeeIdsWithLeave);

            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;
            $data['leaveData'] = $leaveData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Leave Attendance Report';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.leave_report', $data);
    }

    /**
     * Leave Attendance Report - Print
     */
    public function leaveReportPrint(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->orderBy('name');

        // Apply department filter
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        // Apply employee filter
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('id', $request->employee_id);
        }

        $employees = $query->get();

        // Get all unique months in the date range
        $months = [];
        $current = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);
        while ($current->lte($end)) {
            $monthKey = $current->format('Y-m');
            if (!in_array($monthKey, $months)) {
                $months[] = $monthKey;
            }
            $current->addDay();
        }

        // Load attendance data for all months in one query
        $attendanceMonthRecords = AttendanceMonth::whereIn('attendance_month', $months)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get();

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Build leave attendance data array
        $leaveData = [];
        $summary = [
            'totalLeaveRecords' => 0,
            'totalEmployees' => 0,
            'paidLeave' => 0,
            'unpaidLeave' => 0,
        ];

        $employeeIdsWithLeave = [];

        foreach ($employees as $employee) {
            $employeeAttendanceMonths = $attendanceMonthRecords->where('user_id', $employee->id);

            foreach ($employeeAttendanceMonths as $attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];

                foreach ($attendanceJson as $date => $dayData) {
                    // Check if date is within range
                    if ($date < $fromDate || $date > $toDate) {
                        continue;
                    }

                    // Check if status is Leave
                    if (($dayData['status'] ?? '') != 'Leave') {
                        continue;
                    }

                    // Get assigned shift using EmployeeShiftService
                    $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $date);
                    $assignedShift = $employeeShift ? $employeeShift->shift : null;

                    // Determine leave type (paid/unpaid based on leave_type field if available)
                    $leaveType = $dayData['leave_type'] ?? 'Paid';
                    if ($leaveType == 'Paid') {
                        $summary['paidLeave']++;
                    } else {
                        $summary['unpaidLeave']++;
                    }

                    $summary['totalLeaveRecords']++;

                    if (!in_array($employee->id, $employeeIdsWithLeave)) {
                        $employeeIdsWithLeave[] = $employee->id;
                    }

                    $leaveData[] = [
                        'employee' => $employee,
                        'assignedShift' => $assignedShift,
                        'attendanceDate' => $date,
                        'leaveType' => $leaveType,
                        'status' => 'Leave',
                        'systemRemark' => $dayData['system_remark'] ?? '-',
                        'hrRemark' => $dayData['remarks'] ?? '-',
                    ];
                }
            }
        }

        $summary['totalEmployees'] = count($employeeIdsWithLeave);

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();
        $allEmployees = User::where('salary_processing', 1)->where('status', 1)->orderBy('name')->get();

        $data = [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'leaveData' => $leaveData,
            'summary' => $summary,
            'departments' => $departments,
            'employees' => $allEmployees,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Leave Attendance Report',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.leave_report_print', $data);
    }

    /**
     * Shift Attendance Report
     * Filter: Shift, Month
     * Output: Employees assigned to shift
     */
    public function shiftReport(Request $request)
    {
        $data = [
            'shifts' => Shift::where('status', 1)->orderBy('name')->get(),
            'siteInfo' => SiteSetting::orderBy('id', 'desc')->first(),
        ];

        if ($request->has('shift_id') && $request->has('attendance_month')) {
            $shiftId = $request->shift_id;
            $attendanceMonth = $request->attendance_month;

            // Build query with eager loading to avoid N+1 queries
            $query = User::with(['department', 'designation'])
                          ->where('salary_processing', 1)
                          ->where('status', 1)
                          ->orderBy('name');

            $allEmployees = $query->get();

            // Initialize EmployeeShiftService
            $employeeShiftService = new \App\Services\EmployeeShiftService();

            // Filter employees by assigned shift using EmployeeShiftService
            $employees = [];
            foreach ($allEmployees as $employee) {
                $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
                if ($employeeShift && $employeeShift->shift_id == $shiftId) {
                    $employees[] = $employee;
                }
            }

            // Load attendance data for all employees in one query
            $employeeIds = collect($employees)->pluck('id');
            $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                     ->whereIn('user_id', $employeeIds)
                                                     ->get()
                                                     ->keyBy('user_id');

            // Build attendance data array
            $attendanceData = [];
            $summary = [
                'totalEmployees' => 0,
                'totalPresent' => 0,
                'totalLate' => 0,
                'totalAbsent' => 0,
                'averageAttendancePercentage' => 0,
            ];

            $attendancePercentages = [];

            foreach ($employees as $employee) {
                $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);

                $present = $attendanceMonthRecord->summary_present ?? 0;
                $late = $attendanceMonthRecord->summary_late ?? 0;
                $halfDay = $attendanceMonthRecord->summary_halfday ?? 0;
                $absent = $attendanceMonthRecord->summary_absent ?? 0;
                $leave = $attendanceMonthRecord->summary_leave ?? 0;
                $holiday = $attendanceMonthRecord->summary_holiday ?? 0;
                $weeklyOff = $attendanceMonthRecord->summary_weekly_off ?? 0;

                // Calculate expected working days
                $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
                $expectedWorkingDays = $daysInMonth - $holiday - $weeklyOff;

                // Calculate attendance percentage
                $presentCount = $present + $late + $halfDay;
                $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / $expectedWorkingDays) * 100, 2) : 0;

                // Get assigned shift using EmployeeShiftService
                $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
                $assignedShift = $employeeShift ? $employeeShift->shift : null;

                $summary['totalEmployees']++;
                $summary['totalPresent'] += $present;
                $summary['totalLate'] += $late;
                $summary['totalAbsent'] += $absent;
                $attendancePercentages[] = $attendancePercentage;

                $attendanceData[] = [
                    'employee' => $employee,
                    'attendanceMonthRecord' => $attendanceMonthRecord,
                    'assignedShift' => $assignedShift,
                    'present' => $present,
                    'late' => $late,
                    'halfDay' => $halfDay,
                    'absent' => $absent,
                    'leave' => $leave,
                    'holiday' => $holiday,
                    'weeklyOff' => $weeklyOff,
                    'attendancePercentage' => $attendancePercentage,
                ];
            }

            // Calculate average attendance percentage
            if (count($attendancePercentages) > 0) {
                $summary['averageAttendancePercentage'] = round(array_sum($attendancePercentages) / count($attendancePercentages), 2);
            }

            $data['shiftId'] = $shiftId;
            $data['attendanceMonth'] = $attendanceMonth;
            $data['attendanceData'] = $attendanceData;
            $data['summary'] = $summary;
            $data['companyName'] = $data['siteInfo']->company_name ?? 'Safina Payroll';
            $data['companyAddress'] = $data['siteInfo']->address ?? '';
            $data['reportTitle'] = 'Shift Attendance Report';
            $data['generatedBy'] = auth()->user()->name ?? 'System';
            $data['generatedDate'] = now()->format('Y-m-d H:i:s');
        }

        return view('admin.attendance_reports.shift_report', $data);
    }

    /**
     * Shift Attendance Report - Print
     */
    public function shiftReportPrint(Request $request)
    {
        $shiftId = $request->shift_id;
        $attendanceMonth = $request->attendance_month;

        // Build query with eager loading to avoid N+1 queries
        $query = User::with(['department', 'designation'])
                      ->where('salary_processing', 1)
                      ->where('status', 1)
                      ->orderBy('name');

        $allEmployees = $query->get();

        // Initialize EmployeeShiftService
        $employeeShiftService = new \App\Services\EmployeeShiftService();

        // Filter employees by assigned shift using EmployeeShiftService
        $employees = [];
        foreach ($allEmployees as $employee) {
            $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
            if ($employeeShift && $employeeShift->shift_id == $shiftId) {
                $employees[] = $employee;
            }
        }

        // Load attendance data for all employees in one query
        $employeeIds = collect($employees)->pluck('id');
        $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                 ->whereIn('user_id', $employeeIds)
                                                 ->get()
                                                 ->keyBy('user_id');

        // Build attendance data array
        $attendanceData = [];
        $summary = [
            'totalEmployees' => 0,
            'totalPresent' => 0,
            'totalLate' => 0,
            'totalAbsent' => 0,
            'averageAttendancePercentage' => 0,
        ];

        $attendancePercentages = [];

        foreach ($employees as $employee) {
            $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);

            $present = $attendanceMonthRecord->summary_present ?? 0;
            $late = $attendanceMonthRecord->summary_late ?? 0;
            $halfDay = $attendanceMonthRecord->summary_halfday ?? 0;
            $absent = $attendanceMonthRecord->summary_absent ?? 0;
            $leave = $attendanceMonthRecord->summary_leave ?? 0;
            $holiday = $attendanceMonthRecord->summary_holiday ?? 0;
            $weeklyOff = $attendanceMonthRecord->summary_weekly_off ?? 0;

            // Calculate expected working days
            $daysInMonth = Carbon::parse($attendanceMonth . '-01')->daysInMonth;
            $expectedWorkingDays = $daysInMonth - $holiday - $weeklyOff;

            // Calculate attendance percentage
            $presentCount = $present + $late + $halfDay;
            $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / $expectedWorkingDays) * 100, 2) : 0;

            // Get assigned shift using EmployeeShiftService
            $employeeShift = $employeeShiftService->getShiftForDate($employee->id, $attendanceMonth . '-01');
            $assignedShift = $employeeShift ? $employeeShift->shift : null;

            $summary['totalEmployees']++;
            $summary['totalPresent'] += $present;
            $summary['totalLate'] += $late;
            $summary['totalAbsent'] += $absent;
            $attendancePercentages[] = $attendancePercentage;

            $attendanceData[] = [
                'employee' => $employee,
                'attendanceMonthRecord' => $attendanceMonthRecord,
                'assignedShift' => $assignedShift,
                'present' => $present,
                'late' => $late,
                'halfDay' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'holiday' => $holiday,
                'weeklyOff' => $weeklyOff,
                'attendancePercentage' => $attendancePercentage,
            ];
        }

        // Calculate average attendance percentage
        if (count($attendancePercentages) > 0) {
            $summary['averageAttendancePercentage'] = round(array_sum($attendancePercentages) / count($attendancePercentages), 2);
        }

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $shifts = Shift::where('status', 1)->orderBy('name')->get();

        $data = [
            'shiftId' => $shiftId,
            'attendanceMonth' => $attendanceMonth,
            'attendanceData' => $attendanceData,
            'summary' => $summary,
            'shifts' => $shifts,
            'companyName' => $siteInfo->company_name ?? 'Safina Payroll',
            'companyAddress' => $siteInfo->address ?? '',
            'reportTitle' => 'Shift Attendance Report',
            'generatedBy' => auth()->user()->name ?? 'System',
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'printedDate' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.attendance_reports.shift_report_print', $data);
    }
}
