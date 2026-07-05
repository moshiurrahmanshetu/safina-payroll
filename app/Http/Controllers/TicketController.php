<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $tickets = Ticket::orderBy('id','desc')->get();
    $users = User::pluck('name','id');
    return view('admin.tickets.index',compact('tickets','users'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.tickets.create');
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
        'price'  =>'required|numeric',
        'status' =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    unset($data['_token']);
    $tickets=Ticket::create($data);
    if($tickets){
      $message="You have successfully created";
      return redirect()->route('tickets.index')->with('flash_success',$message);
    }else{
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
    $tickets = Ticket::findorfail($id);
    $users = User::where('status',1)->orderBy('name','asc')->pluck('name','id')->toArray();

    return view('admin.tickets.edit',compact('tickets','users'));
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
        'price'  =>'required|numeric',
        'status' =>'required',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
      ->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;
    unset($data['_token']);

    $tickets=Ticket::where('id', $id)->update($data);

    if($tickets){
      $message="You have successfully Updated";
      return redirect()->back()
      ->with('flash_success',$message);
    }else{
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
    $deleted=Ticket::where('id',$id)->delete();
    $message="You have successfully Deleted";
    if($deleted){
      return redirect()->back()
      ->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
}
