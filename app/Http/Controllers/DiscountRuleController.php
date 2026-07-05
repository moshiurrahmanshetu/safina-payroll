<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\DiscountRule;
use App\Models\Service;
use App\Models\ServiceCategory;
use Validator;

class DiscountRuleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $discount_rules = DiscountRule::with('service')->orderBy('id','desc')->get();
    return view('admin.discount_rules.index',compact('discount_rules'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $service_categories = ServiceCategory::where('status', 1)->pluck('name', 'id')->toArray();
    $services = Service::where('status', 1)->pluck('name', 'id')->toArray();
    return view('admin.discount_rules.create', compact('service_categories', 'services'));
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
        'name'          =>'required',
        'discount_type' =>'required',
        'amount'        =>'required|numeric',
        'status'        =>'required',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    unset($data['_token']);
    $discount_rule=DiscountRule::create($data);
    if($discount_rule){
      $message="You have successfully created promo discount";
      return redirect()->route('discount_rules.index')->with('flash_success',$message);
    }else{
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $discount_rule = DiscountRule::findorfail($id);
    $service_categories = ServiceCategory::where('status', 1)->pluck('name', 'id')->toArray();
    $services = Service::where('status', 1)->pluck('name', 'id')->toArray();
    return view('admin.discount_rules.edit', compact('discount_rule', 'service_categories', 'services'));
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
        'name'          =>'required',
        'discount_type' =>'required',
        'amount'        =>'required|numeric',
        'status'        =>'required',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;
    unset($data['_token']);

    $discount_rule=DiscountRule::where('id', $id)->update($data);

    if($discount_rule){
      $message="You have successfully updated promo discount";
      return redirect()->back()->with('flash_success',$message);
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
    $deleted=DiscountRule::where('id',$id)->delete();
    $message="You have successfully deleted promo discount";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }
}
