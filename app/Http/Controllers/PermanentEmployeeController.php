<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermanentEmployee;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Facades\Validator;

class PermanentEmployeeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = PermanentEmployee::orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->where('full_name', 'like', '%' . $request->search . '%')
            ->orWhere('employee_id', 'like', '%' . $request->search . '%')
            ->orWhere('mobile', 'like', '%' . $request->search . '%');
    }

    // Apply department filter
    if ($request->has('department_id') && $request->department_id) {
      $query->where('department_id', $request->department_id);
    }

    // Apply designation filter
    if ($request->has('designation_id') && $request->designation_id) {
      $query->where('designation_id', $request->designation_id);
    }

    // Apply employment status filter
    if ($request->has('employment_status') && $request->employment_status !== '') {
      $query->where('employment_status', $request->employment_status);
    }

    $permanent_employees = $query->with(['department', 'designation'])->get();
    $users = User::where('status', 1)->pluck('name','id');
    $departments = Department::pluck('name','id')->toArray();
    $designations = Designation::pluck('name','id')->toArray();

    return view('admin.permanent_employees.index', compact('permanent_employees', 'users', 'departments', 'designations'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $users = User::where('status', 1)->pluck('name','id');
    $departments = Department::pluck('name','id')->toArray();
    $designations = Designation::pluck('name','id')->toArray();
    return view('admin.permanent_employees.create', compact('users', 'departments', 'designations'));
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
      'full_name' => 'required',
      'father_name' => 'required',
      'mother_name' => 'required',
      'gender' => 'required',
      'mobile' => 'required',
      'joining_date' => 'required',
      'department_id' => 'required',
      'designation_id' => 'required',
      'employment_status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;

    // Handle photo upload
    if ($request->hasFile('photo')) {
      $photo = $request->file('photo');
      $photoName = time() . '.' . $photo->getClientOriginalExtension();
      $photo->move(public_path('uploads/employees'), $photoName);
      $data['photo'] = 'uploads/employees/' . $photoName;
    }

    unset($data['_token']);
    unset($data['photo_file']); // Remove the file input name if used

    $permanent_employee = PermanentEmployee::create($data);

    if ($permanent_employee) {
      $message = "You have successfully created";
      return redirect()->route('permanent_employees.index')->with('flash_success', $message);
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
    $permanent_employee = PermanentEmployee::with(['department', 'designation'])->findorfail($id);
    $users = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $departments = Department::pluck('name','id')->toArray();
    $designations = Designation::pluck('name','id')->toArray();

    return view('admin.permanent_employees.edit', compact('permanent_employee', 'users', 'departments', 'designations'));
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
      'full_name' => 'required',
      'father_name' => 'required',
      'mother_name' => 'required',
      'gender' => 'required',
      'mobile' => 'required',
      'joining_date' => 'required',
      'department_id' => 'required',
      'designation_id' => 'required',
      'employment_status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;

    // Handle photo upload
    if ($request->hasFile('photo')) {
      $photo = $request->file('photo');
      $photoName = time() . '.' . $photo->getClientOriginalExtension();
      $photo->move(public_path('uploads/employees'), $photoName);
      $data['photo'] = 'uploads/employees/' . $photoName;
    }

    unset($data['_token']);
    unset($data['photo_file']);

    $permanent_employee = PermanentEmployee::where('id', $id)->update($data);

    if ($permanent_employee) {
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
    $deleted = PermanentEmployee::where('id', $id)->delete();
    $message = "You have successfully Deleted";
    if ($deleted) {
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withInput();
    }
  }

  /**
   * Get user details via AJAX for auto-fill
   *
   * @param  int  $userId
   * @return \Illuminate\Http\Response
   */
  public function getUserDetails($userId)
  {
    $user = User::find($userId);
    if ($user) {
      return response()->json([
        'name' => $user->name,
        'mobile' => $user->mobile ?? '',
        'email' => $user->email ?? '',
        'success' => true
      ]);
    }
    return response()->json(['success' => false], 404);
  }
}
