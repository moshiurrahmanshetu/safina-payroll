<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Gate;
use App\Models\User;
use App\Models\Ticket;
use Validator;

class GateController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $gates = Gate::with(['users', 'tickets'])->orderBy('id','desc')->get();
    return view('admin.gates.index',compact('gates'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $users = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $tickets = Ticket::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    return view('admin.gates.create',compact('users','tickets'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data=request()->all();
    $validator=Validator::make($data,
      array(
        'name'   =>'required',
        'status' =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Create gate
    $gate=Gate::create([
      'name'   => $data['name'],
      'status' => $data['status']
    ]);

    // Assign users to this gate using pivot table
    if(isset($data['users']) && is_array($data['users'])){
      $gate->users()->sync($data['users']);
    }

    // Assign tickets to this gate
    if(isset($data['tickets']) && is_array($data['tickets'])){
      $gate->tickets()->attach($data['tickets']);
    }

    if($gate){
      $message="Gate created successfully with assigned users";
      return redirect()->route('gates.index')->with('flash_success',$message);
    }else{
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
    $gate = Gate::with(['tickets', 'users'])->findorfail($id);
    $users = User::where('status',1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $tickets = Ticket::where('status',1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $assignedUserIds = $gate->users->pluck('id')->toArray();
    $assignedTicketIds = $gate->tickets->pluck('id')->toArray();

    return view('admin.gates.edit',compact('gate','users','tickets','assignedUserIds','assignedTicketIds'));
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
    $data=request()->except('_method');
    $validator=Validator::make($data,
      array(
        'name'   =>'required',
        'status' =>'required',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Update gate
    Gate::where('id', $id)->update([
      'name'   => $data['name'],
      'status' => $data['status']
    ]);

    // Get the gate model for ticket sync
    $gate = Gate::find($id);

    // Update user assignments using pivot table
    if(isset($data['users']) && is_array($data['users'])){
      $gate->users()->sync($data['users']);
    } else {
      $gate->users()->detach();
    }

    // Sync tickets to this gate
    if(isset($data['tickets']) && is_array($data['tickets'])){
      $gate->tickets()->sync($data['tickets']);
    } else {
      $gate->tickets()->detach();
    }

    $message="Gate updated successfully with assigned users and tickets";
    return redirect()->route('gates.index')->with('flash_success',$message);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // Get the gate model
    $gate = Gate::find($id);

    // Remove users from this gate using pivot table
    $gate->users()->detach();

    // Delete the gate
    $deleted = $gate->delete();
    $message="Gate deleted successfully";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
}
