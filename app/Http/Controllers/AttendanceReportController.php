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
            $query = User::with(['department', 'designation', 'shift'])
                          ->where('status', '1')
                          ->orderBy('name');

            // Apply department filter
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            // Apply shift filter
            if ($request->has('shift_id') && $request->shift_id != '') {
                $query->where('shift_id', $request->shift_id);
            }

            $employees = $query->get();

            // Load attendance data for all employees in one query
            $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                     ->whereIn('user_id', $employees->pluck('id'))
                                                     ->get()
                                                     ->keyBy('user_id');

            // Build attendance data array
            $attendanceData = [];
            foreach ($employees as $employee) {
                $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);
                $dayData = null;

                if ($attendanceMonthRecord) {
                    $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
                    $dayData = $attendanceJson[$attendanceDate] ?? null;
                }

                // Apply status filter
                if ($request->has('status') && $request->status != '') {
                    if (!$dayData || ($dayData['status'] ?? '') != $request->status) {
                        continue;
                    }
                }

                $attendanceData[] = [
                    'employee' => $employee,
                    'attendanceMonthRecord' => $attendanceMonthRecord,
                    'dayData' => $dayData,
                ];
            }

            $data['attendanceDate'] = $attendanceDate;
            $data['attendanceData'] = $attendanceData;
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
        $query = User::with(['department', 'designation', 'shift'])
                      ->where('status', 1)
                      ->orderBy('name');

        // Apply department filter
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        // Apply shift filter
        if ($request->has('shift_id') && $request->shift_id != '') {
            $query->where('shift_id', $request->shift_id);
        }

        $employees = $query->get();

        // Load attendance data for all employees in one query
        $attendanceMonthRecords = AttendanceMonth::where('attendance_month', $attendanceMonth)
                                                 ->whereIn('user_id', $employees->pluck('id'))
                                                 ->get()
                                                 ->keyBy('user_id');

        // Build attendance data array
        $attendanceData = [];
        foreach ($employees as $employee) {
            $attendanceMonthRecord = $attendanceMonthRecords->get($employee->id);
            $dayData = null;

            if ($attendanceMonthRecord) {
                $attendanceJson = $attendanceMonthRecord->attendance_json ?? [];
                $dayData = $attendanceJson[$attendanceDate] ?? null;
            }

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                if (!$dayData || ($dayData['status'] ?? '') != $request->status) {
                    continue;
                }
            }

            $attendanceData[] = [
                'employee' => $employee,
                'attendanceMonthRecord' => $attendanceMonthRecord,
                'dayData' => $dayData,
            ];
        }

        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();
        $departments = Department::orderBy('name')->get();
        $shifts = Shift::where('status', 1)->orderBy('name')->get();

        $data = [
            'attendanceDate' => $attendanceDate,
            'attendanceData' => $attendanceData,
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
     * Filter: Month, Department, Shift
     * Output: All employees for one month, summary, daily attendance
     */
    public function monthlyRegister(Request $request)
    {
        $data = [
            'departments' => Department::orderBy('name')->get(),
            'shifts' => Shift::where('status', 1)->orderBy('name')->get(),
        ];

        return view('admin.attendance_reports.monthly_register', $data);
    }

    /**
     * Late Attendance Report
     * Filter: From Date, To Date, Department, Employee
     * Output: Late employees only, late minutes
     */
    public function lateReport(Request $request)
    {
        $data = [
            'employees' => User::where('status', 1)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ];

        return view('admin.attendance_reports.late_report', $data);
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
        ];

        return view('admin.attendance_reports.department_report', $data);
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
        ];

        return view('admin.attendance_reports.shift_report', $data);
    }
}
