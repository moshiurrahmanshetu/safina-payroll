<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Amenity;
use Validator;

class ServiceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $services = Service::orderBy('id','desc')
      ->withCount(['bookings as total_bookings'])
      ->withCount(['bookings as confirmed_bookings' => function($query) {
        $query->where('status', 1);
      }])
      ->withCount(['bookings as pending_bookings' => function($query) {
        $query->where('status', 0);
      }])
      ->withCount(['bookings as cancelled_bookings' => function($query) {
        $query->where('status', 2);
      }])
      ->withCount(['bookings as completed_bookings' => function($query) {
        $query->where('status', 3);
      }])
      ->withSum(['bookings as total_revenue' => function($query) {
        $query->where('status', 1);
      }], 'final_price')
      ->get();
    $users = User::pluck('name','id');
    return view('admin.services.index',compact('users','services'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $service_categories = ServiceCategory::pluck('name','id')->toArray();
    return view('admin.services.create',compact('service_categories'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  /**
   * Get service data for AJAX (pricing_type only)
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function getService($id)
  {
    $service = Service::find($id);
    if($service){
      return response()->json([
        'pricing_type'  => $service->pricing_type,
        'success'       => true
      ]);
    }
    return response()->json(['success' => false], 404);
  }

  public function store(Request $request)
  {
    $data=request()->all();
    $validator=Validator::make($data,
      array(
        'name'                =>'required',
        'service_category_id' =>'required',
        'pricing_type'        =>'required',
        'status'              =>'required',
        'guest_capacity'      =>'nullable|integer|min:0',
        'service_details'      =>'nullable|string',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    unset($data['_token']);
    unset($data['capacity']);
    $services=Service::create($data);
    if($services){
      $message="You have successfully created";
      return redirect()->route('services.index')->with('flash_success',$message);
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
    $services = Service::findorfail($id);
    return view('admin.services.show',compact('services'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $services = Service::findorfail($id);
    $service_categories = ServiceCategory::pluck('name','id')->toArray();
    return view('admin.services.edit',compact('services','service_categories'));
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
        'name'                =>'required',
        'service_category_id' =>'required',
        'pricing_type'        =>'required',
        'status'              =>'required',
        'guest_capacity'      =>'nullable|integer|min:0',
        'service_details'      =>'nullable|string',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;
    unset($data['_token']);
    unset($data['capacity']);

    $services=Service::where('id', $id)->update($data);

    if($services){
      $service = Service::find($id);
      $message="You have successfully Updated";
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
    $deleted=Service::where('id',$id)->delete();
    $message="You have successfully Deleted";
    if($deleted){
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

  /**
   * Get services by category ID (for AJAX)
   * Filters by counter's allowed services and availability for selected date
   *
   * @param  int  $category_id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function getServicesByCategory($category_id, Request $request)
  {
    try {
      $user = Auth::user();
      $userCounter = $user->counters()->first();
      $bookingDate = $request->query('booking_date');

      // Base query - services in category
      $query = Service::where('service_category_id', $category_id)
        ->where('status', 1);

      // If user has a counter, filter by allowed services
      if ($userCounter) {
        $allowedServiceIds = $userCounter->services()->pluck('services.id')->toArray();
        if (!empty($allowedServiceIds)) {
          $query->whereIn('id', $allowedServiceIds);
        } else {
          // No services assigned to this counter - return empty
          return response()->json([
            'status' => true,
            'data' => []
          ]);
        }
      }

      // Filter by availability if booking date is provided
      if ($bookingDate) {
        try {
          $date = Carbon::createFromFormat('d-m-Y', $bookingDate)->format('Y-m-d');
          $query->availableForDate($date);
        } catch (\Exception $e) {
          // Invalid date format, skip availability filter
          \Log::warning('Invalid booking date format: ' . $bookingDate);
        }
      }

      $services = $query->get(['id', 'name']);

      return response()->json([
        'status' => true,
        'data' => $services
      ]);
    } catch (\Exception $e) {
      \Log::error('Error loading services by category: ' . $e->getMessage());
      return response()->json([
        'status' => false,
        'message' => 'Error loading services'
      ], 500);
    }
  }
}
