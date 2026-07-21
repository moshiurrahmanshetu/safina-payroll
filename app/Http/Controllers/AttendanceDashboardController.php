<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AttendanceMonth;
use App\Models\Department;
use App\Models\SiteSetting;
use Carbon\Carbon;

class AttendanceDashboardController extends Controller
{
    /**
     * Attendance Dashboard
     * Display comprehensive attendance overview
     */
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $currentMonth = Carbon::now()->format('Y-m');

        // Load site info
        $siteInfo = SiteSetting::orderBy('id', 'desc')->first();

        // Today's statistics
        $todayAttendance = AttendanceMonth::where('attendance_month', $currentMonth)
                                          ->whereNotNull('attendance_json')
                                          ->get();

        $todayStats = [
            'totalEmployees' => User::where('salary_processing', 1)->where('status', 1)->count(),
            'present' => 0,
            'late' => 0,
            'halfDay' => 0,
            'absent' => 0,
            'leave' => 0,
            'holiday' => 0,
            'weeklyOff' => 0,
        ];

        $employeesOnLeaveToday = [];
        $employeesLateToday = [];
        $recentAttendance = [];

        foreach ($todayAttendance as $attendanceMonth) {
            $attendanceJson = $attendanceMonth->attendance_json ?? [];
            $employee = $attendanceMonth->employee;

            if (isset($attendanceJson[$today])) {
                $dayData = $attendanceJson[$today];
                $status = $dayData['status'] ?? '';

                switch ($status) {
                    case 'Present':
                        $todayStats['present']++;
                        break;
                    case 'Late':
                        $todayStats['late']++;
                        if ($employee) {
                            $employeesLateToday[] = [
                                'employee' => $employee,
                                'department' => $employee->department,
                                'lateMinutes' => $dayData['late_minutes'] ?? 0,
                            ];
                        }
                        break;
                    case 'Half Day':
                        $todayStats['halfDay']++;
                        break;
                    case 'Absent':
                        $todayStats['absent']++;
                        break;
                    case 'Leave':
                        $todayStats['leave']++;
                        if ($employee) {
                            $employeesOnLeaveToday[] = [
                                'employee' => $employee,
                                'department' => $employee->department,
                                'leaveType' => $dayData['leave_type'] ?? 'Paid',
                            ];
                        }
                        break;
                    case 'Holiday':
                        $todayStats['holiday']++;
                        break;
                    case 'Weekly Off':
                        $todayStats['weeklyOff']++;
                        break;
                }

                // Add to recent attendance (last 15)
                if (count($recentAttendance) < 15 && $employee) {
                    $recentAttendance[] = [
                        'employee' => $employee,
                        'department' => $employee->department,
                        'checkIn' => $dayData['check_in'] ?? '-',
                        'checkOut' => $dayData['check_out'] ?? '-',
                        'status' => $status,
                        'lateMinutes' => $dayData['late_minutes'] ?? 0,
                    ];
                }
            }
        }

        // Monthly summary
        $monthlyAttendance = AttendanceMonth::where('attendance_month', $currentMonth)->get();
        $monthlyStats = [
            'totalPresent' => $monthlyAttendance->sum('summary_present'),
            'totalLate' => $monthlyAttendance->sum('summary_late'),
            'totalHalfDay' => $monthlyAttendance->sum('summary_halfday'),
            'totalAbsent' => $monthlyAttendance->sum('summary_absent'),
            'totalLeave' => $monthlyAttendance->sum('summary_leave'),
        ];

        // Calculate attendance percentage
        $totalEmployees = $todayStats['totalEmployees'];
        $daysInMonth = Carbon::now()->daysInMonth;
        $expectedWorkingDays = $daysInMonth - $monthlyAttendance->sum('summary_holiday') - $monthlyAttendance->sum('summary_weekly_off');
        $presentCount = $monthlyStats['totalPresent'] + $monthlyStats['totalLate'] + $monthlyStats['totalHalfDay'];
        $attendancePercentage = $expectedWorkingDays > 0 ? round(($presentCount / ($expectedWorkingDays * $totalEmployees)) * 100, 2) : 0;
        $monthlyStats['attendancePercentage'] = $attendancePercentage;

        // Last 12 months trend data
        $trendData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $monthAttendance = AttendanceMonth::where('attendance_month', $month)->get();
            $trendData[] = [
                'month' => Carbon::now()->subMonths($i)->format('M Y'),
                'present' => $monthAttendance->sum('summary_present'),
                'late' => $monthAttendance->sum('summary_late'),
                'absent' => $monthAttendance->sum('summary_absent'),
            ];
        }

        // Department wise attendance
        $departments = Department::orderBy('name')->get();
        $departmentAttendance = [];
        foreach ($departments as $department) {
            $deptEmployees = User::where('department_id', $department->id)
                                 ->where('salary_processing', 1)
                                 ->where('status', 1)
                                 ->pluck('id');
            $deptAttendance = AttendanceMonth::where('attendance_month', $currentMonth)
                                             ->whereIn('user_id', $deptEmployees)
                                             ->get();
            $deptPresent = $deptAttendance->sum('summary_present') + $deptAttendance->sum('summary_late') + $deptAttendance->sum('summary_halfday');
            $deptHoliday = $deptAttendance->sum('summary_holiday');
            $deptWeeklyOff = $deptAttendance->sum('summary_weekly_off');
            $deptExpectedDays = $daysInMonth - $deptHoliday - $deptWeeklyOff;
            $deptAttendancePercentage = $deptExpectedDays > 0 && count($deptEmployees) > 0 ? round(($deptPresent / ($deptExpectedDays * count($deptEmployees))) * 100, 2) : 0;
            $departmentAttendance[] = [
                'department' => $department->name,
                'attendancePercentage' => $deptAttendancePercentage,
            ];
        }

        // Weekly attendance (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayName = Carbon::now()->subDays($i)->format('D');
            $dayAttendance = AttendanceMonth::where('attendance_month', Carbon::now()->subDays($i)->format('Y-m'))
                                          ->whereNotNull('attendance_json')
                                          ->get();
            $dayPresent = 0;
            foreach ($dayAttendance as $attendance) {
                $json = $attendance->attendance_json ?? [];
                if (isset($json[$date])) {
                    $status = $json[$date]['status'] ?? '';
                    if (in_array($status, ['Present', 'Late', 'Half Day'])) {
                        $dayPresent++;
                    }
                }
            }
            $weeklyData[] = [
                'day' => $dayName,
                'present' => $dayPresent,
            ];
        }

        // Attendance distribution (current month)
        $distribution = [
            'present' => $monthlyStats['totalPresent'],
            'late' => $monthlyStats['totalLate'],
            'halfDay' => $monthlyStats['totalHalfDay'],
            'absent' => $monthlyStats['totalAbsent'],
            'leave' => $monthlyStats['totalLeave'],
        ];

        $data = [
            'siteInfo' => $siteInfo,
            'todayStats' => $todayStats,
            'monthlyStats' => $monthlyStats,
            'trendData' => $trendData,
            'departmentAttendance' => $departmentAttendance,
            'weeklyData' => $weeklyData,
            'distribution' => $distribution,
            'employeesOnLeaveToday' => $employeesOnLeaveToday,
            'employeesLateToday' => $employeesLateToday,
            'recentAttendance' => $recentAttendance,
        ];

        return view('admin.attendance.dashboard', $data);
    }
}
