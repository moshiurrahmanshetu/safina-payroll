<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceMonth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
  /**
   * Display a listing of attendance months.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = AttendanceMonth::orderBy('attendance_month', 'desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->whereHas('user', function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
      });
    }

    // Apply month filter
    if ($request->has('attendance_month') && $request->attendance_month) {
      $query->where('attendance_month', $request->attendance_month);
    }

    // Apply locked filter
    if ($request->has('attendance_locked') && $request->attendance_locked !== '') {
      $query->where('attendance_locked', $request->attendance_locked);
    }

    $attendanceMonths = $query->with(['user', 'creator'])->get();

    return view('admin.attendances.index', compact('attendanceMonths'));
  }

  /**
   * Show the form for creating a new attendance month.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    // Get users with salary_processing = 1 and status = 1
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->orderBy('name','asc')
                 ->pluck('name','id');

    return view('admin.attendances.create', compact('users'));
  }

  /**
   * Show the form for daily attendance entry.
   *
   * @return \Illuminate\Http\Response
   */
  public function daily()
  {
    // Get users with salary_processing = 1 and status = 1
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->orderBy('name','asc')
                 ->pluck('name','id');

    return view('admin.attendances.daily', compact('users'));
  }

  /**
   * Show the form for bulk attendance editor.
   *
   * @return \Illuminate\Http\Response
   */
  public function bulk()
  {
    // Get users with salary_processing = 1 and status = 1
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->orderBy('name','asc')
                 ->pluck('name','id');

    return view('admin.attendances.bulk', compact('users'));
  }

  /**
   * Load existing attendance month for editing or create new blank month
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function loadMonth(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
      'user_id' => 'required',
      'attendance_month' => 'required|date_format:Y-m',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 422);
    }

    // Check if attendance month already exists
    $attendanceMonth = AttendanceMonth::where('user_id', $data['user_id'])
                                      ->where('attendance_month', $data['attendance_month'])
                                      ->first();

    if ($attendanceMonth) {
      // Return existing attendance data
      return response()->json([
        'exists' => true,
        'attendance_month' => $attendanceMonth,
        'attendance_json' => $attendanceMonth->attendance_json ?? [],
        'attendance_locked' => $attendanceMonth->attendance_locked
      ]);
    } else {
      // Return blank month data
      $daysInMonth = date('t', strtotime($data['attendance_month'] . '-01'));
      $blankJson = [];
      
      for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
        $blankJson[$dayKey] = [
          'status' => '',
          'check_in' => '',
          'check_out' => '',
          'remarks' => ''
        ];
      }

      return response()->json([
        'exists' => false,
        'attendance_json' => $blankJson,
        'attendance_locked' => false
      ]);
    }
  }

  /**
   * Load specific day's attendance data for daily entry
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function loadDay(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
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

    $dayKey = date('d', strtotime($data['attendance_date']));
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
      'attendance_locked' => $isLocked
    ]);
  }

  /**
   * Store daily attendance (updates single day JSON node)
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function dailyStore(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
      'user_id' => 'required',
      'attendance_month' => 'required|date_format:Y-m',
      'attendance_date' => 'required|date',
      'status' => 'required',
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

    // Prepare day data
    $dayKey = date('d', strtotime($data['attendance_date']));
    $dayData = [
      'status' => $data['status'],
      'check_in' => $data['check_in'] ?? '',
      'check_out' => $data['check_out'] ?? '',
      'remarks' => $data['remarks'] ?? ''
    ];

    if ($attendanceMonth) {
      // Update existing month
      $attendanceJson = $attendanceMonth->attendance_json ?? [];
      $attendanceJson[$dayKey] = $dayData;
      $attendanceMonth->attendance_json = $attendanceJson;
      $attendanceMonth->recalculateTotals();
      $attendanceMonth->updated_by = Auth::user()->id;
      $attendanceMonth->save();
    } else {
      // Create new month
      $daysInMonth = date('t', strtotime($data['attendance_month'] . '-01'));
      $blankJson = [];
      
      for ($day = 1; $day <= $daysInMonth; $day++) {
        $blankDayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
        $blankJson[$blankDayKey] = [
          'status' => '',
          'check_in' => '',
          'check_out' => '',
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
      $attendanceMonth->created_by = Auth::user()->id;
      $attendanceMonth->updated_by = Auth::user()->id;
      $attendanceMonth->save();
    }

    return redirect()->route('attendances.daily')->with('flash_success', 'Daily attendance saved successfully');
  }

  /**
   * Store bulk attendance (whole month)
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function bulkStore(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
      'user_id' => 'required',
      'attendance_month' => 'required|date_format:Y-m',
      'attendance_json' => 'required|array',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Check if attendance month already exists for this user and month
    $existingAttendanceMonth = AttendanceMonth::where('user_id', $data['user_id'])
                                              ->where('attendance_month', $data['attendance_month'])
                                              ->first();
    if ($existingAttendanceMonth) {
      return redirect()->back()->withErrors(['attendance_month' => 'Attendance already exists for this employee in this month'])->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;
    $data['attendance_locked'] = 0;

    // Calculate totals from JSON
    $attendanceMonth = new AttendanceMonth();
    $attendanceMonth->user_id = $data['user_id'];
    $attendanceMonth->attendance_month = $data['attendance_month'];
    $attendanceMonth->attendance_json = $data['attendance_json'];
    $attendanceMonth->recalculateTotals();
    $attendanceMonth->attendance_locked = 0;
    $attendanceMonth->created_by = $user_id;
    $attendanceMonth->updated_by = $user_id;
    $attendanceMonth->save();

    $message = "You have successfully created attendance month";
    return redirect()->route('attendances.index')->with('flash_success', $message);
  }

  /**
   * Store a newly created attendance month.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // Redirect to bulkStore for consistency
    return $this->bulkStore($request);
  }

  /**
   * Display the specified attendance month.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $attendanceMonth = AttendanceMonth::with(['user', 'creator', 'updater'])->findorfail($id);
    return view('admin.attendances.show', compact('attendanceMonth'));
  }

  /**
   * Show the form for editing the specified attendance month.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $attendanceMonth = AttendanceMonth::with(['user'])->findorfail($id);

    // Check if attendance is locked
    if ($attendanceMonth->attendance_locked) {
      return redirect()->back()->withErrors(['attendance_locked' => 'Locked attendance cannot be edited'])->withInput();
    }

    // Get users with salary_processing = 1 and status = 1
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->orderBy('name','asc')
                 ->pluck('name','id');

    return view('admin.attendances.edit', compact('attendanceMonth', 'users'));
  }

  /**
   * Update the specified attendance month.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $attendanceMonth = AttendanceMonth::findorfail($id);

    // Check if attendance is locked
    if ($attendanceMonth->attendance_locked) {
      return redirect()->back()->withErrors(['attendance_locked' => 'Locked attendance cannot be edited'])->withInput();
    }

    $data = request()->except('_method');

    $validator = Validator::make($data, [
      'user_id' => 'required',
      'attendance_month' => 'required|date_format:Y-m',
      'attendance_json' => 'required|array',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Update attendance JSON
    $attendanceMonth->attendance_json = $data['attendance_json'];
    // Recalculate totals from JSON
    $attendanceMonth->recalculateTotals();
    $attendanceMonth->updated_by = Auth::user()->id;
    $attendanceMonth->save();

    $message = "Attendance month updated successfully";
    return redirect()->back()->with('flash_success', $message);
  }

  /**
   * Remove the specified attendance month.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $attendanceMonth = AttendanceMonth::findorfail($id);

    // Check if attendance is locked
    if ($attendanceMonth->attendance_locked) {
      return redirect()->back()->withErrors(['attendance_locked' => 'Locked attendance cannot be deleted'])->withInput();
    }

    $attendanceMonth->delete();

    return redirect()->back()->with('flash_success', 'Attendance month deleted successfully');
  }

  /**
   * Update attendance for a specific day in a month.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updateDay(Request $request, $id)
  {
    $attendanceMonth = AttendanceMonth::findorfail($id);

    // Check if attendance is locked
    if ($attendanceMonth->attendance_locked) {
      return response()->json(['error' => 'Locked attendance cannot be edited'], 403);
    }

    $day = $request->day;
    $dayData = [
      'status' => $request->status,
      'check_in' => $request->check_in ?? null,
      'check_out' => $request->check_out ?? null,
      'remarks' => $request->remarks ?? '',
    ];

    $attendanceMonth->updateDayAttendance($day, $dayData);

    return response()->json([
      'success' => true,
      'attendance_json' => $attendanceMonth->attendance_json,
      'totals' => [
        'total_present' => $attendanceMonth->total_present,
        'total_late' => $attendanceMonth->total_late,
        'total_halfday' => $attendanceMonth->total_halfday,
        'total_absent' => $attendanceMonth->total_absent,
        'total_leave' => $attendanceMonth->total_leave,
        'total_holiday' => $attendanceMonth->total_holiday,
        'total_weekly_off' => $attendanceMonth->total_weekly_off,
      ]
    ]);
  }

  /**
   * Lock attendance month.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function lock($id)
  {
    $attendanceMonth = AttendanceMonth::findorfail($id);
    $attendanceMonth->lock();

    return redirect()->back()->with('flash_success', 'Attendance locked successfully');
  }

  /**
   * Unlock attendance month.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function unlock($id)
  {
    $attendanceMonth = AttendanceMonth::findorfail($id);
    $attendanceMonth->unlock();

    return redirect()->back()->with('flash_success', 'Attendance unlocked successfully');
  }
}
