<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Models\Warehouse;

class WarehouseController extends Controller
{

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $warehouses = Warehouse::all();
    return view('admin.warehouses.create',compact('warehouses')); 
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
        'name'   =>'required|unique:warehouses|string|max:100',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
      ->withInput();
    }
      //print_r($data); exit;
    $warehouses=Warehouse::create($data);
    if($warehouses){
      $message="You have successfully created";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
  public function edit($id)
  {
    $warehouse=Warehouse::findorfail($id);
    return view('admin.warehouses.edit',compact('warehouse'));
  }
  public function update(Request $request, $id)
  {
    $data=request()->all();    
    $validator=Validator::make($data,
      array(
        'name'   =>'required|string|max:100|unique:warehouses,name,'.$id,
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
      ->withInput();
    }
    unset($data['_token']); unset($data['_method']);
    $warehouse=Warehouse::where('id', $id)->update($data);
    if($warehouse){
      $message="You Have Successfully Updated";
      return redirect()->route('warehouse.create')->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

}
