<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Service;
use App\Models\ServiceMetaField;
use Validator;

class ServiceMetaFieldController extends Controller
{
  /**
   * Show the form for creating a new resource.
   *
   * @param  int  $service_id
   * @return \Illuminate\Http\Response
   */
  public function create($service_id)
  {
    $service = Service::findorfail($service_id);
    $meta_fields = ServiceMetaField::where('service_id', $service_id)
      ->orderBy('sort_order', 'asc')
      ->orderBy('id', 'asc')
      ->get();
    // Get field names for conditional dropdown (only select fields are valid conditional triggers)
    $conditional_fields = ServiceMetaField::where('service_id', $service_id)
      ->where('field_type', 2) // Select type
      ->pluck('field_name', 'field_name')
      ->toArray();
    return view('admin.service_meta_fields.create',compact('service','meta_fields','conditional_fields'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $service_id
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($service_id, $id)
  {
    $service = Service::findorfail($service_id);
    $meta_field = ServiceMetaField::where('id', $id)->where('service_id', $service_id)->firstOrFail();
    $meta_fields = ServiceMetaField::where('service_id', $service_id)
      ->orderBy('sort_order', 'asc')
      ->orderBy('id', 'asc')
      ->get();
    $conditional_fields = ServiceMetaField::where('service_id', $service_id)
      ->where('field_type', 2)
      ->where('id', '!=', $id) // Exclude self from conditional options
      ->pluck('field_name', 'field_name')
      ->toArray();
    return view('admin.service_meta_fields.edit', compact('service', 'meta_field', 'meta_fields', 'conditional_fields'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $service_id
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $service_id, $id)
  {
    $data = $request->all();
    $validator = Validator::make($data,
      array(
        'field_name' => 'required',
        'field_type' => 'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $meta_field = ServiceMetaField::where('id', $id)->where('service_id', $service_id)->firstOrFail();

    if($data['field_type'] != 2){
      $data['options'] = null;
    }
    // Clear conditional fields if not provided
    if(empty($data['conditional_field'])){
      $data['conditional_field'] = null;
      $data['conditional_value'] = null;
    }
    // Handle resource fields
    if(empty($data['is_resource'])){
      $data['is_resource'] = 0;
      $data['resource_key'] = null;
    }

    $meta_field->update($data);
    return redirect()->route('service_meta_fields.create', $service_id)
      ->with('flash_success', 'Meta field updated successfully');
  }

  /**
   * Update sort order via AJAX.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updateSortOrder(Request $request)
  {
    $orders = $request->input('orders', []);
    foreach($orders as $id => $sort_order){
      ServiceMetaField::where('id', $id)->update(['sort_order' => $sort_order]);
    }
    return response()->json(['success' => true]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $service_id
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $service_id)
  {
    $data=request()->all();
    $validator=Validator::make($data,
      array(
        'field_name'  =>'required',
        'field_type'  =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $data['service_id'] = $service_id;
    if($data['field_type'] != 2){
      $data['options'] = null;
    }
    // Clear conditional fields if not provided
    if(empty($data['conditional_field'])){
      $data['conditional_field'] = null;
      $data['conditional_value'] = null;
    }
    // Handle resource fields
    if(empty($data['is_resource'])){
      $data['is_resource'] = 0;
      $data['resource_key'] = null;
    }
    unset($data['_token']);

    $meta_field=ServiceMetaField::create($data);
    if($meta_field){
      $message="You have successfully created";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $service_id
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($service_id, $id)
  {
    $deleted=ServiceMetaField::where('id',$id)->where('service_id',$service_id)->delete();
    if($deleted){
      $message="You have successfully deleted";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
}
