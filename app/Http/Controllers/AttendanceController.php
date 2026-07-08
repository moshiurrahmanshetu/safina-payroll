<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = Attendance::orderBy('attendance_date','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->whereHas('user', function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
      });
    }

    // Apply status filter
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    // Apply date range filter
    if ($request->has('date_from') && $request->date_from) {
      $query->where('attendance_date', '>=', $request->date_from);
    }
    if ($request->has('date_to') && $request->date_to) {
      $query->where('attendance_date', '<=', $request->date_to);
    }

    $attendances = $query->with(['user', 'creator'])->get();

    return view('admin.attendances.index', compact('attendances'));
  }

  /**
   * Show the form for creating a new resource.
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
      'attendance_date' => 'required|date',
      'check_in' => 'nullable|date_format:H:i',
      'check_out' => 'nullable|date_format:H:i',
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Check if attendance already exists for this user and date
    $existingAttendance = Attendance::where('user_id', $data['user_id'])
                                      ->where('attendance_date', $data['attendance_date'])
                                      ->first();
    if ($existingAttendance) {
      return redirect()->back()->withErrors(['attendance_date' => 'Attendance already exists for this employee on this date'])->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;

    unset($data['_token']);

    $attendance = Attendance::create($data);

    if ($attendance) {
      $message = "You have successfully created";
      return redirect()->route('attendances.index')->with('flash_success', $message);
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $attendance = Attendance::with(['user'])->findorfail($id);

    return view('admin.attendances.edit', compact('attendance'));
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
      'attendance_date' => 'required|date',
      'check_in' => 'nullable|date_format:H:i',
      'check_out' => 'nullable|date_format:H:i',
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Check if attendance already exists for this user and date (excluding current record)
    $existingAttendance = Attendance::where('user_id', $data['user_id'])
                                      ->where('attendance_date', $data['attendance_date'])
                                      ->where('id', '!=', $id)
                                      ->first();
    if ($existingAttendance) {
      return redirect()->back()->withErrors(['attendance_date' => 'Attendance already exists for this employee on this date'])->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;

    unset($data['_token']);

    $attendance = Attendance::where('id', $id)->update($data);

    if ($attendance) {
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
    $deleted = Attendance::where('id', $id)->delete();
    $message = "You have successfully Deleted";
    if ($deleted) {
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withInput();
    }
  }
}
