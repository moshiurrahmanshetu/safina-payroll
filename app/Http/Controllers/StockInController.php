<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Input;
use Auth;
use Validator;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\StockIn;
use App\Models\Warehouse;
use App\Models\RequisitionItem;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\Department;
use App\Models\Item;
use App\Models\MrsItem;

class StockInController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $search_array = array(); $supplier_id = '-1'; $opt='!='; $item_id = '-1'; $opt2='!='; 
    $start_date ='2015-01-01'; $end_date= date('Y-m-d'); 
    if (Input::has('BTSubmit'))
    {
      $supplier_id = Input::get ( 'supplier_id' );
      if(isset($supplier_id)&&(!empty($supplier_id))){
        $opt='='; $supplier_id = $supplier_id;
      }else{
        $opt='!='; $supplier_id = '-1';
      }
      $item_id = Input::get ( 'item_id' );
      if(isset($item_id)&&(!empty($item_id))){
        $opt2='='; $item_id = $item_id;
      }else{
        $opt2='!='; $item_id = '-1';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = reverseDate(request()->start_date); 
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = reverseDate(request()->end_date);
      }
    }
    $stock_ins = StockIn::with('item')->with('warehouse')->where('supplier_id', $opt, $supplier_id)->where('item_id', $opt2, $item_id)->whereBetween('stock_date', [$start_date, $end_date])->orderBy('stock_date', 'desc')->get();
    $stock_in_items = StockIn::with('item')->select(\DB::raw('item_id'))->groupBy('item_id')->get();
    $items=array();
    foreach ($stock_in_items as $key => $value) {
      $items[$value->item->id]=$value->item->name;
    }
    //dd($items);
    $received_by = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $suppliers_list = Supplier::where('status', 1)->orderBy('contact_name','asc')->get();
    $suppliers=array();
    foreach ($suppliers_list as $key => $value) {
      $suppliers[$value->id]=$value->contact_name.' ( '.$value->company_name.' ) ';
    }
    $departments=Department::pluck('name', 'id')->toArray(); $departments[0]='For All Departments';
    $search_array['start_date']=$start_date; $search_array['end_date']=$end_date; $search_array['supplier_id']=$supplier_id; $search_array['item_id']=$item_id;
    return view('admin.stock_in.index',compact('suppliers','search_array','stock_ins','received_by','items','departments')); 
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $purchases = array(); $supplier_id = '-1';
    if (Input::has('BTSubmit'))
      {
        $supplier_id = Input::get ( 'supplier_id' );
        if(isset($supplier_id)&&(!empty($supplier_id))){
          $opt='='; $supplier_id = $supplier_id;
        }else{
          $opt='!='; $supplier_id = '-1';
        }
        $purchases = Purchase::with("purchase_items")->with("purchase_items.category")->where('supplier_id', $opt, $supplier_id)->orderBy('id','desc')->get();
      }
    $stock_in = StockIn::select(\DB::raw('sum(quantity) as total_qty, purchase_item_id'))->where('supplier_id', $supplier_id)->groupBy('purchase_item_id')->get();
    $stocks = array();
    foreach ($stock_in as $key => $value) {
      $stocks[$value->purchase_item_id]=$value->total_qty;
    }
    $received_by = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $suppliers_list = Supplier::where('status', 1)->orderBy('contact_name','asc')->get();
    $suppliers=array();
    foreach ($suppliers_list as $key => $value) {
      $suppliers[$value->id]=$value->contact_name.' ( '.$value->company_name.' ) ';
    }
    $warehouses=Warehouse::pluck('name', 'id')->toArray();
    $departments=Department::pluck('name', 'id')->toArray();
    return view('admin.stock_in.create',compact('suppliers','purchases','supplier_id','stocks','received_by','warehouses','departments')); 
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
    $validator=Validator::make($data,
    array(
      'received_by'   =>'required',
      'given_by'      =>'required',
      'stock_date'    =>'required',
      'warehouse_id'  =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
        ->withInput();
    }

    $data['stock_date']=date('Y-m-d',strtotime($data['stock_date'])); 
    $userId = Auth::id();
    
    $data['created_by'] = $data['updated_by'] = $userId;
    $activity = array(); $activity = $data['activity'];
    unset($data['activity']);
    foreach ($activity as $key => $value) {
      $data['item_id']=$value['item_id'];
      $data['purchase_item_id']=$value['purchase_item_id'];
      $data['combinations']=$value['combinations'];
      $data['quantity']=$value['quantity'];
      if($data['quantity']!=0){         
        $stock_ins = StockIn::create($data);
      }      
    }
    if($stock_ins){
      $message = "You Have Successfully Created";
      return redirect()->back()->with('flash_success', $message);
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
    $stock_in = StockIn::with('purchase')->with("purchase_item")->with("purchase_item.category")->findorfail($id);
    $total_stock = StockIn::select(\DB::raw('sum(quantity) as total_qty, purchase_item_id'))->where('purchase_item_id', $stock_in->purchase_item_id)->groupBy('purchase_item_id')->get();
    $total_qty=0;
    foreach ($total_stock as $key => $value) {
      $total_qty=$value->total_qty;
    }
    //dd($total_qty);
    $received_by = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $warehouses=Warehouse::pluck('name', 'id')->toArray();
    return view('admin.stock_in.edit',compact('stock_in','received_by','total_qty','warehouses')); 
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
    $validator=Validator::make($data,
    array(
      'received_by'   =>'required',
      'given_by'      =>'required',
      'stock_date'    =>'required',
      'warehouse_id'  =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
        ->withInput();
    }
    $data['stock_date']=date('Y-m-d',strtotime($data['stock_date'])); 
    $userId = Auth::id(); $data['updated_by'] = $userId;
      //dd($data);
    $stock_ins = StockIn::where('id',$id)->update($data);
    if($stock_ins){
      $message = "You Have Successfully Updated";
      return redirect()->back()->with('flash_success', $message);
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
    $deleted=StockIn::where('id',$id)->delete(); 
    $message="You have successfully Deleted";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

  public function stock_in_print(Request $request,$id='')
    {
      $search_array = array();$start_date=''; $end_date=''; 
      $item_id = Input::get ('item_id');      
      $supplier_id = Input::get ('supplier_id');
      $start_date = Input::get ('start_date'); 
      $end_date = Input::get ('end_date');
      $start_date=date('Y-m-d', strtotime($start_date));
      $end_date=date('Y-m-d', strtotime($end_date));
      if($supplier_id==-1)
      {
         $opt='!=';
      }
      else{
         $opt='=';
      }
      if($item_id==-1)
      {
         $opt2='!=';
      }
      else{
         $opt2='=';
      }
    $stock_ins = StockIn::with('item')->with('warehouse')->where('supplier_id', $opt, $supplier_id)->where('item_id', $opt2, $item_id)->whereBetween('stock_date', [$start_date, $end_date])->orderBy('stock_date', 'desc')->get();
    $items=array();
    foreach ($stock_ins as $key => $value) {
      $items[$value->item->id]=$value->item->name;
    }
    //dd($stock_ins);
    $received_by = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $suppliers_list = Supplier::where('status', 1)->orderBy('contact_name','asc')->get();
    $suppliers=array();
    foreach ($suppliers_list as $key => $value) {
      $suppliers[$value->id]=$value->contact_name.' ( '.$value->company_name.' ) ';
    }
    $search_array['start_date']=$start_date; $search_array['end_date']=$end_date; $search_array['supplier_id']=$supplier_id; $search_array['item_id']=$item_id;
    $site_info = SiteSetting::orderBy('id', 'desc')->first();
    return View('stock_in_report',compact('suppliers','search_array','stock_ins','received_by','items','site_info'));
  }

  public function stock_summary()
  {
    $search_array = array(); $department_id = '-1'; $opt='!='; $item_id = '-1'; $opt2='!='; 
    $start_date ='2015-01-01'; $end_date= date('Y-m-d'); 
    if (Input::has('BTSubmit'))
    {
      $department_id = Input::get ( 'department_id' );
      if($department_id==''){
        $opt='!='; $department_id = '-1';
      }else{
        $opt='='; $department_id = $department_id;
      }
      $item_id = Input::get ( 'item_id' );
      if(isset($item_id)&&(!empty($item_id))){
        $opt2='='; $item_id = $item_id;
      }else{
        $opt2='!='; $item_id = '-1';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = reverseDate(request()->start_date); 
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = reverseDate(request()->end_date);
      }
    }
    $stock_ins = StockIn::with('item')->select(\DB::raw('sum(quantity) as total_qty, item_id, combinations','department_id','stock_date'))->where('department_id', $opt, $department_id)->where('item_id', $opt2, $item_id)->whereBetween('stock_date', [$start_date, $end_date])->groupBy('item_id')->groupBy('combinations')->get();
    $stock_in_items = StockIn::with('item')->select(\DB::raw('item_id'))->groupBy('item_id')->get();
    $stock_out_item = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id, combinations', 'status'))->where('status', '3')->where('product_type', '0')->where('item_id', $opt2, $item_id)->whereBetween('stock_out_date', [$start_date, $end_date])->groupBy('item_id')->groupBy('combinations')->get(); $stock_out_items=array();
    foreach($stock_out_item as $stock_out){
      $stock_out_items[$stock_out->item_id.'__'.$stock_out->combinations]=$stock_out->total_qty;
    }
    $items=array();
    foreach ($stock_in_items as $key => $value) {
      $items[$value->item->id]=$value->item->name;
    }
    $received_by = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $suppliers_list = Supplier::where('status', 1)->orderBy('contact_name','asc')->get();
    $suppliers=array();
    foreach ($suppliers_list as $key => $value) {
      $suppliers[$value->id]=$value->contact_name.' ( '.$value->company_name.' ) ';
    }
    $departments=Department::pluck('name', 'id')->toArray(); $departments[0]='For All Departments';
    $search_array['start_date']=date('d-m-Y', strtotime($start_date)); $search_array['end_date']=date('d-m-Y', strtotime($end_date));
    if($department_id=='-1'){ $search_array['department_id']='';
    }else{ $search_array['department_id']=$department_id; }
    if($item_id=='-1'){ $search_array['item_id']='';
    }else{ $search_array['item_id']=$item_id; }
    return view('admin.stock_in.stock_summary',compact('suppliers','search_array','stock_ins','received_by','items','departments','stock_out_items')); 
  }

  public function low_stock_reminder()
  {
    $reminder_items = Item::where('low_stock','>',0)->pluck('id','id');
    $stock_ins = StockIn::with('item')->select(\DB::raw('sum(quantity) as total_qty, item_id'))->whereIn('item_id',$reminder_items)->groupBy('item_id')->get();
    $stock_out_item = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('status', '3')->whereIn('item_id',$reminder_items)->where('product_type', '0')->groupBy('item_id')->get(); 
    $stock_out_items=array();
    foreach($stock_out_item as $stock_out){
      $stock_out_items[$stock_out->item_id]=$stock_out->total_qty;
    }

    $mrs_item = MrsItem::select(\DB::raw('sum(quantity) as total_qty, item_id'))->groupBy('item_id')->get();
    $mrs_in_items=$mrs_item->pluck('total_qty','item_id')->toArray();
    $stock_out_item = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('status', '3')->where('product_type', '1')->groupBy('item_id')->get();
    $mrs_stock_out=$stock_out_item->pluck('total_qty','item_id')->toArray();
    $mrs_balance=array();
    foreach($mrs_in_items as $key=>$value){
      if(array_key_exists($key,$mrs_stock_out)){
        $mrs_balance[$key]=$value-$mrs_stock_out[$key];
      }else{
        $mrs_balance[$key]=$value;
      }
    }
    return view('low_stock_reminder',compact('stock_ins','stock_out_items','mrs_balance'));
  }

}
