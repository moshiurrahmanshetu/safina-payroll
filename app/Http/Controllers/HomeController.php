<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use App\Models\Designation;
use App\Models\Department;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Requisition;
use App\Models\MrsItem;
use App\Models\StockIn;
use App\Models\RequisitionItem;
use DB;
use Auth;
class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    $user=Auth::user(); $userId=$user->id;
    $users=User::select(\DB::raw('count(*) as total, status'))->groupBy('status')->get();
    $designations = Designation::count();
    $departments = Department::count();
    $categories = Category::count();
    $products = Item::count();
    $suppliers = Supplier::count();
    $purchases = Purchase::count();
    $myrequisitions = Requisition::where('user_id',$userId)->count();
    $my_indents = Requisition::with(['requisition_items'=>function($q){
        $q->where('returnable',1)->where('status',3)->where('given_quantity','<>',0);
      }])->where('user_id',$userId)->orderBy('id','desc')->get();
    $my_indent=0;
    foreach($my_indents as $items){
      $my_indent+=count($items->requisition_items);
    }
    $indents = Requisition::with(['requisition_items'=>function($q){
        $q->where('returnable',1)->where('status',3)->where('given_quantity','<>',0);
      }])->orderBy('id','desc')->get();
    $all_indent=0;
    foreach($indents as $items){
      $all_indent+=count($items->requisition_items);
    }
    $requisitions = Requisition::count();
    $my_mrs_items = MrsItem::where('user_id', $userId)->count();
    $mrs_items = MrsItem::count();
    
    return view('home',compact('users','designations','departments','categories','products','suppliers','purchases','myrequisitions','requisitions','my_indent','all_indent','my_mrs_items','mrs_items'));
  }
}
