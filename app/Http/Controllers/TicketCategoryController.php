<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TicketCategoryController extends Controller
{
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $ticket_categories = TicketCategory::all();
    return view('admin.ticket_categories.create',compact('ticket_categories'));
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
        'name'   =>'required|unique:ticket_categories',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
      ->withInput();
    }
    $ticket_category=TicketCategory::create($data);

    if($ticket_category){
      $message="You have successfully created";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
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
    $ticket_category=TicketCategory::destroy($id);
    if($ticket_category){
      $message="You have successfully deleted";
      return redirect()->route('ticket_category.create')
      ->with('flash_success',$message);
    }
  }
}
