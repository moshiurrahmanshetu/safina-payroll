<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\ServiceCategory;
use App\Models\CategoryMetaField;
use Validator;

class CategoryMetaFieldController extends Controller
{
  /**
   * Show the form for creating a new resource.
   *
   * @param  int  $category_id
   * @return \Illuminate\Http\Response
   */
  public function create($category_id)
  {
    $category = ServiceCategory::findorfail($category_id);
    $meta_fields = CategoryMetaField::where('service_category_id', $category_id)
      ->orderBy('sort_order', 'asc')
      ->orderBy('id', 'asc')
      ->get();
    // Get field names for conditional dropdown (only select fields are valid conditional triggers)
    $conditional_fields = CategoryMetaField::where('service_category_id', $category_id)
      ->where('field_type', 2) // Select type
      ->pluck('field_name', 'field_name')
      ->toArray();
    return view('admin.category_meta_fields.create',compact('category','meta_fields','conditional_fields'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $category_id
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($category_id, $id)
  {
    $category = ServiceCategory::findorfail($category_id);
    $meta_field = CategoryMetaField::where('id', $id)->where('service_category_id', $category_id)->firstOrFail();
    $meta_fields = CategoryMetaField::where('service_category_id', $category_id)
      ->orderBy('sort_order', 'asc')
      ->orderBy('id', 'asc')
      ->get();
    $conditional_fields = CategoryMetaField::where('service_category_id', $category_id)
      ->where('field_type', 2)
      ->where('id', '!=', $id) // Exclude self from conditional options
      ->pluck('field_name', 'field_name')
      ->toArray();
    return view('admin.category_meta_fields.edit', compact('category', 'meta_field', 'meta_fields', 'conditional_fields'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $category_id
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $category_id, $id)
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

    $meta_field = CategoryMetaField::where('id', $id)->where('service_category_id', $category_id)->firstOrFail();

    if($data['field_type'] != 2){
      $data['options'] = null;
    } else {
      // Handle options from dynamic option builder
      if(isset($data['options_array']) && is_array($data['options_array'])){
        $data['options'] = json_encode(array_values(array_filter($data['options_array'])));
      }
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
    return redirect()->route('category_meta_fields.create', $category_id)
      ->with('flash_success', 'Customer Information Field updated successfully');
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
      CategoryMetaField::where('id', $id)->update(['sort_order' => $sort_order]);
    }
    return response()->json(['success' => true]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $category_id
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $category_id)
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

    $data['service_category_id'] = $category_id;
    if($data['field_type'] != 2){
      $data['options'] = null;
    } else {
      // Handle options from dynamic option builder
      if(isset($data['options_array']) && is_array($data['options_array'])){
        $data['options'] = json_encode(array_values(array_filter($data['options_array'])));
      }
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
    unset($data['options_array']); // Remove the array version

    $meta_field=CategoryMetaField::create($data);
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
   * @param  int  $category_id
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($category_id, $id)
  {
    $deleted=CategoryMetaField::where('id',$id)->where('service_category_id',$category_id)->delete();
    if($deleted){
      $message="You have successfully deleted";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
}
