<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkArea;
use Illuminate\Support\Facades\Validator;

class WorkAreaController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = WorkArea::orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Apply status filter
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    $work_areas = $query->get();

    return view('admin.work_areas.index', compact('work_areas'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.work_areas.create');
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
      'name' => 'required|unique:work_areas,name',
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;

    unset($data['_token']);

    $work_area = WorkArea::create($data);

    if ($work_area) {
      $message = "You have successfully created";
      return redirect()->route('work_areas.index')->with('flash_success', $message);
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
    $work_area = WorkArea::findorfail($id);
    return view('admin.work_areas.edit', compact('work_area'));
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
      'name' => 'required|unique:work_areas,name,'.$id,
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;

    unset($data['_token']);

    $work_area = WorkArea::where('id', $id)->update($data);

    if ($work_area) {
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
    // TODO: Add check to prevent delete if work area is used by Daily Worker or Contract Worker
    $deleted = WorkArea::where('id', $id)->delete();
    $message = "You have successfully Deleted";
    if ($deleted) {
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withInput();
    }
  }
}
