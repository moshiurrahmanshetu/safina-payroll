<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Models\Purpose;

class PurposeController extends Controller
{

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $purposes = Purpose::all();
    return view('admin.purposes.create',compact('purposes')); 
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
        'purpose_type'  =>'required',
        'name'          =>'required|unique:purposes|string|max:250',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $purposes=Purpose::create($data);
    if($purposes){
      $message="You have successfully created";
      return redirect()->back()->with('flash_success',$message);
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning',$message);
    }
  }

  public function edit($id)
  {
    $purposes=Purpose::findorfail($id);
    return view('admin.purposes.edit',compact('purposes'));
  }

  public function update(Request $request, $id)
  {
    $data=request()->all();    
    $validator=Validator::make($data,
      array(
        'purpose_type'  =>'required',
        'name'          =>'required|string|max:100|unique:purposes,name,'.$id,
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    unset($data['_token']); unset($data['_method']);
    $purposes=Purpose::where('id', $id)->update($data);
    if($purposes){
      $message="You Have Successfully Updated";
      return redirect()->route('purpose.create')->with('flash_success',$message);
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning',$message);
    }
  }

}
