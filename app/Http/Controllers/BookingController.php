<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\BookingMetaValue;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceMetaField;
use App\Models\CategoryMetaField;
use App\Models\PricingRule;
use App\Models\DiscountRule;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use Validator;

class BookingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = Auth::user();

    // Check if user has permission to view all bookings (purely permission-based)
    // Any role with 'view_all_bookings' permission gets full access
    $canViewAll = $this->hasPermission($user->role_id, 'view_all_bookings');

    $query = Booking::with(['service', 'creator', 'counter']);

    // Apply access restrictions based on permission
    if(!$canViewAll){
      // Counter user: show only their own bookings OR bookings from their assigned counter
      $userCounterIds = $user->counters()->pluck('counter_id')->toArray();

      $query->where(function($q) use ($user, $userCounterIds) {
        $q->where('created_by', $user->id);
        if(!empty($userCounterIds)){
          $q->orWhereIn('counter_id', $userCounterIds);
        }
      });
    }

    // Apply date range filter
    if($request->has('from_date') && $request->from_date){
      try {
        $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
        $query->where('check_in_date', '>=', $fromDate);
      } catch(\Exception $e) {
        // Invalid date format, ignore filter
      }
    }

    if($request->has('to_date') && $request->to_date){
      try {
        $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
        $query->where('check_in_date', '<=', $toDate);
      } catch(\Exception $e) {
        // Invalid date format, ignore filter
      }
    }

    // Apply user filter (only for admin with permission)
    if($canViewAll && $request->has('user_id') && $request->user_id){
      $query->where('created_by', $request->user_id);
    }

    // Apply counter filter (only for admin with permission)
    if($canViewAll && $request->has('counter_id') && $request->counter_id){
      $query->where('counter_id', $request->counter_id);
    }

    $bookings = $query->orderBy('id','desc')->get();

    // Calculate summary
    $totalBookings = $bookings->count();
    $totalRevenue = $bookings->sum('final_price');

    // Get filter dropdown data (only show all options to users with permission)
    if($canViewAll){
      $users = User::pluck('name','id');
      $counters = \App\Models\Counter::where('status', 1)->pluck('name', 'id');
    } else {
      // Limited options for counter users
      $users = User::where('id', $user->id)->pluck('name','id');
      $counters = $user->counters()->where('status', 1)->pluck('name', 'counter_id');
    }

    // Check if report generation requested
    if($request->has('generate_report') && $request->generate_report){
      return $this->generateReport($bookings, $totalBookings, $totalRevenue, $request);
    }

    return view('admin.bookings.index', compact(
      'users',
      'bookings',
      'counters',
      'totalBookings',
      'totalRevenue',
      'canViewAll'
    ));
  }

  /**
   * Check if role has specific permission
   *
   * @param int $role_id
   * @param string $permissionName
   * @return bool
   */
  private function hasPermission($role_id, $permissionName)
  {
    $permission = Permission::where('name', $permissionName)->first();
    if(!$permission){
      return false;
    }

    return RolePermission::where('role_id', $role_id)
      ->where('permission_id', $permission->id)
      ->exists();
  }

  /**
   * Generate report from filtered bookings
   *
   * @param \Illuminate\Support\Collection $bookings
   * @param int $totalBookings
   * @param float $totalRevenue
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  private function generateReport($bookings, $totalBookings, $totalRevenue, $request)
  {
    $reportData = [
      'bookings' => $bookings,
      'total_bookings' => $totalBookings,
      'total_revenue' => $totalRevenue,
      'filters' => [
        'from_date' => $request->from_date,
        'to_date' => $request->to_date,
        'user_id' => $request->user_id,
        'counter_id' => $request->counter_id
      ],
      'generated_by' => Auth::user()->name,
      'generated_at' => Carbon::now()->format('d-m-Y H:i:s')
    ];

    // Return print-friendly view
    return view('admin.bookings.report', $reportData);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $user = Auth::user();
    $userCounter = $user->counters()->first();
    $counterName = $userCounter->name ?? 'N/A';

    // Get allowed service IDs for user's counter
    $allowedServiceIds = [];
    if ($userCounter) {
      $allowedServiceIds = $userCounter->services()->pluck('services.id')->toArray();
    }

    // Filter categories to only show those containing allowed services
    if (!empty($allowedServiceIds)) {
      $allowedCategoryIds = Service::whereIn('id', $allowedServiceIds)
        ->where('status', 1)
        ->distinct()
        ->pluck('service_category_id')
        ->toArray();
      $service_categories = ServiceCategory::whereIn('id', $allowedCategoryIds)
        ->where('status', 1)
        ->pluck('name','id')
        ->toArray();
    } else {
      // No services assigned - show empty categories
      $service_categories = [];
    }

    $services = Service::where('status',1)->pluck('name','id')->toArray();
    return view('admin.bookings.create',compact('services', 'service_categories', 'counterName'));
  }

  /**
   * Get dynamic fields for a service (AJAX)
   *
   * @param  int  $service_id
   * @return \Illuminate\Http\Response
   */
  public function get_fields($service_id)
  {
    $service = Service::find($service_id);
    
    if (!$service || !$service->service_category_id) {
      return response()->json([
        'meta_fields' => [],
        'pricing_type' => $service ? $service->pricing_type : 0
      ]);
    }
    
    $meta_fields = CategoryMetaField::where('service_category_id', $service->service_category_id)
      ->orderBy('sort_order', 'asc')
      ->orderBy('id', 'asc')
      ->get();

    // Format meta fields with conditional data and sort order
    $formatted_fields = $meta_fields->map(function($field){
      return [
        'id' => $field->id,
        'field_name' => $field->field_name,
        'field_type' => $field->field_type,
        'required' => $field->required,
        'options' => $field->options,
        'conditional_field' => $field->conditional_field,
        'conditional_value' => $field->conditional_value,
        'sort_order' => $field->sort_order,
        'is_resource' => $field->is_resource,
        'resource_key' => $field->resource_key,
        'help_text' => $field->help_text
      ];
    });

    return response()->json([
      'meta_fields' => $formatted_fields,
      'pricing_type' => $service ? $service->pricing_type : 0,
      'guest_capacity' => $service ? $service->guest_capacity : null,
      'service_details' => $service ? $service->service_details : null
    ]);
  }

  /**
   * Calculate total price based on pricing type
   *
   * @param  \App\Models\TimeSlot $timeSlot
   * @return float
   */
  private function calculateTotalPrice($timeSlot)
  {
    // Slot-based pricing: use slot price directly
    return $timeSlot->price ?? 0;
  }

  /**
   * Apply pricing rules to slot price
   *
   * @param  int $service_id
   * @param  float $slot_price
   * @param  string $date (YYYY-MM-DD)
   * @return float
   */
  private function applyPricingRules($service_id, $slot_price, $date)
  {
    $extra_amount = 0;

    // Get active pricing rules for this service
    $pricing_rules = PricingRule::where('service_id', $service_id)
      ->where('status', 1)
      ->get();

    foreach($pricing_rules as $rule){
      $apply = false;

      // Seasonal rule - check if date is between start and end date
      if($rule->rule_type == 0 && $rule->start_date && $rule->end_date){
        if($date >= $rule->start_date && $date <= $rule->end_date){
          $apply = true;
        }
      }

      // Weekend rule - check if day is in the days array
      if($rule->rule_type == 1 && $rule->days){
        $day_name = strtolower(Carbon::parse($date)->format('D')); // mon, tue, etc.
        $days = is_array($rule->days) ? $rule->days : json_decode($rule->days, true);
        if(is_array($days) && in_array($day_name, $days)){
          $apply = true;
        }
      }

      // Holiday rule - check if date matches specific date
      if($rule->rule_type == 2 && $rule->start_date){
        if($date == $rule->start_date){
          $apply = true;
        }
      }

      if($apply){
        if($rule->price_type == 0){ // Fixed amount
          $extra_amount += $rule->amount;
        }elseif($rule->price_type == 1){ // Percentage
          $extra_amount += ($slot_price * $rule->amount / 100);
        }
      }
    }

    return $extra_amount;
  }

  /**
   * Validate promo code (promo-only with category support)
   *
   * @param  string $code
   * @param  int $service_id
   * @return \App\Models\DiscountRule|null
   */
  private function validatePromoCode($code, $service_id)
  {
    if(empty($code)) return null;

    // Get service to determine its category
    $service = Service::find($service_id);
    $service_category_id = $service ? $service->service_category_id : null;

    // Priority 1: Exact service_id match (highest priority)
    $promo = DiscountRule::where('code', $code)
      ->where('status', 1)
      ->where('service_id', $service_id)
      ->where(function($query){
        $today = date('Y-m-d');
        $query->whereNull('start_date')
              ->orWhere(function($q) use ($today){
                $q->where('start_date', '<=', $today)
                  ->where('end_date', '>=', $today);
              });
      })
      ->first();

    // If no exact service match, try category match
    if(!$promo && $service_category_id){
      $promo = DiscountRule::where('code', $code)
        ->where('status', 1)
        ->where('service_category_id', $service_category_id)
        ->whereNull('service_id')
        ->where(function($query){
          $today = date('Y-m-d');
          $query->whereNull('start_date')
                ->orWhere(function($q) use ($today){
                  $q->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
                });
        })
        ->first();
    }

    // If no category match, try global promo (both null)
    if(!$promo){
      $promo = DiscountRule::where('code', $code)
        ->where('status', 1)
        ->whereNull('service_id')
        ->whereNull('service_category_id')
        ->where(function($query){
          $today = date('Y-m-d');
          $query->whereNull('start_date')
                ->orWhere(function($q) use ($today){
                  $q->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
                });
        })
        ->first();
    }

    return $promo;
  }

  /**
   * Calculate discount amount (promo-only)
   *
   * @param  float $price
   * @param  int $service_id
   * @param  string|null $promo_code
   * @return array
   */
  private function calculateDiscount($price, $service_id, $promo_code = null)
  {
    $discount_amount = 0;
    $discount_details = [];

    // Check promo code
    if($promo_code){
      $promo = $this->validatePromoCode($promo_code, $service_id);
      if($promo){
        if($promo->discount_type == 0){ // Fixed
          $discount_amount += $promo->amount;
        }else{ // Percentage
          $discount_amount += ($price * $promo->amount / 100);
        }
        $discount_details[] = 'Promo: ' . $promo->name;
      }
    }

    // Ensure discount doesn't exceed price
    if($discount_amount > $price){
      $discount_amount = $price;
    }

    return [
      'discount_amount' => $discount_amount,
      'discount_details' => $discount_details
    ];
  }

  /**
   * Check if two time ranges overlap
   * @param string $slot1_start
   * @param string $slot1_end
   * @param string $slot2_start
   * @param string $slot2_end
   * @return bool
   */
  private function checkTimeOverlap($slot1_start, $slot1_end, $slot2_start, $slot2_end)
  {
    $times1 = $this->normalizeTimeRange($slot1_start, $slot1_end);
    $times2 = $this->normalizeTimeRange($slot2_start, $slot2_end);

    // Check overlap: slot1_start < slot2_end AND slot1_end > slot2_start
    return ($times1['start'] < $times2['end'] && $times1['end'] > $times2['start']);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      $data = request()->all();

      // Debug: Uncomment to see all submitted data
      // dd($data);

      $validator = Validator::make($data,
        array(
          'service_id'       => 'required',
          'name'             => 'required',
          'phone'            => 'required',
          'emergency_contact'=> 'required',
          'check_in_date'    => 'required',
          'check_in_time'    => 'nullable',
          'check_out_date'   => 'required',
          'check_out_time'   => 'nullable',
        )
      );

      if($validator->fails()){
        return redirect()->back()->withErrors($validator)->withInput();
      }

      // Convert dates from DD-MM-YYYY to YYYY-MM-DD
      try {
        if(isset($data['check_in_date']) && !empty($data['check_in_date'])){
          $data['check_in_date'] = Carbon::createFromFormat('d-m-Y', $data['check_in_date'])->format('Y-m-d');
        }
        if(isset($data['check_out_date']) && !empty($data['check_out_date'])){
          $data['check_out_date'] = Carbon::createFromFormat('d-m-Y', $data['check_out_date'])->format('Y-m-d');
        }
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['date' => 'Invalid date format. Please use DD-MM-YYYY'])->withInput();
      }

      // For availability checking, use check_in_date as the main booking date
      $booking_date = $data['check_in_date'];

      // Get service for pricing calculation and availability check
      $service = Service::find($data['service_id']);

      // Check availability before booking (REQUIRED)
      if(!$service){
        return redirect()->back()->withErrors(['service_id' => 'Invalid service selected'])->withInput();
      }

      // Validate service access for user's counter
      $user = Auth::user();
      $userCounter = $user->counters()->first();
      if ($userCounter) {
        $allowedServiceIds = $userCounter->services()->pluck('services.id')->toArray();
        if (!empty($allowedServiceIds) && !in_array($data['service_id'], $allowedServiceIds)) {
          return redirect()->back()->withErrors(['service_id' => 'Unauthorized service access'])->withInput();
        }
      }

      // Handle time slot for ALL services (slot-based booking system)
      if(isset($data['time_slot_id']) && $data['time_slot_id']){
        $timeSlot = \App\Models\TimeSlot::find($data['time_slot_id']);
        if($timeSlot){
          // Set start_time and end_time from slot times (for display/reference only)
          $data['start_time'] = $timeSlot->start_time;
          $data['end_time'] = $timeSlot->end_time;
        }else{
          return redirect()->back()->withErrors(['time_slot_id' => 'Invalid time slot selected'])->withInput();
        }
      }else{
        return redirect()->back()->withErrors(['time_slot_id' => 'Time slot is required'])->withInput();
      }

      // Check for overlapping slot booking using time range overlap detection
      // This prevents booking Full Day Night if Full Day is already booked (and vice versa)
      $existingBookings = Booking::where('service_id', $data['service_id'])
        ->where('check_in_date', $booking_date)
        ->whereIn('status', [0, 1]) // Pending and Confirmed
        ->whereNotNull('time_slot_id')
        ->with('timeSlot')
        ->get();

      $hasOverlap = false;
      foreach($existingBookings as $existingBooking) {
        if($existingBooking->timeSlot) {
          if($this->checkTimeOverlap(
            $timeSlot->start_time,
            $timeSlot->end_time,
            $existingBooking->timeSlot->start_time,
            $existingBooking->timeSlot->end_time
          )) {
            $hasOverlap = true;
            break;
          }
        }
      }

      if($hasOverlap){
        return redirect()->back()->withErrors([
          'time_slot_id' => 'This service is already booked for an overlapping time slot. Please choose a different time slot.'
        ])->withInput();
      }

      // Slot-based pricing: use slot price directly
      if(!$timeSlot){
        return redirect()->back()->withErrors([
          'time_slot_id' => 'Please create a Time Slot for this service before creating a booking.'
        ])->withInput();
      }
      $total_price = $timeSlot->price ?? 0;

      // Get promo code
      $promo_code = isset($data['promo_code']) ? $data['promo_code'] : null;

      // Get manual discount
      $manual_discount = isset($data['manual_discount']) ? floatval($data['manual_discount']) : 0;

      // Calculate discount (promo code only)
      $discount_result = $this->calculateDiscount($total_price, $service->id, $promo_code);
      $promo_discount_amount = $discount_result['discount_amount'];

      // Total discount = promo discount + manual discount
      $total_discount = $promo_discount_amount + $manual_discount;

      // Calculate final price
      $final_price = $total_price - $total_discount;

      $data['total_price'] = $total_price;
      $data['discount_amount'] = $promo_discount_amount;
      $data['manual_discount'] = $manual_discount;
      $data['final_price'] = $final_price;

    $user = Auth::user();
    $user_id = $user->id;
    $data['updated_by'] = $data['created_by'] = $user_id;
    $data['user_id'] = $user_id;

    // Get counter_id from user's assigned counters (first counter if multiple)
    $userCounter = $user->counters()->first();
    if($userCounter){
      $data['counter_id'] = $userCounter->id;
    }
    // Note: Booking has no relation with gate

    // Handle meta values and files
    $meta_values = [];
    $meta_files = [];

    // Get all meta field definitions for conditional validation
    $service = Service::find($data['service_id']);
    if ($service && $service->service_category_id) {
      $meta_field_definitions = CategoryMetaField::where('service_category_id', $service->service_category_id)->get()->keyBy('field_name');
    } else {
      $meta_field_definitions = collect();
    }

    if(isset($data['meta_values'])){
      $meta_values = $data['meta_values'];
      unset($data['meta_values']);
    }

    // Handle file uploads
    if($request->hasFile('meta_files')){
      foreach($request->file('meta_files') as $field_name => $file){
        if($file->isValid()){
          $filePath = $file->store('booking_files', 'public');
          $meta_files[$field_name] = $filePath;
        }
      }
    }

    // Filter meta values based on conditional logic
    $filtered_meta_values = [];
    foreach($meta_values as $field_name => $value){
      $field_def = $meta_field_definitions->get($field_name);
      if($field_def){
        // Check if field has conditional logic
        if($field_def->conditional_field && $field_def->conditional_value){
          // Check if condition is met
          $trigger_value = $meta_values[$field_def->conditional_field] ?? null;
          if($trigger_value == $field_def->conditional_value){
            $filtered_meta_values[$field_name] = $value;
          }
        }else{
          $filtered_meta_values[$field_name] = $value;
        }
      }
    }

    // Also filter meta files based on conditional logic
    $filtered_meta_files = [];
    foreach($meta_files as $field_name => $file_path){
      $field_def = $meta_field_definitions->get($field_name);
      if($field_def){
        // Check if field has conditional logic
        if($field_def->conditional_field && $field_def->conditional_value){
          $trigger_value = $filtered_meta_values[$field_def->conditional_field] ?? null;
          if($trigger_value == $field_def->conditional_value){
            $filtered_meta_files[$field_name] = $file_path;
          }
        }else{
          $filtered_meta_files[$field_name] = $file_path;
        }
      }
    }

    unset($data['_token']);

    // Remove non-database fields before create
    // These fields are used for UI/filtering only and should not be saved to bookings table
    $nonDatabaseFields = [
      'service_category_id',  // Used for category filtering in create form
    ];

    foreach($nonDatabaseFields as $field){
      unset($data[$field]);
    }

    // Add date field for backward compatibility (using check_in_date as main date)
    $data['date'] = $data['check_in_date'];

    // Use slot name for time_slot (from selected time slot)
    if(isset($timeSlot) && $timeSlot){
      $data['time_slot'] = $timeSlot->name;
    }else{
      $data['time_slot'] = 'N/A';
    }

    // Debug: Check data before creating booking
    // dd('Creating booking with data:', $data);

    // Build JSON meta_values from submitted data
    $metaValuesJson = [];
    $categoryMetaFields = CategoryMetaField::where('service_category_id', $service->service_category_id)->get();

    foreach($filtered_meta_values as $field_name => $value){
      $file_path = $filtered_meta_files[$field_name] ?? null;

      // Get the label from category_meta_fields
      $fieldDef = $categoryMetaFields->firstWhere('field_name', $field_name);
      $label = $fieldDef ? $fieldDef->field_name : $field_name;

      $metaValuesJson[$field_name] = [
        'label' => $label,
        'value' => $value,
        'file_path' => $file_path
      ];
    }

    // Store file-only meta fields
    foreach($filtered_meta_files as $field_name => $file_path){
      if(!isset($filtered_meta_values[$field_name])){
        $fieldDef = $categoryMetaFields->firstWhere('field_name', $field_name);
        $label = $fieldDef ? $fieldDef->field_name : $field_name;

        $metaValuesJson[$field_name] = [
          'label' => $label,
          'value' => '',
          'file_path' => $file_path
        ];
      }
    }

    // Add meta_values JSON to booking data
    $data['meta_values'] = $metaValuesJson;

    $booking=Booking::create($data);

    if($booking){
      $message="You have successfully created";
      return redirect()->route('bookings.index')->with('flash_success',$message);
    }else{
      return redirect()->back()->withErrors($validator)->withInput();
    }

    } catch (\Exception $e) {
      \Log::error('Booking creation error: ' . $e->getMessage());
      \Log::error($e->getTraceAsString());
      return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput()->with('flash_error', 'Booking failed: ' . $e->getMessage());
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
    $booking = Booking::with(['service','bookingMetaValues'])->findorfail($id);
    $users = User::pluck('name','id');
    return view('admin.bookings.show',compact('booking','users'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $booking = Booking::with(['service'])->findorfail($id);
    $services = Service::where('status',1)->pluck('name','id')->toArray();
    $users = User::pluck('name','id');

    // Get service categories based on user's counter access (same as create)
    $user = Auth::user();
    $userCounter = $user->counters()->first();
    
    if($userCounter){
      // Get categories for services assigned to this counter
      $counterServices = $userCounter->services()->pluck('service_category_id')->unique();
      $service_categories = ServiceCategory::whereIn('id', $counterServices)->where('status', 1)->pluck('name', 'id')->toArray();
    }else{
      // If no counter assigned, show all categories
      $service_categories = ServiceCategory::where('status', 1)->pluck('name', 'id')->toArray();
    }

    // Get meta values from JSON column
    // Use full meta_values to include file_path for file preview
    $meta_values_array = $booking->getMetaValues();

    // Get category meta fields for this booking's service category
    $service = $booking->service;
    if ($service && $service->service_category_id) {
      $meta_fields = CategoryMetaField::where('service_category_id', $service->service_category_id)->get();
    } else {
      $meta_fields = collect();
    }

    return view('admin.bookings.edit',compact('booking','services','users','meta_values_array','meta_fields','service_categories'));
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
    \Log::debug('UPDATE METHOD ENTERED', [
      'booking_id' => $id,
      'request' => $request->all()
    ]);

    $data=request()->except('_method');
    $validator=Validator::make($data,
      array(
        'service_id'   =>'required',
        'check_in_date'=>'required',
        'time_slot_id'=>'required',
        'status'       =>'required',
      )
    );
    if($validator->fails()){
      \Log::debug('UPDATE VALIDATION FAILED', [
        'errors' => $validator->errors()->toArray()
      ]);
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Convert dates from DD-MM-YYYY to YYYY-MM-DD
    try {
      if(isset($data['check_in_date']) && !empty($data['check_in_date'])){
        $data['check_in_date'] = Carbon::createFromFormat('d-m-Y', $data['check_in_date'])->format('Y-m-d');
      }
      if(isset($data['check_out_date']) && !empty($data['check_out_date'])){
        $data['check_out_date'] = Carbon::createFromFormat('d-m-Y', $data['check_out_date'])->format('Y-m-d');
      }
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['date' => 'Invalid date format. Please use DD-MM-YYYY'])->withInput();
    }

    // For availability checking, use check_in_date as the main booking date
    $booking_date = $data['check_in_date'];

    // Load booking model for meta values preservation
    $booking = Booking::findOrFail($id);

    // Handle time slot - find by ID since form submits time_slot_id
    if(isset($data['time_slot_id']) && $data['time_slot_id']){
      $timeSlot = \App\Models\TimeSlot::find($data['time_slot_id']);
      if($timeSlot){
        // Set start_time and end_time from slot times
        $data['start_time'] = $timeSlot->start_time;
        $data['end_time'] = $timeSlot->end_time;
      }else{
        return redirect()->back()->withErrors(['time_slot_id' => 'Invalid time slot selected'])->withInput();
      }
    }else{
      return redirect()->back()->withErrors(['time_slot_id' => 'Time slot is required'])->withInput();
    }

    // Check for overlapping slot booking using time range overlap detection, excluding current booking
    // This prevents booking Full Day Night if Full Day is already booked (and vice versa)
    $existingBookings = Booking::where('service_id', $data['service_id'])
      ->where('check_in_date', $booking_date)
      ->whereIn('status', [0, 1]) // Pending and Confirmed
      ->where('id', '!=', $id) // Exclude current booking
      ->whereNotNull('time_slot_id')
      ->with('timeSlot')
      ->get();

    $hasOverlap = false;
    foreach($existingBookings as $existingBooking) {
      if($existingBooking->timeSlot) {
        if($this->checkTimeOverlap(
          $timeSlot->start_time,
          $timeSlot->end_time,
          $existingBooking->timeSlot->start_time,
          $existingBooking->timeSlot->end_time
        )) {
          $hasOverlap = true;
          break;
        }
      }
    }

    if($hasOverlap){
      return redirect()->back()->withErrors([
        'time_slot' => 'This service is already booked for an overlapping time slot. Please choose a different time slot.'
      ])->withInput();
    }

    // Get service for pricing calculation
    $service = Service::find($data['service_id']);
    if($service){
      // Slot-based pricing: use slot price directly
      if(!$timeSlot){
        return redirect()->back()->withErrors([
          'time_slot_id' => 'Please create a Time Slot for this service before creating a booking.'
        ])->withInput();
      }
      $total_price = $timeSlot->price ?? 0;

      // Get promo code
      $promo_code = isset($data['promo_code']) ? $data['promo_code'] : null;

      // Get manual discount
      $manual_discount = isset($data['manual_discount']) ? floatval($data['manual_discount']) : 0;

      // Calculate discount (promo code only)
      $discount_result = $this->calculateDiscount($total_price, $service->id, $promo_code);
      $promo_discount_amount = $discount_result['discount_amount'];

      // Total discount = promo discount + manual discount
      $total_discount = $promo_discount_amount + $manual_discount;

      // Calculate final price
      $final_price = $total_price - $total_discount;

      $data['total_price'] = $total_price;
      $data['discount_amount'] = $promo_discount_amount;
      $data['manual_discount'] = $manual_discount;
      $data['final_price'] = $final_price;
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;

    // Use slot name for time_slot (from selected time slot)
    if(isset($timeSlot) && $timeSlot){
      $data['time_slot'] = $timeSlot->name;
    }else{
      $data['time_slot'] = 'N/A';
    }

    // Remove non-database fields before update
    // These fields are used for UI/filtering only and should not be saved to bookings table
    $nonDatabaseFields = [
      'service_category_id',  // Used for category filtering in edit form
      '_token',               // CSRF token
      '_method',              // HTTP method override
    ];

    foreach($nonDatabaseFields as $field){
      unset($data[$field]);
    }

    // Handle meta values
    $meta_values = [];
    if(isset($data['meta_values'])){
      $meta_values = $data['meta_values'];
      unset($data['meta_values']);
    }

    \Log::debug('UPLOAD DEBUG - META VALUES ARRAY', [
      'meta_values' => $meta_values
    ]);

    // Handle file uploads
    $meta_files = [];
    if($request->hasFile('meta_files')){
      \Log::debug('UPLOAD DEBUG - REQUEST FILE', [
        'has_files' => true,
        'file_fields' => array_keys($request->file('meta_files'))
      ]);
      foreach($request->file('meta_files') as $field_name => $file){
        if($file->isValid()){
          $filePath = $file->store('booking_files', 'public');
          \Log::debug('UPLOAD DEBUG - FILE STORED', [
            'field_name' => $field_name,
            'file_path' => $filePath
          ]);
          $meta_files[$field_name] = $filePath;
        }
      }
    } else {
      \Log::debug('UPLOAD DEBUG - NO FILES IN REQUEST');
    }

    // Remove meta_files from data array (files are stored in meta_values JSON, not as separate column)
    if(isset($data['meta_files'])){
      unset($data['meta_files']);
    }

    \Log::debug('UPLOAD DEBUG - META FILES ARRAY', [
      'meta_files' => $meta_files
    ]);

    // Get existing meta values to preserve file_paths
    $existingMeta = $booking->getMetaValues();

    // Build JSON meta_values from submitted data
    $metaValuesJson = [];
    $service = Service::find($data['service_id']);
    if ($service && $service->service_category_id) {
      $categoryMetaFields = CategoryMetaField::where('service_category_id', $service->service_category_id)->get();
    } else {
      $categoryMetaFields = collect();
    }

    // First, preserve all existing meta values
    foreach($existingMeta as $field_name => $field_data){
      $fieldDef = $categoryMetaFields->firstWhere('field_name', $field_name);
      if($fieldDef){
        $metaValuesJson[$field_name] = [
          'label' => $field_data['label'] ?? $fieldDef->field_name,
          'value' => $field_data['value'] ?? '',
          'file_path' => $field_data['file_path'] ?? null
        ];
      }
    }

    // Then, update with submitted values
    foreach($meta_values as $field_name => $value){
      // Get the label from category_meta_fields
      $fieldDef = $categoryMetaFields->firstWhere('field_name', $field_name);
      $label = $fieldDef ? $fieldDef->field_name : $field_name;

      // Preserve existing file_path if no new file uploaded
      $file_path = isset($existingMeta[$field_name]['file_path'])
        ? $existingMeta[$field_name]['file_path']
        : null;

      // If new file uploaded, replace file_path
      if(isset($meta_files[$field_name])){
        $file_path = $meta_files[$field_name];
      }

      $metaValuesJson[$field_name] = [
        'label' => $label,
        'value' => $value,
        'file_path' => $file_path
      ];
    }

    // Store file-only meta fields (fields with file but no value in meta_values)
    foreach($meta_files as $field_name => $file_path){
      if(!isset($meta_values[$field_name])){
        $fieldDef = $categoryMetaFields->firstWhere('field_name', $field_name);
        $label = $fieldDef ? $fieldDef->field_name : $field_name;

        $metaValuesJson[$field_name] = [
          'label' => $label,
          'value' => '',
          'file_path' => $file_path
        ];
      }
    }

    $data['meta_values'] = $metaValuesJson;

    \Log::debug('UPLOAD DEBUG - FINAL META VALUES', [
      'meta_values_json' => $metaValuesJson
    ]);

    \Log::debug('UPDATE DATE CONVERSION',[
      'incoming_check_in_date' => $request->check_in_date,
      'incoming_check_out_date' => $request->check_out_date,
      'converted_check_in_date' => $data['check_in_date'] ?? null,
      'converted_check_out_date' => $data['check_out_date'] ?? null,
    ]);

    $updated=Booking::where('id', $id)->update($data);

    if($updated){
      \Log::debug('UPDATE SUCCESSFUL', [
        'booking_id' => $id
      ]);
      $message="You have successfully Updated";
      return redirect()->back()->with('flash_success',$message);
    }else{
      \Log::debug('UPDATE FAILED', [
        'booking_id' => $id
      ]);
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
    $deleted=Booking::where('id',$id)->delete();
    if($deleted){
      $message="You have successfully Deleted";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
    }
  }

  /**
   * Validate promo code via AJAX
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function validatePromo(Request $request)
  {
    $promo_code = $request->get('promo_code');
    $service_id = $request->get('service_id');
    $total_price = $request->get('total_price');

    if(!$promo_code || !$service_id){
      return response()->json([
        'success' => false,
        'message' => 'Promo code and service are required'
      ]);
    }

    $promo = $this->validatePromoCode($promo_code, $service_id);

    if($promo){
      $discount_result = $this->calculateDiscount($total_price, $service_id, $promo_code);

      return response()->json([
        'success' => true,
        'message' => $promo->name,
        'discount_amount' => $discount_result['discount_amount'],
        'discount_details' => $discount_result['discount_details']
      ]);
    }else{
      return response()->json([
        'success' => false,
        'message' => 'Invalid or expired promo code'
      ]);
    }
  }

  /**
   * Check availability for a service on a specific date
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function checkAvailability(Request $request)
  {
    $service_id = $request->get('service_id');
    $date = $request->get('date');
    $check_in_date = $request->get('check_in_date');
    $check_out_date = $request->get('check_out_date');
    $start_time = $request->get('start_time');
    $end_time = $request->get('end_time');
    $time_slot_id = $request->get('time_slot_id');

    // Handle time slot for hourly services
    if($time_slot_id){
      $timeSlot = \App\Models\TimeSlot::find($time_slot_id);
      if($timeSlot){
        $start_time = $timeSlot->start_time;
        $end_time = $timeSlot->end_time;
      }
    }

    if(!$service_id){
      return response()->json([
        'success' => false,
        'message' => 'Service is required'
      ]);
    }

    // Handle single date or date range
    $start_date = $date ?: $check_in_date;
    $end_date = $check_out_date ?: $start_date;

    if(!$start_date){
      return response()->json([
        'success' => false,
        'message' => 'Date is required'
      ]);
    }

    // Convert date format if needed (DD-MM-YYYY to YYYY-MM-DD)
    try {
      if(strpos($start_date, '-') !== false && strlen($start_date) == 10){
        $start_date = Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d');
      }
      if($end_date && strpos($end_date, '-') !== false && strlen($end_date) == 10){
        $end_date = Carbon::createFromFormat('d-m-Y', $end_date)->format('Y-m-d');
      }
    } catch(\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid date format'
      ]);
    }

    $service = Service::find($service_id);
    if(!$service){
      return response()->json([
        'success' => false,
        'message' => 'Service not found'
      ]);
    }

    // Check availability for the date range (with time for hourly services)
    $availability = $this->getAvailabilityStatusForRange($service_id, $start_date, $end_date, $start_time, $end_time);

    return response()->json($availability);
  }

  /**
   * Check room availability for a service on a specific date
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function checkRoomAvailability(Request $request)
  {
    // DEBUG: Log immediately when method is called
    \Log::debug('checkRoomAvailability method entered', [
      'url' => $request->fullUrl(),
      'method' => $request->method(),
      'ip' => $request->ip(),
      'user' => auth()->id()
    ]);

    try {
      $service_id = $request->get('service_id');
      $date = $request->get('date');
      $check_out_date = $request->get('check_out_date', $date); // Default to check_in if not provided

      // Time parameters for hourly services
      $start_time = $request->get('start_time');
      $end_time = $request->get('end_time');

      // Booking ID to exclude (for edit page - don't block current booking)
      $exclude_booking_id = $request->get('booking_id');

      // DEBUG: Log received values
      \Log::debug('checkRoomAvailability parameters', [
        'service_id' => $service_id,
        'raw_date' => $date,
        'raw_check_out' => $check_out_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'exclude_booking_id' => $exclude_booking_id
      ]);

      // Get service details for pricing type
      $service = Service::find($service_id);
      $pricing_type = $service ? $service->pricing_type : 0;

      // Validate required fields
      if(!$service_id || !$date){
        return response()->json([
          'success' => false,
          'message' => 'Service and date are required'
        ]);
      }

      // Convert date format if needed (DD-MM-YYYY to YYYY-MM-DD)
      // Format from frontend: 20-04-2026
      try {
        // Check if date is in DD-MM-YYYY format (has dashes and day is first)
        if(strpos($date, '-') !== false && strlen($date) == 10){
          $parts = explode('-', $date);
          if(count($parts) == 3 && intval($parts[0]) <= 31){
            // Likely DD-MM-YYYY format
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
          }
        }
        if($check_out_date && strpos($check_out_date, '-') !== false && strlen($check_out_date) == 10){
          $parts = explode('-', $check_out_date);
          if(count($parts) == 3 && intval($parts[0]) <= 31){
            $check_out_date = Carbon::createFromFormat('d-m-Y', $check_out_date)->format('Y-m-d');
          }
        }
      } catch(\Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Invalid date format: ' . $e->getMessage()
        ]);
      }

      \Log::debug('Dates after conversion', [
        'date' => $date,
        'check_out_date' => $check_out_date
      ]);

      // Ensure check_out is not before check_in
      if($check_out_date < $date){
        $check_out_date = $date;
      }

      // Service is now the bookable unit - check if service is available for date/time
      $query = Booking::where('service_id', $service_id)
        ->whereIn('status', [0, 1]) // Pending and Confirmed
        ->where(function($query) use ($date, $check_out_date) {
          // Date range overlap: existing booking overlaps with requested range
          $query->where('check_in_date', '<=', $check_out_date)
                ->where('check_out_date', '>=', $date);
        })
        ->whereNotNull('time_slot_id'); // Must have a slot assigned

      // Exclude current booking from overlap detection (edit page)
      if($exclude_booking_id){
        $query->where('id', '!=', $exclude_booking_id);
      }

      // If time range provided, load slot relationships for overlap check
      if($start_time && $end_time){
        $overlappingBookings = $query->with('timeSlot')->get()->filter(function($booking) use ($start_time, $end_time) {
          // If booking has no slot, it doesn't block anything
          if(!$booking->timeSlot){
            return false;
          }

          // Get booking slot times
          $bookingStart = $booking->timeSlot->start_time;
          $bookingEnd = $booking->timeSlot->end_time;

          // Normalize times for comparison (handle overnight slots)
          $newTimes = $this->normalizeTimeRange($start_time, $end_time);
          $existTimes = $this->normalizeTimeRange($bookingStart, $bookingEnd);

          // Time overlap logic: new_start < existing_end AND new_end > existing_start
          $hasOverlap = ($newTimes['start'] < $existTimes['end'] && $newTimes['end'] > $existTimes['start']);

          return $hasOverlap; // Only include bookings that actually overlap in time
        });
      }else{
        // No time provided - fall back to date-only check
        $overlappingBookings = $query->get();
      }

      // Service is unavailable if there are any overlapping bookings
      $isAvailable = $overlappingBookings->isEmpty();

      \Log::debug('Service availability check', [
        'service_id' => $service_id,
        'date' => $date,
        'check_out_date' => $check_out_date,
        'is_available' => $isAvailable,
        'overlapping_bookings_count' => $overlappingBookings->count()
      ]);

      return response()->json([
        'success' => true,
        'service_id' => $service_id,
        'check_in_date' => $date,
        'check_out_date' => $check_out_date,
        'is_available' => $isAvailable
      ]);
    } catch(\Exception $e) {
      // Always return JSON even on unexpected errors
      return response()->json([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
      ], 500);
    }
  }

  /**
   * Get availability status for a service on a specific date
   *
   * @param  int $service_id
   * @param  string $date (YYYY-MM-DD)
   * @return array
   */
  private function getAvailabilityStatus($service_id, $date)
  {
    $service = Service::find($service_id);

    // Get resource field for this service category
    $resourceField = null;
    if ($service && $service->service_category_id) {
      $resourceField = CategoryMetaField::where('service_category_id', $service->service_category_id)
        ->where('is_resource', 1)
        ->whereNotNull('resource_key')
        ->first();
    }

    // Calculate total resources
    $total_resources = 0;
    if($resourceField && $resourceField->options){
      $options = json_decode($resourceField->options, true);
      if(is_array($options)){
        $total_resources = count($options);
      } elseif(is_string($resourceField->options)){
        $total_resources = count(array_map('trim', explode(',', $resourceField->options)));
      }
    }

    // Single date - no time overlap check possible without slot selection
    return [
      'success' => true,
      'status' => 'available',
      'total_resources' => $total_resources,
      'booked_resources' => 0, // Will be calculated when slot is selected
      'available_resources' => $total_resources, // All resources initially available
      'is_available' => true,
      'message' => 'Select a time slot to check availability',
      'date' => $date,
      'service_name' => $service->name
    ];
  }

  /**
   * Get availability status for a service over a date range
   *
   * @param  int $service_id
   * @param  string $start_date (YYYY-MM-DD)
   * @param  string $end_date (YYYY-MM-DD)
   * @param  string $start_time (optional, for hourly services)
   * @param  string $end_time (optional, for hourly services)
   * @return array
   */
  private function getAvailabilityStatusForRange($service_id, $start_date, $end_date, $start_time = null, $end_time = null)
  {
    $service = Service::find($service_id);
    $pricing_type = $service->pricing_type ?? 0;

    // Get resource field for this service category to count total resources
    $resourceField = null;
    if ($service && $service->service_category_id) {
      $resourceField = CategoryMetaField::where('service_category_id', $service->service_category_id)
        ->where('is_resource', 1)
        ->whereNotNull('resource_key')
        ->first();
    }

    // Calculate total resources from meta field options
    $total_resources = 0;
    if($resourceField && $resourceField->options){
      $options = json_decode($resourceField->options, true);
      if(is_array($options)){
        $total_resources = count($options);
      } elseif(is_string($resourceField->options)){
        // Handle comma-separated string format
        $total_resources = count(array_map('trim', explode(',', $resourceField->options)));
      }
    }

    // Query bookings for date range with time overlap check
    $query = Booking::where('service_id', $service_id)
      ->whereIn('status', [0, 1]) // Pending and Confirmed
      ->where(function($query) use ($start_date, $end_date) {
        $query->where('check_in_date', '<=', $end_date)
              ->where('check_out_date', '>=', $start_date);
      })
      ->whereNotNull('meta_values')
      ->whereNotNull('time_slot_id');

    // If time provided, filter by time overlap using slot times
    if($start_time && $end_time){
      $bookings = $query->with('timeSlot')->get()->filter(function($booking) use ($start_time, $end_time) {
        if(!$booking->timeSlot) return false;

        // Normalize times for comparison (handle overnight slots)
        $newTimes = $this->normalizeTimeRange($start_time, $end_time);
        $existTimes = $this->normalizeTimeRange($booking->timeSlot->start_time, $booking->timeSlot->end_time);

        // Time overlap: new_start < exist_end AND new_end > exist_start
        return ($newTimes['start'] < $existTimes['end'] && $newTimes['end'] > $existTimes['start']);
      });
    } else {
      // No time provided - get all bookings for date range
      $bookings = $query->get();
    }

    // Count distinct booked resources
    $resourceKey = $resourceField ? $resourceField->resource_key : null;
    $booked_resources = 0;
    if($resourceKey){
      $booked_resources = $bookings
        ->pluck('meta_values')
        ->map(function($metaValues) use ($resourceKey) {
          if(is_string($metaValues)){
            $metaValues = json_decode($metaValues, true);
          }
          return isset($metaValues[$resourceKey]) ? $metaValues[$resourceKey] : null;
        })
        ->filter()
        ->unique()
        ->count();
    }

    // Calculate available resources
    $available_resources = $total_resources - $booked_resources;
    $is_available = $available_resources > 0;
    $status = $is_available ? 'available' : 'fully_booked';
    $message = $is_available ? 'Slots Available' : 'Fully Booked';

    return [
      'success' => true,
      'status' => $status,
      'total_resources' => $total_resources,
      'booked_resources' => $booked_resources,
      'available_resources' => $available_resources,
      'is_available' => $is_available,
      'message' => $message,
      'check_in_date' => $start_date,
      'check_out_date' => $end_date,
      'service_name' => $service->name,
      'pricing_type' => $pricing_type
    ];
  }

  /**
   * Normalize time range for comparison
   * Handles overnight slots where end_time < start_time (e.g., 20:00 to 08:00)
   * @param string $start_time
   * @param string $end_time
   * @return array ['start' => timestamp, 'end' => timestamp]
   */
  private function normalizeTimeRange($start_time, $end_time)
  {
    $start = strtotime($start_time);
    $end = strtotime($end_time);

    // If end_time <= start_time, treat as overnight slot (add 24 hours to end)
    if($end <= $start){
      $end += 24 * 60 * 60; // Add 24 hours in seconds
    }

    return ['start' => $start, 'end' => $end];
  }


  /**
   * Show availability calendar
   *
   * @return \Illuminate\Http\Response
   */
  public function availabilityCalendar()
  {
    $user = Auth::user();
    $userCounter = $user->counters()->first();

    // Get allowed service IDs for user's counter
    $allowedServiceIds = [];
    if ($userCounter) {
      $allowedServiceIds = $userCounter->services()->pluck('services.id')->toArray();
    }

    // Filter categories to only show those containing allowed services
    if (!empty($allowedServiceIds)) {
      $allowedCategoryIds = Service::whereIn('id', $allowedServiceIds)
        ->where('status', 1)
        ->distinct()
        ->pluck('service_category_id')
        ->toArray();
      $service_categories = ServiceCategory::whereIn('id', $allowedCategoryIds)
        ->where('status', 1)
        ->pluck('name','id');
    } else {
      // No services assigned - show empty categories
      $service_categories = collect([]);
    }

    $services = Service::where('status', 1)->pluck('name', 'id');
    return view('admin.bookings.availability_calendar', compact('services', 'service_categories'));
  }

  /**
   * Get calendar data for availability
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function getCalendarData(Request $request)
  {
    $service_id = $request->get('service_id');
    $month = $request->get('month', date('m'));
    $year = $request->get('year', date('Y'));

    if(!$service_id){
      return response()->json([
        'success' => false,
        'message' => 'Service is required'
      ]);
    }

    $service = Service::find($service_id);
    if(!$service){
      return response()->json([
        'success' => false,
        'message' => 'Service not found'
      ]);
    }

    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $events = [];

    // Get all active time slots for this service
    $slots = \App\Models\TimeSlot::where('service_id', $service_id)
      ->where('status', 1)
      ->get();

    for($day = 1; $day <= $days_in_month; $day++){
      $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

      // Get existing bookings for this date
      $existingBookings = Booking::where('service_id', $service_id)
        ->where('check_in_date', $date)
        ->whereIn('status', [0, 1])
        ->whereNotNull('start_time')
        ->whereNotNull('end_time')
        ->get(['start_time', 'end_time']);

      // Count available slots (non-conflicting)
      $availableSlots = 0;
      foreach($slots as $slot){
        // Normalize slot times for comparison (handle overnight slots)
        $slotTimes = $this->normalizeTimeRange($slot->start_time, $slot->end_time);
        $slotStart = $slotTimes['start'];
        $slotEnd = $slotTimes['end'];
        $hasConflict = false;

        foreach($existingBookings as $booking){
          // Normalize booking times
          $bookTimes = $this->normalizeTimeRange($booking->start_time, $booking->end_time);
          $bookStart = $bookTimes['start'];
          $bookEnd = $bookTimes['end'];

          if($slotStart < $bookEnd && $slotEnd > $bookStart){
            $hasConflict = true;
            break;
          }
        }

        if(!$hasConflict){
          $availableSlots++;
        }
      }

      // Determine status based on available slots
      $totalSlots = $slots->count();
      $booked_count = $totalSlots - $availableSlots;

      $color = '#28a745'; // Green - available
      $status = 'available';
      $title = $availableSlots . ' slots available';

      if($availableSlots == 0 && $totalSlots > 0){
        $color = '#dc3545'; // Red - fully booked
        $status = 'fully_booked';
        $title = 'Fully Booked';
      } elseif($availableSlots <= 1 && $totalSlots > 2){
        $color = '#ffc107'; // Yellow - limited
        $status = 'limited';
        $title = 'Only ' . $availableSlots . ' slot' . ($availableSlots == 1 ? '' : 's') . ' left';
      }

      $events[] = [
        'date' => $date,
        'title' => $title,
        'color' => $color,
        'status' => $status,
        'available_slots' => $availableSlots,
        'total_slots' => $totalSlots,
        'booked_count' => $booked_count
      ];
    }

    return response()->json([
      'success' => true,
      'service_name' => $service->name,
      'events' => $events
    ]);
  }

  /**
   * Find next available date
   *
   * @param  int $service_id
   * @param  string $start_date (YYYY-MM-DD)
   * @return string|null
   */
  private function findNextAvailableDate($service_id, $start_date)
  {
    $service = Service::find($service_id);
    if(!$service) return null;

    $current = Carbon::parse($start_date);
    $max_days = 365; // Search up to 1 year ahead

    // Get all active slots for this service
    $slots = \App\Models\TimeSlot::where('service_id', $service_id)
      ->where('status', 1)
      ->get();

    if($slots->isEmpty()){
      return null; // No slots defined, can't find availability
    }

    for($i = 1; $i <= $max_days; $i++){
      $check_date = $current->copy()->addDays($i)->format('Y-m-d');

      // Get existing bookings for this date
      $existingBookings = Booking::where('service_id', $service_id)
        ->where('check_in_date', $check_date)
        ->whereIn('status', [0, 1])
        ->whereNotNull('start_time')
        ->whereNotNull('end_time')
        ->get(['start_time', 'end_time']);

      // Check if any slot is available (not conflicted)
      $hasAvailableSlot = false;
      foreach($slots as $slot){
        // Normalize slot times for comparison (handle overnight slots)
        $slotTimes = $this->normalizeTimeRange($slot->start_time, $slot->end_time);
        $slotStart = $slotTimes['start'];
        $slotEnd = $slotTimes['end'];
        $hasConflict = false;

        foreach($existingBookings as $booking){
          // Normalize booking times
          $bookTimes = $this->normalizeTimeRange($booking->start_time, $booking->end_time);
          $bookStart = $bookTimes['start'];
          $bookEnd = $bookTimes['end'];

          if($slotStart < $bookEnd && $slotEnd > $bookStart){
            $hasConflict = true;
            break;
          }
        }

        if(!$hasConflict){
          $hasAvailableSlot = true;
          break;
        }
      }

      if($hasAvailableSlot){
        return $check_date;
      }
    }

    return null;
  }

  /**
   * Counter report view
   */
  public function counterReport()
  {
    $counters = \App\Models\Counter::where('status', 1)->pluck('name', 'id');
    $users = User::where('status', 1)->pluck('name', 'id');
    return view('admin.bookings.counter_report', compact('counters', 'users'));
  }

  /**
   * Get counter report data (AJAX)
   */
  public function getCounterReportData(Request $request)
  {
    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $counter_id = $request->get('counter_id');
    $user_id = $request->get('user_id');

    // Convert dates from DD-MM-YYYY to YYYY-MM-DD
    try {
      if($start_date){
        $start_date = Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d');
      }
      if($end_date){
        $end_date = Carbon::createFromFormat('d-m-Y', $end_date)->format('Y-m-d');
      }
    } catch(\Exception $e) {
      return response()->json(['success' => false, 'message' => 'Invalid date format']);
    }

    $query = Booking::with(['service', 'creator', 'counter'])
      ->whereIn('status', [0, 1]); // Pending and Confirmed

    if($start_date && $end_date){
      $query->whereBetween('check_in_date', [$start_date, $end_date]);
    }

    if($counter_id){
      $query->where('counter_id', $counter_id);
    }

    if($user_id){
      $query->where('created_by', $user_id);
    }

    // For users without 'view_all_bookings' permission, only show their own data
    $user = Auth::user();
    if(!$this->hasPermission($user->role_id, 'view_all_bookings')){
      $userCounterIds = $user->counters()->pluck('counter_id')->toArray();

      $query->where(function($q) use ($user, $userCounterIds) {
        $q->where('created_by', $user->id);
        if(!empty($userCounterIds)){
          $q->orWhereIn('counter_id', $userCounterIds);
        }
      });
    }

    $bookings = $query->orderBy('check_in_date', 'desc')->get();

    // Calculate summary
    $total_bookings = $bookings->count();
    $total_amount = $bookings->sum('final_price');

    // Service-wise breakdown
    $service_breakdown = $bookings->groupBy('service.name')->map(function($group){
      return [
        'count' => $group->count(),
        'amount' => $group->sum('final_price')
      ];
    });

    // User-wise breakdown
    $user_breakdown = $bookings->groupBy('creator.name')->map(function($group){
      return [
        'count' => $group->count(),
        'amount' => $group->sum('final_price')
      ];
    });

    // Counter-wise breakdown
    $counter_breakdown = $bookings->groupBy(function($booking){
      return $booking->counter ? $booking->counter->name : 'No Counter';
    })->map(function($group){
      return [
        'count' => $group->count(),
        'amount' => $group->sum('final_price')
      ];
    });

    return response()->json([
      'success' => true,
      'total_bookings' => $total_bookings,
      'total_amount' => $total_amount,
      'bookings' => $bookings,
      'service_breakdown' => $service_breakdown,
      'user_breakdown' => $user_breakdown,
      'counter_breakdown' => $counter_breakdown
    ]);
  }
}
