<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Input;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\MrsItem;

class MrsItemController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user_id=''; $mrs_no='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('mrs_no')&&(!empty(request('mrs_no')))){ 
        $mrs_no = request()->mrs_no; $opt2='=';
      }else{
        $opt2='!=';
      }
      if(request()->has('user_id')&&(!empty(request('user_id')))){ 
        $user_id = request()->user_id; $opt='=';
      }else{
        $opt='!=';
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
      $mrs_items = MrsItem::with("requisition_item","requisition.purpose")->whereBetween('received_date', [$start_date, $end_date])->where('user_id',$opt,$user_id)->where('id',$opt2,$mrs_no)->orderBy('id','desc')->get();
    }else{
      $mrs_items = MrsItem::with("requisition_item","requisition.purpose")->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id')->toArray();
    $search_array['user_id']=$user_id; $search_array['mrs_no']=$mrs_no;
    return view('admin.mrs_item.index',compact('users','mrs_items', 'search_array'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $users = User::pluck('name','id')->toArray();
    $user=Auth::user(); $receive_by[$user->id]=$user->name;
    $warehouses=Warehouse::pluck('name', 'id')->toArray();
    return view('admin.mrs_item.create',compact('users','receive_by','warehouses')); 
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
        'user_id'       =>'required',
        'item_id'       =>'required',
        'received_by'   =>'required',
        'quantity'      =>'required',
        'item_condition'=>'required',
        'received_date' =>'required',
        'warehouse_id'  =>'required',
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    if($data['received_date']){ $data['received_date']=date('Y-m-d', strtotime($data['received_date'])); }
    $req_item_id=$data['requisition_item_id']=$data['item_id']; unset($data['item_id']);
    $req_item = RequisitionItem::where('id',$req_item_id)->first();
    $data['requisition_id']=$req_item->requisition_id; $data['item_id']=$req_item->item_id;
    $data['name']=$req_item->name; $data['combinations']=$req_item->combinations;
    $data['measuring_unit']=$req_item->measuring_unit;
    $mrs_item=MrsItem::create($data);
    if($mrs_item){
      $message = "You Have Successfully Created";
      return redirect()->route('mrs_item.create')->with('flash_success', $message);
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
    $users = User::pluck('name','id')->toArray();
    $mrs_item = MrsItem::with("requisition.purpose")->with("requisition_item")->with('warehouse')->where('id', $id)->first();
    return view('admin.mrs_item.show',compact('mrs_item','users'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
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
    //
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
  
  public function my_mrs_item_list()
  {
    $search_array = array(); $search_array['start_date']=''; $search_array['end_date']=''; $user=Auth::user(); $user_id=$user->id;
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
      $mrs_items = MrsItem::with("requisition_item","requisition.purpose")->where('user_id', $user_id)->whereBetween('created_at', [$start_date, $end_date])->orderBy('id','desc')->get();
    }else{
      $mrs_items = MrsItem::with("requisition_item","requisition.purpose")->where('user_id', $user_id)->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id');
    return view('admin.mrs_item.my_mrs_item_list',compact('users','mrs_items', 'search_array'));
  }

  public function my_mrs_item_show($id)
  {
    $users = User::pluck('name','id')->toArray(); $user=Auth::user(); $user_id=$user->id;
    $mrs_item = MrsItem::with("requisition.purpose")->with("requisition_item")->with('warehouse')->where('user_id', $user_id)->where('id', $id)->first();
    return view('admin.mrs_item.my_mrs_item_show',compact('mrs_item','users'));
  }

  public function mrs_item_summary()
  {
    $search_array = array(); $item_id = '-1'; $opt2='!='; $search_array['item_id']='';
    $search_array['start_date']=''; $search_array['end_date']='';
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
      $item_id = Input::get ( 'item_id' );
      if(isset($item_id)&&(!empty($item_id))){
        $opt2='='; $item_id = $item_id; $search_array['item_id']=$item_id;
      }else{
        $opt2='!='; $item_id = '-1';
      }
      $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
      $mrs_items = MrsItem::select(\DB::raw('sum(quantity) as total_qty, item_id','received_date'))->where('item_id', $opt2, $item_id)->whereBetween('received_date', [$start_date, $end_date])->groupBy('item_id')->get();
      $stock_out_item = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('status', '3')->where('product_type', '1')->where('item_id', $opt2, $item_id)->whereBetween('stock_out_date', [$start_date, $end_date])->groupBy('item_id')->get();
    }else{
      $mrs_items = MrsItem::select(\DB::raw('sum(quantity) as total_qty, item_id','received_date'))->where('item_id', $opt2, $item_id)->groupBy('item_id')->get();
      $stock_out_item = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('status', '3')->where('product_type', '1')->where('item_id', $opt2, $item_id)->groupBy('item_id')->get();
    }
    $stock_out_items=$stock_out_item->pluck('total_qty','item_id')->toArray();
    $mrs_item_lists = MrsItem::pluck('name','item_id')->toArray();
    $users = User::pluck('name','id');
    return view('admin.mrs_item.mrs_item_summary',compact('users','mrs_items', 'search_array','mrs_item_lists','stock_out_items'));
  }

}