<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ContractWorker;
use App\Models\User;
use App\Models\WorkArea;
use Illuminate\Support\Facades\Validator;

class ContractWorkerController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = ContractWorker::orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->where('full_name', 'like', '%' . $request->search . '%')
            ->orWhere('contract_worker_id', 'like', '%' . $request->search . '%')
            ->orWhere('mobile', 'like', '%' . $request->search . '%');
    }

    // Apply work area filter
    if ($request->has('work_area_id') && $request->work_area_id) {
      $query->where('work_area_id', $request->work_area_id);
    }

    // Apply status filter
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    $contract_workers = $query->with(['workArea'])->get();
    $work_areas = WorkArea::where('status', 1)->pluck('name','id')->toArray();

    return view('admin.contract_workers.index', compact('contract_workers', 'work_areas'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $users = User::where('status', 1)->pluck('name','id');
    $work_areas = WorkArea::where('status', 1)->pluck('name','id')->toArray();
    return view('admin.contract_workers.create', compact('users', 'work_areas'));
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
      'mobile' => 'required',
      'work_area_id' => 'required',
      'contract_title' => 'required',
      'contract_start_date' => 'required',
      'contract_end_date' => 'required',
      'contract_amount' => 'required',
      'status' => 'required',
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
      $photo->move(public_path('uploads/contract_workers'), $photoName);
      $data['photo'] = 'uploads/contract_workers/' . $photoName;
    }

    unset($data['_token']);
    unset($data['photo_file']);

    $contract_worker = ContractWorker::create($data);

    if ($contract_worker) {
      $message = "You have successfully created";
      return redirect()->route('contract_workers.index')->with('flash_success', $message);
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
    $contract_worker = ContractWorker::with(['workArea'])->findorfail($id);
    $users = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $work_areas = WorkArea::where('status', 1)->pluck('name','id')->toArray();

    return view('admin.contract_workers.edit', compact('contract_worker', 'users', 'work_areas'));
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
      'mobile' => 'required',
      'work_area_id' => 'required',
      'contract_title' => 'required',
      'contract_start_date' => 'required',
      'contract_end_date' => 'required',
      'contract_amount' => 'required',
      'status' => 'required',
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
      $photo->move(public_path('uploads/contract_workers'), $photoName);
      $data['photo'] = 'uploads/contract_workers/' . $photoName;
    }

    unset($data['_token']);
    unset($data['photo_file']);

    $contract_worker = ContractWorker::where('id', $id)->update($data);

    if ($contract_worker) {
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
    $deleted = ContractWorker::where('id', $id)->delete();
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
