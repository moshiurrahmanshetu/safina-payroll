<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Input;
use App\Models\Requisition;
use App\Models\RequisitionItem;

use App\Models\Item;
use App\Models\User;

class IndentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function indent_list()
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
      $requisitions = Requisition::with('purpose')->with(['requisition_items'=>function($q){
        $q->with(['mrs_items'=>function($q2){
        $q2->select(\DB::raw('sum(quantity) as total_qty, requisition_item_id'))->groupBy('requisition_item_id');}])->where('returnable',1)->where('status',3)->where('given_quantity','<>',0);
      }])->where('user_id',$userId)->whereBetween('created_at', [$start_date, $end_date])->where('id',$opt2,$req_no)->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with('purpose')->with(['requisition_items'=>function($q){
        $q->with(['mrs_items'=>function($q2){
        $q2->select(\DB::raw('sum(quantity) as total_qty, requisition_item_id'))->groupBy('requisition_item_id');}])->where('returnable',1)->where('status',3)->where('given_quantity','<>',0);
      }])->where('user_id',$userId)->orderBy('id','desc')->get();
    }
    //dd($requisitions);
    $users = User::pluck('name','id');  $search_array['req_no']=$req_no;
    return view('admin.indent.indent_list',compact('users','requisitions', 'search_array')); 
  }

  public function admin_indent_list()
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
      $requisitions = Requisition::with('purpose')->with(['requisition_items'=>function($q){
        $q->with(['mrs_items'=>function($q2){
        $q2->select(\DB::raw('sum(quantity) as total_qty, requisition_item_id'))->groupBy('requisition_item_id');}])->where('returnable',1)->where('status',3)->where('given_quantity','<>',0);
      }])->where('user_id',$opt,$user_id)->where('id',$opt2,$req_no)->whereBetween('created_at', [$start_date, $end_date])->orderBy('id','desc')->get();
    }else{
      $requisitions = Requisition::with('purpose')->with(['requisition_items'=>function($q){
        $q->with(['mrs_items'=>function($q2){
        $q2->select(\DB::raw('sum(quantity) as total_qty, requisition_item_id'))->groupBy('requisition_item_id');}])->where('returnable',1)->where('status',3)->where('given_quantity','<>',0);
      }])->orderBy('id','desc')->get();
    }
    $users = User::pluck('name','id')->toArray();
    $search_array['user_id']=$user_id; $search_array['req_no']=$req_no;
    return view('admin.indent.admin_indent_list',compact('users','requisitions', 'search_array')); 
  }


}