<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Validator;
use Input;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SiteSetting;
class PurchaseController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $search_array = array(); $start_date=''; $end_date=''; $supplier_id='';
    if (Input::has('BTSubmit'))
    {
      if(request()->has('supplier_id')&&(!empty(request('supplier_id')))){ 
        $supplier_id = request()->supplier_id; $opt='=';
      }else{
        $opt='!=';
      }
      if(request()->has('start_date')&&(!empty(request('start_date')))){ 
        $start_date = reverseDate(request()->start_date); 
      }else{
        $start_date ='2015-01-01';
      }
      if(request()->has('end_date')&&(!empty(request('end_date')))){
        $end_date = reverseDate(request()->end_date);
      }else{
        $end_date= date('Y-m-d'); 
      }
      $purchases = Purchase::with(['purchase_transactions'=>function($q){
        $q->select(\DB::raw('sum(amount) as amount, purchase_id'))->groupBy('purchase_id');
      }])->with("supplier")->with("purchase_items")->where('supplier_id',$opt,$supplier_id)->whereBetween('purchase_date', [$start_date, $end_date])->orderBy('id','desc')->get();
    }else{
      $purchases = Purchase::with(['purchase_transactions'=>function($q){
        $q->select(\DB::raw('sum(amount) as amount, purchase_id'))->groupBy('purchase_id');
      }])->with("supplier")->with("purchase_items")->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id');
    $supllier_lists=Supplier::orderBy('company_name','asc')->pluck('company_name','id')->toArray();
    $search_array['start_date']=$start_date; $search_array['end_date']=$end_date; $search_array['supplier_id']=$supplier_id;
    //dd($purchases);
    return view('admin.purchase.index',compact('users','purchases', 'search_array','supllier_lists')); 
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $purchase_persons = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $item_types =Category::orderBy('name','asc')->pluck('name','id')->toArray();
    $item_names =Item::orderBy('name','asc')->pluck('name','id')->toArray();
    return view('admin.purchase.create',compact('purchase_persons','item_names','item_types')); 
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
        'contact_name'    =>'required',
        'supplier_type'   =>'required',
        'mobile'          =>'required',
        'purchase_date'   =>'required',
        'purchase_person' =>'required',
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    if($data['purchase_date']){
      $data['purchase_date']=date('Y-m-d',strtotime($data['purchase_date']));
    }else{
      $data['purchase_date']=date('Y-m-d');
    }
    $userId = Auth::id();
    $data['created_by'] = $data['updated_by'] = $userId;
    $data2 = array(); $activity = array(); $activity = $data['activity']; unset($data['activity']); $activity1 = array();
    if(request()->has('activity1')){
      $activity1 = $data['activity1']; unset($data['activity1']);
    }
    if($data['supplier_id']==''){
      $new_supplier=array(); 
      $new_supplier['supplier_type']=$data['supplier_type'];
      $new_supplier['contact_name']=$data['contact_name'];
      $new_supplier['company_name']=$data['company_name'];
      $new_supplier['mobile']=$data['mobile'];
      $new_supplier['address']=$data['address'];
      $new_supplier['email']=$data['email'];
      $new_supplier['web_site']=$data['web_site'];
      $new_supplier['status']=1;
      $new_supplier['created_by'] = $new_supplier['updated_by'] = $userId;
      $supplier=Supplier::create($new_supplier);
      $data['supplier_id']=$supplier->id;
    }
    $purchases=Purchase::create($data);
    if($purchases){
      $data2['purchase_id']=$purchases->id; 
      foreach ($activity as $key => $value) {
        $data2['item_id']=$value['item_name'];
        $data2['name']=$value['name'];
        if(array_key_exists($key, $activity1)){
          $data2['combinations'] =json_encode($activity1[$key], JSON_HEX_APOS);
        }else{
          $data2['combinations'] =json_encode('');
        }
        $data2['unit_price']=$value['unit_price'];
        $data2['description']=$value['description'];
        $data2['category_id']=$value['type'];
        $data2['measuring_unit']=$value['measuring_unit'];
        $data2['quantity']=$value['no_of_unit'];
        $data2['per_total']=$value['per_total'];
        $purchase_items=PurchaseItem::create($data2); 
      }
      if($purchase_items){
        $message = "You Have Successfully Created";
        return redirect()->route('purchase.create')->with('flash_success', $message);
      }else{
       $deleted = Purchase::where('id', $purchases->id)->delete();
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
    $purchase_persons = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $item_names =Item::orderBy('name','asc')->pluck('name','id')->toArray();
    $cat_names =Category::orderBy('name','asc')->pluck('name','id')->toArray();
    $purchases = Purchase::with('purchase_items','purchase_items.item')->where('id', $id)->first();
    $supllier_lists=Supplier::where('supplier_type', $purchases->supplier_type)->where( 'status',1)->pluck('company_name','id')->toArray();     
    return view('admin.purchase.edit',compact('purchase_persons','item_names','purchases','supllier_lists', 'cat_names')); 
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
        'supplier_id'     =>'required',
        'contact_name'    =>'required',
        'supplier_type'   =>'required',
        'mobile'          =>'required',
        'purchase_date'   =>'required',
        'purchase_person' =>'required',
      ));
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    //dd($data);
    if($data['purchase_date']){
      $data['purchase_date']=date('Y-m-d',strtotime($data['purchase_date']));
    }else{
      $data['purchase_date']=date('Y-m-d');
    }
    $userId = Auth::id(); $data['updated_by'] = $userId;
    $data2 = array(); $activity = array(); $activity = $data['activity']; unset($data['activity']); $activity1 = array(); 
    if(request()->has('activity1')){
      $activity1 = $data['activity1']; unset($data['activity1']);
    }
    $update = Purchase::where('id', $id)->where('status', '<>', 3)->update($data);
    if($update){
      $data2['purchase_id']=$id; 
      foreach ($activity as $key => $value) {
        $data2['item_id']=$value['item_name'];
        $data2['name']=$value['name'];
        if(array_key_exists($key, $activity1)){
          $data2['combinations'] =json_encode($activity1[$key], JSON_HEX_APOS);
        }else{
          $data2['combinations'] =json_encode('');
        }
        $data2['unit_price']=$value['unit_price'];
        $data2['description']=$value['description'];
        $data2['category_id']=$value['type'];
        $data2['measuring_unit']=$value['measuring_unit'];
        $data2['quantity']=$value['no_of_unit'];
        $data2['per_total']=$value['per_total'];
        if(array_key_exists('id',$value)){
          $purchase_items=PurchaseItem::where('id',$value['id'])->update($data2);
        }else{
          $purchase_items=PurchaseItem::create($data2);
        }
      }
      if($purchase_items){
        $message = "You Have Successfully Updated";
        return redirect()->back()->with('flash_success', $message);
      }else{
        //$deleted = Purchase::where('id', $id)->delete();
        $message = "Something is Wrong, Please Try again";
        return redirect()->back()->withInput()->with('flash_warning', $message);
      }
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning', $message);
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
    $deleted=Purchase::where('id',$id)->delete();
    if($deleted){
      $message="You have successfully Deleted";
      return redirect()->back()->with('flash_success',$message);
    }else{
      $message = "Something is Wrong, Please Try again";
      return redirect()->back()->withInput()->with('flash_warning',$message);
    }
  }

  public function purchase_print(Request $request,$id='')
  {
    $search_array = array();$start_date=''; $end_date='';
    $start_date = Input::get ('start_date'); 
    $end_date = Input::get ('end_date');
    $start_date=date('Y-m-d', strtotime($start_date));
    $end_date=date('Y-m-d', strtotime($end_date));
    $purchases = Purchase::with("supplier")->whereBetween('purchase_date', [$start_date, $end_date])->orderBy('purchase_date','desc')->get();
    $users = User::pluck('name','id');    
    $search_array['start_date']=$start_date; $search_array['end_date']=$end_date;
    $site_info = SiteSetting::orderBy('id', 'desc')->first();
    return View('purchase_details_report',compact('users','purchases','search_array','site_info')); 
  } 
  
}