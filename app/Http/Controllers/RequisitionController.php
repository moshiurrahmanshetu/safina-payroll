<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Input;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Purpose;

class RequisitionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user=Auth::user(); $userId=$user->id;
    if (Input::has('BTSubmit'))
    {
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = date('Y-m-d', strtotime(request()->start_date)); 
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = date('Y-m-d H:i:s', strtotime(request()->end_date)+86390);
      }else{
        $end_date= date('Y-m-d H:i:s'); 
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $requisitions = Requisition::with("purpose")->with("requisition_items")->where('user_id',$userId)->whereBetween('created_at', [$start_date, $end_date])->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with("purpose")->with("requisition_items")->where('user_id',$userId)->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id');
    return view('admin.requisition.index',compact('users','requisitions', 'search_array')); 
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $item_types =Category::orderBy('name','asc')->pluck('name','id')->toArray();
    $item_names =Item::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    return view('admin.requisition.create',compact('item_names','item_types')); 
  } 

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = request()->except('_method', '_token');
    //dd($data);
    $validator=Validator::make($data,array(
        'purpose_type'   =>'required',
        'purpose_id'     =>'required'
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user = Auth::user(); $userId = $user->id; $supervisor_id = $user->supervisor_id;
    if($supervisor_id==0){
      $data['counter_sign_by']=$userId;
    }else{ $data['counter_sign_by']=$supervisor_id; }

    $data['user_id']=$data['created_by']=$data['updated_by']=$userId;
    $data2 = array(); $activity = array(); $activity = $data['activity']; unset($data['activity']);  $activity1 = array();
    if(request()->has('activity1')){
      $activity1 = $data['activity1']; unset($data['activity1']);
    }
    $requisition=Requisition::create($data);
    if($requisition){
      $data2['requisition_id']=$requisition->id; $data2['warehouse_id']=0; $requisition_items='';
      foreach ($activity as $key => $value) {
        $data2['item_id']=$value['item_name'];
        $data2['name']=$value['name'];
        $data2['category_id']=$value['type'];
        if(array_key_exists($key, $activity1)){
          $data2['combinations'] =json_encode($activity1[$key], JSON_HEX_APOS);
        }else{
          $data2['combinations'] =json_encode('');
        }
        $data2['description']=$value['description'];
        $data2['measuring_unit']=$value['measuring_unit'];
        $data2['req_quantity']=$value['req_quantity'];
        $requisition_items=RequisitionItem::create($data2); 
      }
      if($requisition_items){
        $message = "You Have Successfully Created";
        return redirect()->route('requisition.create')->with('flash_success', $message);
      }else{
        $deleted = Requisition::where('id', $requisition->id)->delete();
        $message = "Something is Wrong, Please Try again";
        return redirect()->back()->withInput()->with('flash_warning',$message);
      }
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning',$message);
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
    $user = Auth::user(); $userId = $user->id;
    $users = User::pluck('name','id')->toArray();
    $requisitions = Requisition::with('requisition_items', 'purpose')->with('supervisor')->where('id', $id)->where('user_id', $userId)->first();
    return view('admin.requisition.show',compact('requisitions','user','users'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $user = Auth::user(); $userId = $user->id; $purpose=array();
    $item_names =Item::orderBy('name','asc')->where('status', 1)->pluck('name','id')->toArray();
    $requisitions = Requisition::with('requisition_items.item')->where('user_id', $userId)->where('status', 0)->where('counter_sign_status', 0)->findorfail($id);
    //dd($requisitions);
    if($requisitions){
      $purpose = Purpose::where('purpose_type', $requisitions->purpose_type)->pluck('name','id')->toArray();
    }
    $item_types =Category::orderBy('name','asc')->pluck('name','id')->toArray();
    return view('admin.requisition.edit',compact('item_names','requisitions','purpose','item_types')); 
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
    $data = request()->except('_method', '_token');
    $validator=Validator::make($data,array(
        'purpose_type'   =>'required',
        'purpose_id'     =>'required'
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    //dd($data);
    $user = Auth::user(); $userId = $user->id; $supervisor_id = $user->supervisor_id;
    if($supervisor_id==0){
      $data['counter_sign_by']=$userId;
    }else{ $data['counter_sign_by']=$supervisor_id; }
    $data['updated_by']=$userId;
    $data2 = array(); $activity = array(); $activity = $data['activity']; unset($data['activity']); $activity1 = array();
    if(request()->has('activity1')){
      $activity1 = $data['activity1']; unset($data['activity1']);
    }
    $update = Requisition::where('id', $id)->where('user_id', $userId)->where('status', 0)->where('counter_sign_status',0)->update($data);
    if($update){
      $data2['requisition_id']=$id; $data2['warehouse_id']=0; $requisition_items='';
      foreach ($activity as $key => $value) {
        $data2['item_id']=$value['item_name'];
        $data2['name']=$value['name'];
        $data2['category_id']=$value['type'];
        if(array_key_exists($key, $activity1)){
          $data2['combinations'] =json_encode($activity1[$key], JSON_HEX_APOS);
        }else{
          $data2['combinations'] =json_encode('');
        }
        $data2['description']=$value['description'];
        $data2['measuring_unit']=$value['measuring_unit'];
        $data2['req_quantity']=$value['req_quantity'];
        if(array_key_exists('id',$value)){
          $requisition_items=RequisitionItem::where('id',$value['id'])->update($data2);
        }else{
          $requisition_items=RequisitionItem::create($data2);
        }
      }
      if($requisition_items){
        $message = "You Have Successfully Updated";
        return redirect()->back()->with('flash_success', $message);
      }else{
        //$deleted = Requisition::where('id', $id)->delete();
        $message = "Something is Wrong, Please Try again";
        return redirect()->back()->withInput()->with('flash_warning',$message);
      }
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning',$message);
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
    $user = Auth::user(); $userId = $user->id;
    $deleted=Requisition::where('id',$id)->where('user_id', $userId)->where('status', 0)->where('counter_sign_status',0)->delete(); 
    $message="You have successfully Deleted";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning',$message);
    }
  }

  public function counter_sign_list()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user=Auth::user(); $userId=$user->id; $req_no='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('req_no')&&(!empty(request('req_no')))){ 
        $req_no = request()->req_no; $opt2='=';
      }else{
        $opt2='!=';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = date('Y-m-d', strtotime(request()->start_date));
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = date('Y-m-d H:i:s', strtotime(request()->end_date)+86390);
      }else{
        $end_date= date('Y-m-d H:i:s'); 
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $requisitions = Requisition::with("requisition_items","purpose")->where('counter_sign_by',$userId)->whereBetween('created_at', [$start_date, $end_date])->where('id',$opt2,$req_no)->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with("requisition_items","purpose")->where('counter_sign_by',$userId)->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id'); $search_array['req_no']=$req_no;
    return view('admin.requisition.counter_sign_list',compact('users','requisitions', 'search_array')); 
  }

  public function counter_sign_show($id)
  {
    $user = Auth::user(); $userId = $user->id;
    $users = User::pluck('name','id')->toArray();
    $requisitions = Requisition::with('requisition_items', 'purpose')->with('user')->with('supervisor')->where('id', $id)->where('counter_sign_by', $userId)->first();
    return view('admin.requisition.counter_sign_show',compact('requisitions','user','users'));
  }

  public function counter_sign_update(Request $request, $id)
  {
    $data = request()->except('_method', '_token');
    $validator=Validator::make($data,array(
        'counter_sign_status'     =>'required',
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $data2=array(); $user = Auth::user(); $userId = $user->id; $data2['updated_by']=$userId;
    $data2['counter_sign_date']=date('Y-m-d'); $data2['counter_sign_status']=$data['counter_sign_status']; $data2['supervisor_comments']=$data['supervisor_comments'];
    $update = Requisition::where('id', $id)->where('counter_sign_by', $userId)->where('status', 0)->update($data2);
    if($update){
      $message = "You Have Successfully Updated";
      return redirect()->back()->with('flash_success', $message);
    }else{
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  public function admin_requisition_list()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user_id=''; $req_no='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('user_id')&&(!empty(request('user_id')))){ 
        $user_id = request()->user_id; $opt='=';
      }else{
        $opt='!=';
      }
      if(request()->has('req_no')&&(!empty(request('req_no')))){ 
        $req_no = request()->req_no; $opt2='=';
      }else{
        $opt2='!=';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = date('Y-m-d', strtotime(request()->start_date));
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = date('Y-m-d H:i:s', strtotime(request()->end_date)+86390);
      }else{
        $end_date= date('Y-m-d H:i:s'); 
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $requisitions = Requisition::with("requisition_items")->whereBetween('created_at', [$start_date, $end_date])->where('user_id',$opt,$user_id)->where('id',$opt2,$req_no)->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with("requisition_items")->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id')->toArray();
    $search_array['user_id']=$user_id; $search_array['req_no']=$req_no;
    return view('admin.requisition.admin_requisition_list',compact('users','requisitions', 'search_array')); 
  }

  public function admin_requisition_show($id)
  {
    $user = Auth::user(); $userId = $user->id;
    $users = User::pluck('name','id')->toArray();
    $requisitions = Requisition::with('requisition_items', 'purpose')->with('user')->with('supervisor')->where('id', $id)->first();
    return view('admin.requisition.admin_requisition_show',compact('requisitions','user','users'));
  }

  public function admin_requisition_update(Request $request, $id)
  {
    $data = request()->except('_method', '_token');
    $validator=Validator::make($data,array(
        'status'     =>'required',
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user = Auth::user(); $userId = $user->id; $data['updated_by']=$userId; $data3=array(); $data2 = array(); $data3['status']=$data2['status']=$data['status']; $data3['admin_comments']=$data['admin_comments']; $data3['given_by']=$user->name; $data3['received_by']=$data['received_by'];
    if($data['stock_out_date']){ $data3['stock_out_date']=$data2['stock_out_date']=date('Y-m-d', strtotime($data['stock_out_date'])); }
    $activity = array(); $activity = $data['activity']; unset($data['activity']);
    $update = Requisition::where('id', $id)->where('status','<',3)->update($data3);
    if($update){
      $requisition_items='';
      foreach ($activity as $key => $value) {
        $data2['description']=$value['description'];
        $data2['returnable']=$value['returnable'];
        $data2['product_type']=$value['product_type'];
        $data2['given_quantity']=$value['given_quantity'];
        $requisition_items=RequisitionItem::where('id',$value['id'])->where('requisition_id',$id)->update($data2);
      }
      if($requisition_items){
        $message = "You Have Successfully Updated";
        return redirect()->back()->with('flash_success', $message);
      }else{
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }else{
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  public function admin_requisition_summary()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user_id=''; $req_no='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('user_id')&&(!empty(request('user_id')))){ 
        $user_id = request()->user_id; $opt='=';
      }else{
        $opt='!=';
      }
      if(request()->has('req_no')&&(!empty(request('req_no')))){ 
        $req_no = request()->req_no; $opt2='=';
      }else{
        $opt2='!=';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = date('Y-m-d', strtotime(request()->start_date));
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = date('Y-m-d H:i:s', strtotime(request()->end_date)+86390);
      }else{
        $end_date= date('Y-m-d H:i:s'); 
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $requisitions = Requisition::with("requisition_items","purpose")->where('status',3)->whereBetween('stock_out_date', [$start_date, $end_date])->where('user_id',$opt,$user_id)->where('id',$opt2,$req_no)->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with("requisition_items","purpose")->where('status',3)->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id')->toArray();
    $search_array['user_id']=$user_id; $search_array['req_no']=$req_no;
    return view('admin.requisition.admin_requisition_summary',compact('users','requisitions', 'search_array')); 
  }

  public function item_wise_requisition()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user_id=''; $req_no='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('user_id')&&(!empty(request('user_id')))){ 
        $user_id = request()->user_id; $opt='=';
      }else{
        $opt='!=';
      }
      if(request()->has('req_no')&&(!empty(request('req_no')))){ 
        $req_no = request()->req_no; $opt2='=';
      }else{
        $opt2='!=';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = date('Y-m-d', strtotime(request()->start_date));
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = date('Y-m-d H:i:s', strtotime(request()->end_date)+86390);
      }else{
        $end_date= date('Y-m-d H:i:s'); 
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $requisition_ids = Requisition::where('status',3)->whereBetween('stock_out_date', [$start_date, $end_date])->where('user_id',$opt,$user_id)->orderBy('id','desc')->pluck('id','id');
      $requisition_items = RequisitionItem::with('item')->select(\DB::raw('sum(given_quantity) as given_quantity, item_id'))->whereIn("requisition_id",$requisition_ids)->where('status',3)->whereBetween('stock_out_date', [$start_date, $end_date])->groupBy('item_id')->get();
    }else{
      $requisition_ids = Requisition::where('status',3)->orderBy('id','desc')->pluck('id','id');
      $requisition_items = RequisitionItem::with('item')->select(\DB::raw('sum(given_quantity) as given_quantity, item_id'))->whereIn("requisition_id",$requisition_ids)->where('status',3)->groupBy('item_id')->get();
    }
    //dd($requisition_items);
    $users = User::pluck('name','id')->toArray();
    $search_array['user_id']=$user_id; $search_array['req_no']=$req_no;
    return view('admin.requisition.item_wise_requisition',compact('users','requisition_items', 'search_array')); 
  }

  public function report_stockout()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user_id=''; $req_no='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('user_id')&&(!empty(request('user_id')))){ 
        $user_id = request()->user_id; $opt='=';
      }else{
        $opt='!=';
      }
      if(request()->has('req_no')&&(!empty(request('req_no')))){ 
        $req_no = request()->req_no; $opt2='=';
      }else{
        $opt2='!=';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = date('Y-m-d', strtotime(request()->start_date));
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = date('Y-m-d H:i:s', strtotime(request()->end_date)+86390);
      }else{
        $end_date= date('Y-m-d H:i:s'); 
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $requisitions = Requisition::with("requisition_items","purpose")->where('status',3)->whereBetween('stock_out_date', [$start_date, $end_date])->where('user_id',$opt,$user_id)->where('id',$opt2,$req_no)->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with("requisition_items","purpose")->where('status',3)->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id')->toArray();
    $search_array['user_id']=$user_id; $search_array['req_no']=$req_no;
    return view('admin.requisition.report_stockout',compact('users','requisitions', 'search_array')); 
  }


}