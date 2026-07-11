<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;

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
    // Get users with salary_processing = 1 and who have a salary structure
    $usersWithSalaryStructure = SalaryStructure::pluck('user_id')->toArray();
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->whereIn('id', $usersWithSalaryStructure)
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

    // Check if payroll already exists for this user and month
    $existingPayroll = Payroll::where('user_id', $data['user_id'])
                              ->where('payroll_month', $data['payroll_month'])
                              ->first();
    if ($existingPayroll) {
      return redirect()->back()->withErrors(['payroll_month' => 'Payroll already exists for this employee in this month'])->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;

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
   * Get salary structures for a user via AJAX
   *
   * @param  int  $userId
   * @return \Illuminate\Http\Response
   */
  public function getSalaryStructure($userId)
  {
    $salaryStructures = SalaryStructure::where('user_id', $userId)
                                        ->where('status', 1)
                                        ->get(['id', 'basic_salary']);

    return response()->json([
      'salary_structures' => $salaryStructures
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

    // Parse the month to get start and end dates
    $startDate = date('Y-m-01', strtotime($payrollMonth));
    $endDate = date('Y-m-t', strtotime($payrollMonth));

    // Get attendance summary
    $attendances = Attendance::where('user_id', $userId)
                              ->whereBetween('attendance_date', [$startDate, $endDate])
                              ->get();

    $summary = [
      'Present' => 0,
      'Late' => 0,
      'Half Day' => 0,
      'Absent' => 0,
      'Leave' => 0,
      'Holiday' => 0,
      'Weekly Off' => 0
    ];

    foreach ($attendances as $attendance) {
      if (isset($summary[$attendance->status])) {
        $summary[$attendance->status]++;
      }
    }

    return response()->json([
      'attendance_summary' => $summary
    ]);
  }

  /**
   * Calculate generated salary based on salary structure and attendance
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function calculateGeneratedSalary(Request $request)
  {
    $salaryStructureId = $request->salary_structure_id;
    $userId = $request->user_id;
    $payrollMonth = $request->payroll_month;
    $bonus = $request->bonus ?? 0;
    $deduction = $request->deduction ?? 0;

    // Get salary structure
    $salaryStructure = SalaryStructure::where('id', $salaryStructureId)
                                       ->where('user_id', $userId)
                                       ->first();

    if (!$salaryStructure) {
      return response()->json(['error' => 'Salary structure not found'], 404);
    }

    // Parse the month to get start and end dates
    $startDate = date('Y-m-01', strtotime($payrollMonth));
    $endDate = date('Y-m-t', strtotime($payrollMonth));

    // Get attendance summary
    $attendances = Attendance::where('user_id', $userId)
                              ->whereBetween('attendance_date', [$startDate, $endDate])
                              ->get();

    $summary = [
      'Present' => 0,
      'Late' => 0,
      'Half Day' => 0,
      'Absent' => 0,
      'Leave' => 0,
      'Holiday' => 0,
      'Weekly Off' => 0
    ];

    foreach ($attendances as $attendance) {
      if (isset($summary[$attendance->status])) {
        $summary[$attendance->status]++;
      }
    }

    $lateCount = $summary['Late'];
    $absentCount = $summary['Absent'];
    $halfDayCount = $summary['Half Day'];

    // Calculate deductions
    $lateDeduction = $lateCount * $salaryStructure->late_fine;
    $effectiveAbsent = $absentCount + ($halfDayCount * 0.5);
    $absentDeduction = $effectiveAbsent * $salaryStructure->absent_deduction;

    // Calculate total allowances
    $totalAllowances = $salaryStructure->basic_salary +
                      $salaryStructure->house_rent +
                      $salaryStructure->medical +
                      $salaryStructure->transport +
                      $salaryStructure->food +
                      $salaryStructure->mobile +
                      $salaryStructure->other_allowance +
                      $salaryStructure->festival_bonus;

    // Calculate total deductions
    $totalDeductions = $salaryStructure->tax +
                       $salaryStructure->pf +
                       $salaryStructure->other_deduction +
                       $salaryStructure->advance_salary +
                       $lateDeduction +
                       $absentDeduction;

    // Calculate generated salary
    $generatedSalary = $totalAllowances - $totalDeductions + $bonus - $deduction;

    return response()->json([
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

    return view('admin.payrolls.show', compact('payroll'));
  }
}
