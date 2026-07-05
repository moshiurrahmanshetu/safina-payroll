<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\ServiceCategory;
use Validator;

class ServiceCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $categories = ServiceCategory::orderBy('id','desc')->get();
    return view('admin.service_categories.index',compact('categories'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.service_categories.create');
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
    $user_id = Auth::user()->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    unset($data['_token']);
    $category=ServiceCategory::create($data);
    if($category){
      $message="You have successfully created service category";
      return redirect()->route('service_categories.index')->with('flash_success',$message);
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
    $category = ServiceCategory::findorfail($id);
    return view('admin.service_categories.edit',compact('category'));
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

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;
    unset($data['_token']);

    $category=ServiceCategory::where('id', $id)->update($data);

    if($category){
      $message="You have successfully updated service category";
      return redirect()->back()->with('flash_success',$message);
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
    $deleted=ServiceCategory::where('id',$id)->delete();
    $message="You have successfully deleted service category";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
}
