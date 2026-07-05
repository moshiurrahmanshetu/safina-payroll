<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\RequisitionItem;
use Auth;
use App\Models\Requisition;
use App\Models\MrsItem;
use App\Models\Purpose;

class AjaxController extends Controller
{
  public function __construct(){
    parent::__construct();       
  }

  public function received_products() {
    $req_id = request()->req_id;
    $user=Auth::user(); 
    $update = Requisition::where('id', $req_id)->where('user_id', $user->id)->where('status', 3)->update(['received_status'=>'received']);
    return response()->json($update);
  }

  public function get_supplier_lists() {
    $supplier_type = request()->supplier_type;
    $suppliers = Supplier::where('supplier_type', $supplier_type)->where('status', 1)->orderBy('contact_name','asc')->get();
    $supllier_lists=array();
    foreach ($suppliers as $key => $value) {
      $supllier_lists[$value->id]=$value->contact_name.' ( '.$value->company_name.' ) ';
    }
    return response()->json($supllier_lists);
  }

  public function show_type_wise_item_list() {
    $type_id = request()->type_id;
    if($type_id==''){
      $item_list = Item::where('status', 1)->orderBy('name','asc')->pluck('name', 'id');
    }else{
      $item_list = Item::where('category_id', $type_id)->where('status', 1)->orderBy('name','asc')->pluck('name', 'id');
    }
    return response()->json($item_list);
  }

  public function show_purpose_names() {
    $type_id = request()->type_id;
    $item_list = Purpose::where('purpose_type', $type_id)->orderBy('name','asc')->pluck('name', 'id');
    return response()->json($item_list);
  }

  public function get_supllier_info() {
    $clientId = request()->clientId;
    $client = Supplier::where('id',$clientId)->where('status', 1)->first();
    return response()->json($client);
  }

  public function get_item_info() {
    $clientId = request()->clientId;
    $item = Item::with('category')->select('name','attributes','combination','category_id','measuring_unit','brand_name','model','additional')->where('id',$clientId)->first();
    return response()->json($item);
  }

  public function check_availability() {
    $item_id=request()->item_id; $product_type=request()->product_type; $combinations=request()->combination; 
 $user_id=request()->user_id; $user=User::where('id', $user_id)->first(); 
 $department_ids=array(); $department_ids[]=0; $department_ids[]=$user->department_id; $available_items=0;
   if($product_type==0){
      if($combinations){
        $stock_ins = StockIn::select(\DB::raw('sum(quantity) as total_qty, item_id', 'combinations'))->where('item_id', $item_id)->where('combinations', $combinations)->whereIn('department_id', $department_ids)->groupBy('item_id')->first();
        $stock_outs = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('item_id', $item_id)->where('status', '3')->where('product_type', $product_type)->where('combinations', $combinations)->groupBy('item_id')->first();  
   }else{
        $stock_ins = StockIn::select(\DB::raw('sum(quantity) as total_qty, item_id', 'department_id'))->where('item_id', $item_id)->whereIn('department_id', $department_ids)->groupBy('item_id')->first();
        $stock_outs = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('item_id', $item_id)->where('status', '3')->where('product_type', $product_type)->groupBy('item_id')->first();
      }
      if($stock_ins && $stock_outs){
        $available_items=$stock_ins->total_qty-$stock_outs->total_qty; 
      }else if($stock_ins){
        $available_items=$stock_ins->total_qty; 
      }
    }else if($product_type==1){
      if($combinations){
        $stock_ins = MrsItem::select(\DB::raw('sum(quantity) as total_qty, item_id', 'combinations'))->where('item_id', $item_id)->where('combinations', $combinations)->groupBy('item_id')->first();
        $stock_outs = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('item_id', $item_id)->where('status', '3')->where('product_type', $product_type)->where('combinations', $combinations)->groupBy('item_id')->first();
      }else{
        $stock_ins = MrsItem::select(\DB::raw('sum(quantity) as total_qty, item_id'))->where('item_id', $item_id)->groupBy('item_id')->first();
        $stock_outs = RequisitionItem::select(\DB::raw('sum(given_quantity) as total_qty, item_id', 'status'))->where('item_id', $item_id)->where('status', '3')->where('product_type', $product_type)->groupBy('item_id')->first();
      }
      if($stock_ins && $stock_outs){
        $available_items=$stock_ins->total_qty-$stock_outs->total_qty; 
      }else if($stock_ins){
        $available_items=$stock_ins->total_qty; 
      }
    }
    return response()->json($available_items);
  }

  public function show_user_item_mrs() {
    $user_id=request()->user_id; $item_list=array();
    $requisitions = Requisition::with(['requisition_items'=>function($q){
        $q->where('returnable',1)->where('status',3);
      }])->where('user_id',$user_id)->orderBy('id','desc')->get();
    $mrs_items = MrsItem::select(\DB::raw('sum(quantity) as quantity, requisition_item_id', 'user_id'))->where('user_id',$user_id)->groupBy('requisition_item_id')->get();
    $mrs_lists=$mrs_items->pluck('quantity','requisition_item_id')->toArray();
    if($requisitions){
      foreach($requisitions as $values){
        foreach($values->requisition_items as $item){
          if(array_key_exists($item->id,$mrs_lists)){
            if(($item->given_quantity)>($mrs_lists[$item->id])){
              $item_list[$item->id]=$item->name;
            }
          }else{
            $item_list[$item->id]=$item->name;
          }
        }
      }
    }
    return response()->json($item_list);
  }

  public function get_mrs_item_info() {
    $req_item_id = request()->req_item_id;
    $item = RequisitionItem::with('requisition.purpose','mrs_items')->where('id',$req_item_id)->first();
    return response()->json($item);
  }


  public function lowstock_summary() {
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
    $i=1; $stock_out=0; $item=array();
    foreach ($stock_ins as $data){
    if(array_key_exists($data->item_id,$stock_out_items)){
      $balance=$data->total_qty-$stock_out_items[$data->item_id];
      $stock_out=$stock_out_items[$data->item_id];
    }else{
      $balance=$data->total_qty+0; $stock_out=0;
    }
    if(array_key_exists($data->item_id,$mrs_balance)){
      $balance+=$mrs_balance[$data->item_id];
    }
    if($balance<=$data->item->low_stock){
      $item[$i]['name']=$data->item->name; 
      $item[$i]['balance']=$balance.' '.$data->item->measuring_unit; 
      $item[$i]['low_stock']=$data->item->low_stock.' '.$data->item->measuring_unit;
    $i=$i+1; } if($i>10){ break; } 
    }
    return response()->json($item);

  }


}
