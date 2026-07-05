<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Input;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\User;
use Validator;
use App\Models\SiteSetting;

class SupplierController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {      
    $suppliers = Supplier::with('purchases')->orderBy('id','desc')->get();
    $users = User::pluck('name','id');
    return view('admin.supplier.index',compact('users','suppliers')); 
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $users = User::pluck('name','id'); 
    return view('admin.supplier.create',compact('users'));
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
        'contact_name'  =>'required',
        'supplier_type' =>'required',
        'mobile'        =>'required',
        'status'        =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    unset($data['_token']);
    $suppliers=Supplier::create($data);
    if($suppliers){
      $message="You have successfully created";
      return redirect()->route('supplier.index')->with('flash_success',$message);
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
    $search_array = array(); $start_date='2015-01-01'; $end_date=date('Y-m-d'); $total_paid=0; $total_purchases=0; $partial_purchase=0; $partial_payment=0;
    if (Input::has('BTSubmit'))
    {
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
    }
    $purchasing_details=Purchase::with("purchase_items")->where('supplier_id',$id)->orderBy('purchase_date','desc')->get();
    foreach($purchasing_details as $key => $value) {
      $total_purchases=$total_purchases+$value->grand_total;
      if(($value->purchase_date>=$start_date)&&($value->purchase_date<=$end_date)){
        $partial_purchase=$partial_purchase+$value->grand_total;
      }
    }
    $suppliers = Supplier::findorfail($id); 
    $users = User::pluck('name','id');
    $search_array['start_date']=$start_date; $search_array['end_date']=$end_date; 
    $search_array['total_paid']=$total_paid;
    $search_array['total_purchases']=$total_purchases; 
    $search_array['partial_payment']=$partial_payment;
    $search_array['partial_purchase']=$partial_purchase;
    return view('admin.supplier.show',compact('users','suppliers','purchasing_details','search_array'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $suppliers = Supplier::findorfail($id);
    $users = User::where('status',1)->orderBy('name','asc')->pluck('name','id')->toArray(); 

    return View('admin.supplier.edit',compact('suppliers','users'));
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
       'contact_name'  =>'required',
       'supplier_type' =>'required',
       'mobile'        =>'required',
       'status'        =>'required', 
     )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;
    unset($data['_token']);

    $suppliers=Supplier::where('id', $id)->update($data);

    if($suppliers){
     $message="You have successfully Updated";
     return redirect()->back()
     ->with('flash_success',$message);
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
    //dd($id);
    $deleted=Supplier::where('id',$id)->delete(); 
    $message="You have successfully Deleted";
    if($deleted){
      return redirect()->back()
      ->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    } 
  }


  public function print_supplier_details(Request $request,$id='')
  {
    $search_array = array();$start_date=''; $end_date=''; 
    $total_paid=0;$total_purchases=0;$partial_purchase=0;
    $partial_payment=0;
    $id = Input::get ('supplier_id'); 
    $start_date = Input::get ('start_date'); 
    $end_date = Input::get ('end_date');
    $start_date=date('Y-m-d', strtotime($start_date));
    $end_date=date('Y-m-d', strtotime($end_date)); 
    $purchasing_details = Purchase::where('supplier_id',$id)->orderBy('purchase_date','desc')->get(); 

    foreach ($purchasing_details as $key => $value) {
      $total_purchases=$total_purchases+$value->grand_total;
      if(($value->purchase_date>=$start_date)&&($value->purchase_date<=$end_date)){
        $partial_purchase=$partial_purchase+$value->grand_total;
      }
    }

    $suppliers = Supplier::findorfail($id); 
    $users = User::pluck('name','id');
    $site_info = SiteSetting::orderBy('id', 'desc')->first();
    $search_array['start_date']=$start_date; $search_array['end_date']=$end_date; 
    $search_array['total_paid']=$total_paid;
    $search_array['total_purchases']=$total_purchases; 
    $search_array['partial_payment']=$partial_payment;
    $search_array['partial_purchase']=$partial_purchase; 
    return View('supplier_details_report',compact('users','suppliers','purchasing_details','search_array','site_info'));
  }
}