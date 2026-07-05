<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Validator;
use Input;

class ItemController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    if (Input::has('search')) {
      $q = Input::get ( 'search' );
      $items = Item::with('category')->where('name','LIKE','%'.$q.'%')->orderBy('id','asc')->get();
    }else{
      $items =Item::orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id');
    return view('admin.item.index',compact('items','users'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $categories=Category::pluck('name','id')->toArray();
    return view('admin.item.create', compact('categories'));
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
    $validator=Validator::make($data,array(               
      'name'            =>'required|string|max:200',        
      'category_id'     =>'required',
      'measuring_unit'  =>'required',
    ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    $additional = $data['activity3'];
    $data['additional']=json_encode([]);
    foreach ($additional as $key => $value) {
      if ( $value['name']!='') {
        $data['additional'] = json_encode($data['activity3'], JSON_HEX_APOS);
      }
    }
    unset($data['activity3']);
    $request = $data['activity'];
    $data['attributes']=json_encode([]);
    foreach ($request as $key => $value) {
      if ( $value['name']!='') {
        $data['attributes'] = json_encode($data['activity'], JSON_HEX_APOS);
      }
    }
    unset($data['activity']);
    unset($data['_token']);
    if (Input::has('activity1')){
      $activity1=$data['activity1'];
      foreach ($data['activity1'] as $key => $value) {
        unset($data['activity1'][$key]['regular_price']);
        unset($data['activity1'][$key]['sale_price']);
      }
      $input=array_unique($data['activity1'], SORT_REGULAR);
      $keys=array_diff_key($activity1, $input);
      foreach ($keys as $key => $value) {
        unset($activity1[$key]);
      }
      unset($data['activity1']);
      $data['combination'] = json_encode($activity1, JSON_HEX_APOS);
    }else{
      $data['combination'] = json_encode([]);
    }
    $imageName=null;
      $file=request()->file('item_img');
      if($file != null){
        $imageName = time().'_'.get_file_name($file->getClientOriginalName());
        $data['item_img']=$imageName;
      }
    $items=Item::create($data);
    if($items){
      if($imageName!=null){
        $file->storeAs('admin/item',$imageName);
      }
    $message="You have successfully created";
    return redirect()->back()->with('flash_success',$message);
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
    $items = Item::where('id', $id)->first();
     if($items){
      $users = User::pluck('name','id');
      $categories=Category::pluck('name','id')->toArray();
      $activity=json_decode($items->attributes, true);
      $activity1=json_decode($items->combination, true);
      $activity3=json_decode($items->additional, true);
      return view('admin.item.edit',compact('users','items','categories','activity','activity1','activity3'));
      }else{
        return redirect()->back();
      } 
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
    $validator=Validator::make($data,array(
      'name'            =>'required',        
      'category_id'     =>'required',
      'measuring_unit'  =>'required',
    ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;
    $add = $data['activity3'];
    $data['additional']=json_encode([]);
    foreach ($add as $key => $value) {
      if ( $value['name']!='') {
        $data['additional'] = json_encode($data['activity3'], JSON_HEX_APOS);
      }
    }
    unset($data['activity3']);    
    $request = $data['activity'];
    $data['attributes']=json_encode([]);
    foreach ($request as $key => $value) {
      if ( $value['name']!='') {
        $data['attributes'] = json_encode($data['activity'], JSON_HEX_APOS);
      }
    }
    unset($data['activity']);
    if (Input::has('activity1')){
      $activity1=$data['activity1'];
      foreach ($data['activity1'] as $key => $value) {
        unset($data['activity1'][$key]['regular_price']);
        unset($data['activity1'][$key]['sale_price']);
      }
      $input=array_unique($data['activity1'], SORT_REGULAR);
      $keys=array_diff_key($activity1, $input);
      foreach ($keys as $key => $value) {
        unset($activity1[$key]);
      }
      unset($data['activity1']);
      $data['combination'] = json_encode($activity1, JSON_HEX_APOS);
    }else{
      $data['combination'] = json_encode([]);
    }
    $imageName=null;
    $file=request()->file('item_img');
    if($file != null){
      $imageName = time().'_'.get_file_name($file->getClientOriginalName());
      $data['item_img']=$imageName;
    }
    $old_image_name = $data['old_image']; 
    unset($data['_token']);
    unset($data['_method']);
    unset($data['old_image']);
    $item = Item::where('id', $id)->update($data);
    if($item){
      if($imageName!=null){
        if($old_image_name !=null){
          unlink(storage_path('app/admin/item/'.$old_image_name));
        }
        $file->storeAs('admin/item',$imageName);
      }
      $message = "You have successfully updated";
      return redirect()->back()->with('flash_success', $message);
    }else{
      $error = "data can't updated please check again";
      return redirect()->back()->with('flash_success', $error);
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
      //
  }
}
