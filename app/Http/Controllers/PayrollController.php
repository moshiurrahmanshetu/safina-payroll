<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Models\Salary;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PayrollController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = Payroll::whereIn('approval_status', ['pending', 'returned'])->orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->whereHas('user', function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
      });
    }

    // Apply payroll_month filter
    if ($request->has('payroll_month') && $request->payroll_month) {
      $query->where('payroll_month', $request->payroll_month);
    }

    // Apply status filter
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    $payrolls = $query->with(['user', 'salaryStructure'])->get();

    return view('admin.payrolls.index', compact('payrolls'));
  }

  /**
   * Display payrolls pending approval (Manager view)
   *
   * @return \Illuminate\Http\Response
   */
  public function approval(Request $request)
  {
    $query = Payroll::where('approval_status', 'submitted')->orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->whereHas('user', function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
      });
    }

    // Apply payroll_month filter
    if ($request->has('payroll_month') && $request->payroll_month) {
      $query->where('payroll_month', $request->payroll_month);
    }

    $payrolls = $query->with(['user', 'salaryStructure', 'creator'])->get();

    return view('admin.payrolls.approval', compact('payrolls'));
  }

  /**
   * Display approved payrolls history
   *
   * @return \Illuminate\Http\Response
   */
  public function approved(Request $request)
  {
    $query = Payroll::where('approval_status', 'approved')->orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->whereHas('user', function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
      });
    }

    // Apply payroll_month filter
    if ($request->has('payroll_month') && $request->payroll_month) {
      $query->where('payroll_month', $request->payroll_month);
    }

    $payrolls = $query->with(['user', 'salaryStructure', 'approver'])->get();

    return view('admin.payrolls.approved', compact('payrolls'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    // Get users with salary_processing = 1 and who have at least one salary record
    $usersWithSalary = Salary::pluck('user_id')->toArray();
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->whereIn('id', $usersWithSalary)
                 ->orderBy('name','asc')
                 ->pluck('name','id');

    return view('admin.payrolls.create', compact('users'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
      'user_id' => 'required',
      'payroll_month' => 'required|date_format:Y-m',
      'generated_salary' => 'required|numeric|min:0',
      'bonus' => 'nullable|numeric|min:0',
      'deduction' => 'nullable|numeric|min:0',
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Check if payroll already exists for this user and month
    $existingPayroll = Payroll::where('user_id', $data['user_id'])
                              ->where('payroll_month', $data['payroll_month'])
                              ->first();
    if ($existingPayroll) {
      return redirect()->back()->withErrors(['payroll_month' => 'Payroll already exists for this employee in this month'])->withInput();
    }

    // Get salary using common method
    $salary = $this->getSalaryForPayroll($data['user_id'], $data['payroll_month']);
    
    if (!$salary) {
      return redirect()->back()->withErrors(['user_id' => 'No salary found for this employee effective before this payroll month'])->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;
    $data['salary_id'] = $salary->id;

    // Set default values for nullable fields
    $data['attendance_adjustment'] = 0;
    $data['bonus'] = $data['bonus'] ?? 0;
    $data['deduction'] = $data['deduction'] ?? 0;
    $data['approval_status'] = 'pending';

    // Calculate net_salary (no attendance_adjustment anymore)
    $data['net_salary'] = $data['generated_salary'] + $data['bonus'] - $data['deduction'];

    unset($data['_token']);

    $payroll = Payroll::create($data);

    if ($payroll) {
      $message = "You have successfully created";
      return redirect()->route('payrolls.index')->with('flash_success', $message);
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $payroll = Payroll::with(['user', 'salaryStructure'])->findorfail($id);

    return view('admin.payrolls.edit', compact('payroll'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $data = request()->except('_method');

    $validator = Validator::make($data, [
      'user_id' => 'required',
      'salary_structure_id' => 'required',
      'payroll_month' => 'required|date_format:Y-m',
      'generated_salary' => 'required|numeric|min:0',
      'bonus' => 'nullable|numeric|min:0',
      'deduction' => 'nullable|numeric|min:0',
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $payroll = Payroll::findorfail($id);

    // Only allow edit if status is pending or returned
    if ($payroll->approval_status == 'submitted' || $payroll->approval_status == 'approved') {
      return redirect()->back()->withErrors(['approval_status' => 'Submitted and Approved payrolls cannot be edited'])->withInput();
    }

    // Check if payroll already exists for this user and month (excluding current record)
    $existingPayroll = Payroll::where('user_id', $data['user_id'])
                              ->where('payroll_month', $data['payroll_month'])
                              ->where('id', '!=', $id)
                              ->first();
    if ($existingPayroll) {
      return redirect()->back()->withErrors(['payroll_month' => 'Payroll already exists for this employee in this month'])->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;

    // Set default values for nullable fields
    $data['attendance_adjustment'] = 0;
    $data['bonus'] = $data['bonus'] ?? 0;
    $data['deduction'] = $data['deduction'] ?? 0;

    // If status was returned, reset to pending when edited
    if ($payroll->approval_status == 'returned') {
      $data['approval_status'] = 'pending';
    }

    // Calculate net_salary (no attendance_adjustment anymore)
    $data['net_salary'] = $data['generated_salary'] + $data['bonus'] - $data['deduction'];

    unset($data['_token']);

    $payroll = Payroll::where('id', $id)->update($data);

    if ($payroll) {
      $message = "You have successfully Updated";
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $payroll = Payroll::findorfail($id);

    // Only allow delete if status is pending
    if ($payroll->approval_status != 'pending') {
      return redirect()->back()->withErrors(['approval_status' => 'Only pending payrolls can be deleted'])->withInput();
    }

    $deleted = Payroll::where('id', $id)->delete();
    $message = "You have successfully Deleted";
    if ($deleted) {
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withInput();
    }
  }

  /**
   * Get current salary for a user via AJAX
   *
   * @param  int  $userId
   * @return \Illuminate\Http\Response
   */
  public function getSalaryStructure($userId)
  {
    $currentSalary = Salary::where('user_id', $userId)
                          ->where('is_current', 1)
                          ->where('status', 1)
                          ->first(['id', 'basic_salary', 'house_rent', 'medical', 'transport', 'food', 'mobile', 'other_allowance', 'festival_bonus', 'late_fine', 'absent_deduction', 'advance_salary', 'tax', 'pf', 'other_deduction']);

    return response()->json([
      'current_salary' => $currentSalary
    ]);
  }

  /**
   * Get attendance summary for a user and month via AJAX
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function getAttendanceSummary(Request $request)
  {
    $userId = $request->user_id;
    $payrollMonth = $request->payroll_month; // YYYY-MM format

    // Get attendance summary from attendance_months table
    $attendanceMonth = \App\Models\AttendanceMonth::where('user_id', $userId)
                                                  ->where('attendance_month', $payrollMonth)
                                                  ->first();

    $summary = [
      'Present' => 0,
      'Late' => 0,
      'Half Day' => 0,
      'Absent' => 0,
      'Leave' => 0,
      'Holiday' => 0,
      'Weekly Off' => 0
    ];

    if ($attendanceMonth) {
      $summary['Present'] = $attendanceMonth->summary_present ?? 0;
      $summary['Late'] = $attendanceMonth->summary_late ?? 0;
      $summary['Half Day'] = $attendanceMonth->summary_halfday ?? 0;
      $summary['Absent'] = $attendanceMonth->summary_absent ?? 0;
      $summary['Leave'] = $attendanceMonth->summary_leave ?? 0;
      $summary['Holiday'] = $attendanceMonth->summary_holiday ?? 0;
      $summary['Weekly Off'] = $attendanceMonth->summary_weekly_off ?? 0;
    }

    // Get current salary for deduction calculations
    $salary = Salary::where('user_id', $userId)
                     ->where('is_current', 1)
                     ->where('status', 1)
                     ->first();

    $lateDeduction = 0;
    $absentDeduction = 0;
    $effectiveAbsent = 0;

    if ($salary) {
      $lateCount = $summary['Late'];
      $absentCount = $summary['Absent'];
      $halfDayCount = $summary['Half Day'];

      // Calculate deductions using existing payroll calculation logic
      $lateDeduction = $lateCount * $salary->late_fine;
      // Business Rule: First 4 absent days are free, deduction starts from 5th day
      $effectiveAbsent = max($absentCount - 4, 0);
      $absentDeduction = $effectiveAbsent * $salary->absent_deduction;
    }

    return response()->json([
      'attendance_summary' => $summary,
      'late_deduction' => number_format($lateDeduction, 2, '.', ''),
      'absent_deduction' => number_format($absentDeduction, 2, '.', ''),
      'effective_absent' => $effectiveAbsent
    ]);
  }

  /**
   * Get salary for user based on effective_from date
   * Finds the latest salary where effective_from <= end of payroll month
   *
   * @param  int  $userId
   * @param  string  $payrollMonth (YYYY-MM format)
   * @return \App\Models\Salary|null
   */
  private function getSalaryForPayroll($userId, $payrollMonth)
  {
    $payrollMonthDate = Carbon::parse($payrollMonth . '-01')
                               ->endOfMonth()
                               ->toDateString();
    
    return Salary::where('user_id', $userId)
                 ->where('effective_from', '<=', $payrollMonthDate)
                 ->where('status', 1)
                 ->orderBy('effective_from', 'desc')
                 ->first();
  }

  /**
   * Calculate generated salary based on current salary and attendance
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function calculateGeneratedSalary(Request $request)
  {
    $userId = $request->user_id;
    $payrollMonth = $request->payroll_month;
    $bonus = $request->bonus ?? 0;
    $deduction = $request->deduction ?? 0;

    // Get current salary from salaries table
    $salary = Salary::where('user_id', $userId)
                     ->where('is_current', 1)
                     ->where('status', 1)
                     ->first();

    if (!$salary) {
      return response()->json(['error' => 'Current salary not found for this employee'], 404);
    }

    // Get attendance summary from attendance_months table
    $attendanceMonth = \App\Models\AttendanceMonth::where('user_id', $userId)
                                                  ->where('attendance_month', $payrollMonth)
                                                  ->first();

    $summary = [
      'Present' => 0,
      'Late' => 0,
      'Half Day' => 0,
      'Absent' => 0,
      'Leave' => 0,
      'Holiday' => 0,
      'Weekly Off' => 0
    ];

    if ($attendanceMonth) {
      $summary['Present'] = $attendanceMonth->summary_present ?? 0;
      $summary['Late'] = $attendanceMonth->summary_late ?? 0;
      $summary['Half Day'] = $attendanceMonth->summary_halfday ?? 0;
      $summary['Absent'] = $attendanceMonth->summary_absent ?? 0;
      $summary['Leave'] = $attendanceMonth->summary_leave ?? 0;
      $summary['Holiday'] = $attendanceMonth->summary_holiday ?? 0;
      $summary['Weekly Off'] = $attendanceMonth->summary_weekly_off ?? 0;
    }

    $lateCount = $summary['Late'];
    $absentCount = $summary['Absent'];
    $halfDayCount = $summary['Half Day'];

    // Calculate deductions
    $lateDeduction = $lateCount * $salary->late_fine;
    // Business Rule: First 4 absent days are free, deduction starts from 5th day
    $effectiveAbsent = max($absentCount - 4, 0);
    $absentDeduction = $effectiveAbsent * $salary->absent_deduction;

    // Calculate total allowances
    $totalAllowances = $salary->basic_salary +
                      $salary->house_rent +
                      $salary->medical +
                      $salary->transport +
                      $salary->food +
                      $salary->mobile +
                      $salary->other_allowance +
                      $salary->festival_bonus;

    // Calculate total deductions
    $totalDeductions = $salary->tax +
                       $salary->pf +
                       $salary->other_deduction +
                       $salary->advance_salary +
                       $lateDeduction +
                       $absentDeduction;

    // Calculate generated salary
    $generatedSalary = $totalAllowances - $totalDeductions + $bonus - $deduction;

    // Calculate gross salary (total allowances)
    $grossSalary = $totalAllowances;

    // Calculate net salary (gross salary - total deductions)
    $netSalary = $grossSalary - $totalDeductions;

    return response()->json([
      'salary_id' => $salary->id,
      'gross_salary' => number_format($grossSalary, 2, '.', ''),
      'net_salary' => number_format($netSalary, 2, '.', ''),
      'generated_salary' => number_format($generatedSalary, 2, '.', ''),
      'attendance_summary' => $summary,
      'late_deduction' => number_format($lateDeduction, 2, '.', ''),
      'absent_deduction' => number_format($absentDeduction, 2, '.', ''),
      'effective_absent' => $effectiveAbsent
    ]);
  }

  /**
   * Submit payroll for approval
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function submit($id)
  {
    $payroll = Payroll::findorfail($id);

    // Only allow submission if status is pending
    if ($payroll->approval_status != 'pending') {
      return redirect()->back()->withErrors(['approval_status' => 'Only pending payrolls can be submitted'])->withInput();
    }

    $payroll->approval_status = 'submitted';
    $payroll->submitted_at = now();
    $payroll->updated_by = Auth::user()->id;
    $payroll->save();

    $message = "Payroll submitted for approval";
    return redirect()->route('payrolls.index')->with('flash_success', $message);
  }

  /**
   * Approve payroll
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function approve($id)
  {
    $payroll = Payroll::findorfail($id);

    // Only allow approval if status is submitted
    if ($payroll->approval_status != 'submitted') {
      return redirect()->back()->withErrors(['approval_status' => 'Only submitted payrolls can be approved'])->withInput();
    }

    $payroll->approval_status = 'approved';
    $payroll->approved_by = Auth::user()->id;
    $payroll->approved_at = now();
    $payroll->updated_by = Auth::user()->id;
    $payroll->save();

    // Lock attendance for this month when payroll is approved
    $attendanceMonth = \App\Models\AttendanceMonth::where('user_id', $payroll->user_id)
                                                  ->where('attendance_month', $payroll->payroll_month)
                                                  ->first();
    if ($attendanceMonth) {
      $attendanceMonth->lock();
    }

    $message = "Payroll approved successfully";
    return redirect()->route('payrolls.index')->with('flash_success', $message);
  }

  /**
   * Return payroll with remark
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function returnPayroll(Request $request, $id)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
      'approval_remark' => 'required|string',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $payroll = Payroll::findorfail($id);

    // Only allow return if status is submitted
    if ($payroll->approval_status != 'submitted') {
      return redirect()->back()->withErrors(['approval_status' => 'Only submitted payrolls can be returned'])->withInput();
    }

    $payroll->approval_status = 'returned';
    $payroll->returned_by = Auth::user()->id;
    $payroll->returned_at = now();
    $payroll->approval_remark = $data['approval_remark'];
    $payroll->updated_by = Auth::user()->id;
    $payroll->save();

    $message = "Payroll returned successfully";
    return redirect()->route('payrolls.index')->with('flash_success', $message);
  }

  /**
   * Show payroll details with approval history
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $payroll = Payroll::with(['user', 'salaryStructure', 'creator', 'updater', 'approver', 'returner'])->findorfail($id);

    // Load attendance month data
    $attendanceMonth = \App\Models\AttendanceMonth::where('user_id', $payroll->user_id)
                                                  ->where('attendance_month', $payroll->payroll_month)
                                                  ->first();

    // Load salary data for components
    $salary = Salary::where('user_id', $payroll->user_id)
                     ->where('is_current', 1)
                     ->where('status', 1)
                     ->first();

    // Calculate attendance summary and deductions
    $attendanceSummary = [
      'Present' => $attendanceMonth ? ($attendanceMonth->summary_present ?? 0) : 0,
      'Late' => $attendanceMonth ? ($attendanceMonth->summary_late ?? 0) : 0,
      'Half Day' => $attendanceMonth ? ($attendanceMonth->summary_halfday ?? 0) : 0,
      'Absent' => $attendanceMonth ? ($attendanceMonth->summary_absent ?? 0) : 0,
      'Leave' => $attendanceMonth ? ($attendanceMonth->summary_leave ?? 0) : 0,
      'Holiday' => $attendanceMonth ? ($attendanceMonth->summary_holiday ?? 0) : 0,
      'Weekly Off' => $attendanceMonth ? ($attendanceMonth->summary_weekly_off ?? 0) : 0,
    ];

    $lateDeduction = 0;
    $absentDeduction = 0;
    $effectiveAbsent = 0;

    if ($salary) {
      $lateCount = $attendanceSummary['Late'];
      $absentCount = $attendanceSummary['Absent'];
      $halfDayCount = $attendanceSummary['Half Day'];

      $lateDeduction = $lateCount * $salary->late_fine;
      // Business Rule: First 4 absent days are free, deduction starts from 5th day
      $effectiveAbsent = max($absentCount - 4, 0);
      $absentDeduction = $effectiveAbsent * $salary->absent_deduction;
    }

    return view('admin.payrolls.show', compact('payroll', 'attendanceSummary', 'lateDeduction', 'absentDeduction', 'effectiveAbsent', 'salary'));
  }

  /**
   * Print payslip
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function payslipPrint($id)
  {
    $payroll = Payroll::with(['user', 'salaryStructure', 'creator', 'updater', 'approver', 'returner'])->findorfail($id);

    // Load attendance month data
    $attendanceMonth = \App\Models\AttendanceMonth::where('user_id', $payroll->user_id)
                                                  ->where('attendance_month', $payroll->payroll_month)
                                                  ->first();

    // Load salary data for components
    $salary = Salary::where('user_id', $payroll->user_id)
                     ->where('is_current', 1)
                     ->where('status', 1)
                     ->first();

    // Calculate attendance summary and deductions
    $attendanceSummary = [
      'Present' => $attendanceMonth ? ($attendanceMonth->summary_present ?? 0) : 0,
      'Late' => $attendanceMonth ? ($attendanceMonth->summary_late ?? 0) : 0,
      'Half Day' => $attendanceMonth ? ($attendanceMonth->summary_halfday ?? 0) : 0,
      'Absent' => $attendanceMonth ? ($attendanceMonth->summary_absent ?? 0) : 0,
      'Leave' => $attendanceMonth ? ($attendanceMonth->summary_leave ?? 0) : 0,
      'Holiday' => $attendanceMonth ? ($attendanceMonth->summary_holiday ?? 0) : 0,
      'Weekly Off' => $attendanceMonth ? ($attendanceMonth->summary_weekly_off ?? 0) : 0,
    ];

    $lateDeduction = 0;
    $absentDeduction = 0;
    $effectiveAbsent = 0;

    if ($salary) {
      $lateCount = $attendanceSummary['Late'];
      $absentCount = $attendanceSummary['Absent'];
      $halfDayCount = $attendanceSummary['Half Day'];

      $lateDeduction = $lateCount * $salary->late_fine;
      // Business Rule: First 4 absent days are free, deduction starts from 5th day
      $effectiveAbsent = max($absentCount - 4, 0);
      $absentDeduction = $effectiveAbsent * $salary->absent_deduction;
    }

    // Load site info
    $siteInfo = \App\Models\SiteSetting::orderBy('id', 'desc')->first();

    return view('admin.payrolls.payslip_print', compact('payroll', 'attendanceSummary', 'lateDeduction', 'absentDeduction', 'effectiveAbsent', 'salary', 'siteInfo'));
  }
}
