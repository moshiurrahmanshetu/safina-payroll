<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\PurchaseTransaction;
use App\Models\User;
use Validator;
use Input;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SiteSetting;
use File;

class PurchaseTransactionController extends Controller
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
    $supplier_id = Input::get ( 'supplier_id' );
    if(isset($supplier_id)&&(!empty($supplier_id))){
      $opt='='; $supplier_id = $supplier_id;
    }else{
      $opt='!='; $supplier_id = '-1';
    }
    $purchase_transactions = PurchaseTransaction::with("supplier")->where('supplier_id', $opt, $supplier_id)->whereBetween('payment_date', [$start_date, $end_date])->orderBy('id','desc')->get();
    $purchases = Purchase::select(\DB::raw('sum(grand_total) as total'))->whereBetween('purchase_date', [$start_date, $end_date])->where('supplier_id', $opt, $supplier_id)->first();
    }else{
      $purchase_transactions = PurchaseTransaction::with("supplier")->orderBy('id','desc')->get();
      $purchases = Purchase::select(\DB::raw('sum(grand_total) as total'))->first();
    }
    $suppliers = Supplier::where('status', 1)->orderBy('contact_name','asc')->get();
    $supplier_list=array();
    foreach ($suppliers as $key => $value) {
      $supplier_list[$value->id]=$value->contact_name.' ( '.$value->company_name.' ) ';
    }

  $users = User::pluck('name','id');    
  $search_array['start_date']=$start_date; $search_array['end_date']=$end_date; $search_array['supplier_id']=$supplier_id;

  return view('admin.purchase_transaction.index',compact('users','supplier_list', 'search_array','purchase_transactions','purchases')); 
}

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    if(Input::has('id'))
    {
      $purchase_id = Input::get('id');
    }else{
      exit;
    }
    $purchase = Purchase::with('supplier')->findorfail($purchase_id);
    $transactions=PurchaseTransaction::select(\DB::raw('sum(amount) as total, purchase_id'))->where('purchase_id',$purchase_id)->groupBy('purchase_id')->get();
    $transaction_last=PurchaseTransaction::where('purchase_id',$purchase_id)->orderBy('payment_date','desc')->first();
    $users = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    return view('admin.purchase_transaction.create',compact('purchase','users','transactions','transaction_last')); 
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
      'supplier_id'     =>'required',
      'purchase_id'     =>'required',
      'given_by'        =>'required',
      'amount'          =>'required',
      'payment_type'    =>'required',           
      'payment_date'    =>'required',
      'attachment_copy' =>'file|mimes:jpeg,png,jpg|max:250'
    )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
    ->withInput();
    }
    $imageName=null;
    $file=request()->file('attachment_copy');
    if($file != null){
      $imageName = time().'_'.get_file_name($file->getClientOriginalName());
      $data['attachment_copy']=$imageName;
    }
    $data['payment_date'] =date('Y-m-d',strtotime($data['payment_date']));
    $userId = Auth::id();
    $data['created_by'] = $data['updated_by'] = $userId;
    $purchase_transactions=PurchaseTransaction::create($data);
    if($purchase_transactions){
      if($imageName!=null){
        $file->storeAs('admin/transactions',$imageName);
      }
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
    $purchase_transactions = PurchaseTransaction::with('purchase')->with('supplier')->findorfail($id);
    $transaction_persons = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $transactions=PurchaseTransaction::select(\DB::raw('sum(amount) as total, purchase_id'))->where('purchase_id',$purchase_transactions->purchase_id)->groupBy('purchase_id')->get();
    $transaction_last=PurchaseTransaction::where('purchase_id',$purchase_transactions->purchase_id)->orderBy('payment_date','desc')->first();
    return view('admin.purchase_transaction.show',compact('transactions','transaction_persons','purchase_transactions','transaction_last')); 
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $purchase_transactions = PurchaseTransaction::with('purchase')->with('supplier')->findorfail($id);
    $transaction_persons = User::where('status', 1)->orderBy('name','asc')->pluck('name','id')->toArray();
    $transactions=PurchaseTransaction::select(\DB::raw('sum(amount) as total, purchase_id'))->where('purchase_id',$purchase_transactions->purchase_id)->groupBy('purchase_id')->get();
    $transaction_last=PurchaseTransaction::where('purchase_id',$purchase_transactions->purchase_id)->orderBy('payment_date','desc')->first();
    return view('admin.purchase_transaction.edit',compact('transactions','transaction_persons','purchase_transactions','transaction_last')); 
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
    //dd($data);
    $validator=Validator::make($data,
    array(
      'given_by'        =>'required',
      'amount'          =>'required',
      'payment_type'    =>'required',
      'payment_date'    =>'required',
      'attachment_copy' =>'file|mimes:jpeg,png,jpg|max:250'
    )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $fileName=null; $file_name=request()->file('attachment_copy');
    if($file_name != null){
      $fileName = rand(99,9999).'_'.$file_name->getClientOriginalName();
      $data['attachment_copy']=$fileName;
    }
    $old_file_name=$data['old_file']; unset($data['old_file']);
    $data['payment_date'] =date('Y-m-d',strtotime($data['payment_date']));
    $userId = Auth::id(); $data['updated_by'] = $userId;
    $purchase_transactions=PurchaseTransaction::where('id', $id)->update($data);
    if($purchase_transactions){
      if($fileName!=null){
        if($old_file_name !=null){
          if(File::exists(storage_path('app/admin/transactions/'.$old_file_name))){
            File::delete(storage_path('app/admin/transactions/'.$old_file_name));
          }
        }
        $file_name->storeAs('admin/transactions',$fileName);
      }
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
    $deleted=PurchaseTransaction::where('id',$id)->delete(); 
    $message="You have successfully Deleted";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

  
}