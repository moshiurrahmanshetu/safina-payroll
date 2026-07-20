<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    /**
     * Display attendance reports index
     */
    public function index()
    {
        return view('admin.attendance_reports.index');
    }

    /**
     * Employee Daily Attendance Report
     * Filter: Employee, Date
     * Output: One employee, one day
     */
    public function employeeDaily(Request $request)
    {
        return view('admin.attendance_reports.employee_daily');
    }

    /**
     * Employee Monthly Attendance Report
     * Filter: Employee, Month
     * Output: Complete monthly attendance, summary, daily records
     */
    public function employeeMonthly(Request $request)
    {
        return view('admin.attendance_reports.employee_monthly');
    }

    /**
     * Daily Attendance Register
     * Filter: Date, Department (optional), Shift (optional), Status (optional)
     * Output: All employees for one day
     */
    public function dailyRegister(Request $request)
    {
        return view('admin.attendance_reports.daily_register');
    }

    /**
     * Monthly Attendance Register
     * Filter: Month, Department, Shift
     * Output: All employees for one month, summary, daily attendance
     */
    public function monthlyRegister(Request $request)
    {
        return view('admin.attendance_reports.monthly_register');
    }

    /**
     * Late Attendance Report
     * Filter: From Date, To Date, Department, Employee
     * Output: Late employees only, late minutes
     */
    public function lateReport(Request $request)
    {
        return view('admin.attendance_reports.late_report');
    }

    /**
     * Department Attendance Report
     * Filter: Department, Month
     * Output: All employees under department
     */
    public function departmentReport(Request $request)
    {
        return view('admin.attendance_reports.department_report');
    }

    /**
     * Shift Attendance Report
     * Filter: Shift, Month
     * Output: Employees assigned to shift
     */
    public function shiftReport(Request $request)
    {
        return view('admin.attendance_reports.shift_report');
    }
}
