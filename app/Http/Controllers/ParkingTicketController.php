<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ParkingTicket;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class ParkingTicketController extends Controller
{
  /**
   * Constructor - Apply auth middleware and call parent
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = Auth::user();
    // Check if user has 'view_all_parking_tickets' permission
    $canViewAll = $this->hasPermission($user->role_id, 'view_all_parking_tickets');

    // Check if force_all flag is set (from view_all_parking_tickets method)
    $forceAll = $request->has('force_all') && $request->force_all === true;

    $query = ParkingTicket::with(['creator', 'vehicle', 'parkingCounter']);

    // Permission-based filter - users without view_all permission see only own tickets
    // Unless force_all flag is set (internal call from view_all method)
    if (!$canViewAll && !$forceAll) {
      $query->where('created_by', $user->id);
    }

    // Apply status filter
    if ($request->has('status') && $request->status) {
      $query->where('status', $request->status);
    }

    // Apply date range filter
    if ($request->has('from_date') && $request->from_date) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }
    if ($request->has('to_date') && $request->to_date) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    // Apply user filter (only for users with view_all permission)
    if ($canViewAll && $request->has('user_id') && $request->user_id) {
      $query->where('created_by', $request->user_id);
    }

    // Apply parking counter filter (only for users with view_all permission)
    if ($canViewAll && $request->has('parking_counter_id') && $request->parking_counter_id) {
      $query->where('parking_counter_id', $request->parking_counter_id);
    }

    $parking_tickets = $query->orderBy('created_at', 'desc')->get();

    // Get users for dropdown (only for users with view_all permission)
    // Get users assigned to Parking Counters (matches Package Report pattern)
    $users = [];
    if ($canViewAll) {
      $userIds = \App\Models\ParkingCounter::where('status', 1)
        ->with('users')
        ->get()
        ->pluck('users')
        ->flatten()
        ->pluck('id')
        ->unique()
        ->toArray();

      $users = User::whereIn('id', $userIds)
        ->pluck('name', 'id')
        ->toArray();
    }

    // Get parking counters for dropdown (only for users with view_all permission)
    $counters = [];
    if ($canViewAll) {
      $counters = \App\Models\ParkingCounter::where('status', 1)
        ->pluck('name', 'id')
        ->toArray();
    }

    return view('admin.parking_tickets.index', compact('parking_tickets', 'canViewAll', 'users', 'counters'));
  }

  /**
   * View all parking tickets (requires view_all_parking_tickets permission)
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function view_all_parking_tickets(Request $request)
  {
    $user = Auth::user();

    // Check permission
    if (!$this->hasPermission($user->role_id, 'view_all_parking_tickets')) {
      abort(403, 'Unauthorized access');
    }

    // Set force_all flag and delegate to index()
    $request->merge(['force_all' => true]);
    return $this->index($request);
  }

  /**
   * Show camera scanner for parking tickets
   *
   * @return \Illuminate\Http\Response
   */
  public function scanCamera()
  {
    // Check scan permission
    if (!$this->hasPermission(Auth::user()->role_id, 'scan_parking_tickets')) {
      abort(403, 'Unauthorized access');
    }

    return view('admin.parking_tickets.scan_camera');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $user = Auth::user();
    $parkingCounterName = $user->parkingCounters()->first()->name ?? 'N/A';

    // Check if user has parking counters assigned
    $userParkingCounters = $user->parkingCounters()->where('status', 1)->get();
    if ($userParkingCounters->isEmpty()) {
      return redirect()->route('parking_tickets.index')->with('flash_error', 'You must be assigned to a Parking Counter to create parking tickets.');
    }

    $vehicles = Vehicle::where('status', 'active')->pluck('name', 'id')->toArray();
    return view('admin.parking_tickets.create', compact('vehicles', 'parkingCounterName'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data,
      array(
        'vehicle_id'       => 'required|exists:vehicles,id',
        'vehicle_number'   => 'required|string|max:50',
        'driver_name'      => 'nullable|string|max:100',
        'driver_phone'     => 'nullable|string|max:20',
        'base_price'       => 'required|numeric|min:0',
      )
    );

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = Auth::user();

    // Check if user has parking counters assigned
    $userParkingCounters = $user->parkingCounters()->where('status', 1)->get();
    if ($userParkingCounters->isEmpty()) {
      return redirect()->back()->with('flash_error', 'You must be assigned to a Parking Counter to create parking tickets.');
    }

    // Determine parking counter based on user's assigned parking counters
    $parkingCounterId = null;

    if ($userParkingCounters->count() === 1) {
      // User has exactly one parking counter, use it
      $parkingCounterId = $userParkingCounters->first()->id;
    } elseif ($userParkingCounters->count() > 1) {
      // User has multiple parking counters, use the first one as default
      $parkingCounterId = $userParkingCounters->first()->id;
    }

    // Default parking slot configuration (08:00 to 18:00 = 10 hours)
    $slotStartTime = '08:00:00';
    $slotEndTime = '18:00:00';
    $basePrice = $data['base_price'];

    // Create parking ticket with PAY AT ENTRY system
    // Ticket is auto-checked-in with payment
    $parking_ticket = ParkingTicket::create([
      'vehicle_id'              => $data['vehicle_id'],
      'vehicle_number'          => $data['vehicle_number'],
      'driver_name'             => $data['driver_name'] ?? null,
      'driver_phone'            => $data['driver_phone'] ?? null,
      'base_price'              => $basePrice,
      'hourly_rate'             => $basePrice, // Keep for compatibility
      'parking_slot_start_time' => $slotStartTime,
      'parking_slot_end_time'   => $slotEndTime,
      'status'                  => 'checked_in', // Auto check-in
      'entry_time'              => now(), // Entry time = now
      'paid_amount'             => $basePrice, // Paid at entry
      'total_amount'            => $basePrice, // Initial total = base price
      'extra_amount'            => 0, // No extra yet
      'parking_counter_id'      => $parkingCounterId,
      'created_by'              => $user->id,
    ]);

    // Create entry payment record for cash handover tracking
    \App\Models\ParkingTicketPayment::create([
      'parking_ticket_number' => $parking_ticket->ticket_number,
      'payment_type' => 'entry',
      'amount' => $basePrice,
      'payment_date' => now()->toDateString(),
      'created_by' => $user->id,
      'parking_counter_id' => $parkingCounterId,
    ]);

    if ($parking_ticket) {
      // Redirect to entry receipt
      return redirect()->route('parking_tickets.entry_receipt', $parking_ticket->ticket_number)
        ->with('flash_success', 'Parking ticket created. Entry payment: ' . number_format($basePrice, 2) . ' Tk');
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  /**
   * Show entry receipt after ticket creation
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function entryReceipt($ticket_number)
  {
    $parking_ticket = ParkingTicket::with(['creator', 'vehicle'])->findOrFail($ticket_number);
    return view('admin.parking_tickets.entry_receipt', compact('parking_ticket'));
  }

  /**
   * Display the specified resource.
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function show($ticket_number)
  {
    $parking_ticket = ParkingTicket::with(['creator', 'vehicle'])->findOrFail($ticket_number);
    return view('admin.parking_tickets.show', compact('parking_ticket'));
  }

  /**
   * QR Scan - Show ticket details and action buttons
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function scan($ticket_number)
  {
    $parking_ticket = ParkingTicket::with(['creator', 'vehicle'])->where('ticket_number', $ticket_number)->firstOrFail();
    return view('admin.parking_tickets.scan', compact('parking_ticket'));
  }

  /**
   * Check In - Record entry time
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function checkIn($ticket_number)
  {
    $parking_ticket = ParkingTicket::findOrFail($ticket_number);

    // Validation: Prevent double check-in
    if ($parking_ticket->status == 'checked_in') {
      return redirect()->back()->with('flash_error', 'This ticket is already checked in.');
    }

    if ($parking_ticket->status == 'checked_out') {
      return redirect()->back()->with('flash_error', 'This ticket has already been checked out.');
    }

    // Update status and entry time
    $parking_ticket->update([
      'status' => 'checked_in',
      'entry_time' => now(),
    ]);

    return redirect()->route('parking_tickets.show', $ticket_number)->with('flash_success', 'Check-in successful. Entry time recorded.');
  }

  /**
   * Check Out - Calculate extra amount, redirect to payment if needed
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function checkOut($ticket_number)
  {
    $parking_ticket = ParkingTicket::findOrFail($ticket_number);

    // Validation: Must be checked in first
    if ($parking_ticket->status == 'pending') {
      return redirect()->back()->with('flash_error', 'This ticket has not been checked in yet.');
    }

    if ($parking_ticket->status == 'checked_out') {
      return redirect()->back()->with('flash_error', 'This ticket has already been checked out.');
    }

    if (!$parking_ticket->entry_time) {
      return redirect()->back()->with('flash_error', 'Entry time not found. Cannot process checkout.');
    }

    // Get slot configuration (default 08:00 - 18:00)
    $slotStart = $parking_ticket->parking_slot_start_time ?? '08:00:00';
    $slotEnd = $parking_ticket->parking_slot_end_time ?? '18:00:00';
    $basePrice = $parking_ticket->base_price ?? $parking_ticket->hourly_rate ?? 0;
    $paidAmount = $parking_ticket->paid_amount ?? $basePrice;

    // Calculate slot duration in minutes
    $slotStartMinutes = $this->timeToMinutes($slotStart);
    $slotEndMinutes = $this->timeToMinutes($slotEnd);
    $slotDurationMinutes = $slotEndMinutes - $slotStartMinutes; // e.g., 600 minutes for 10 hours

    // Ensure minimum slot duration of 1 hour (60 minutes) if times are invalid
    if ($slotDurationMinutes <= 0) {
      $slotDurationMinutes = 600; // Default 10 hours
    }

    // Calculate time difference
    $exit_time = now();
    $entry_time = $parking_ticket->entry_time;

    // Calculate total minutes parked
    $total_minutes = $entry_time->diffInMinutes($exit_time);

    // Calculate slot multiplier (ceil used_time / slot_duration)
    $slot_multiplier = ceil($total_minutes / $slotDurationMinutes);
    if ($slot_multiplier < 1) {
      $slot_multiplier = 1;
    }

    // Calculate total hours for display
    $total_hours = ceil($total_minutes / 60);

    // Calculate total and extra amounts
    $total_amount = $basePrice * $slot_multiplier;
    $extra_amount = $total_amount - $paidAmount;

    if ($extra_amount <= 0) {
      // No extra payment needed - complete checkout
      $parking_ticket->update([
        'status' => 'checked_out',
        'exit_time' => $exit_time,
        'total_minutes' => $total_minutes,
        'total_hours' => $total_hours,
        'slot_multiplier' => $slot_multiplier,
        'total_amount' => $total_amount,
        'extra_amount' => 0,
      ]);

      $message = "Check-out successful. Slots used: {$slot_multiplier}. Total: " . number_format($total_amount, 2) . " Tk";
      return redirect()->route('parking_tickets.receipt', $ticket_number)->with('flash_success', $message);
    }

    // Extra payment required - redirect to extra payment page
    return redirect()->route('parking_tickets.extra_payment', [
      'ticket_number' => $ticket_number,
      'slot_multiplier' => $slot_multiplier,
      'total_amount' => $total_amount,
      'extra_amount' => $extra_amount,
      'total_minutes' => $total_minutes,
      'total_hours' => $total_hours,
    ]);
  }

  /**
   * Show extra payment page for overstay
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function extraPayment($ticket_number, Request $request)
  {
    $parking_ticket = ParkingTicket::with(['creator', 'vehicle'])->findOrFail($ticket_number);

    // Validate checkout data
    $slot_multiplier = $request->input('slot_multiplier', 1);
    $total_amount = $request->input('total_amount', $parking_ticket->base_price);
    $extra_amount = $request->input('extra_amount', 0);
    $total_minutes = $request->input('total_minutes', 0);
    $total_hours = $request->input('total_hours', 0);

    return view('admin.parking_tickets.extra_payment', compact(
      'parking_ticket',
      'slot_multiplier',
      'total_amount',
      'extra_amount',
      'total_minutes',
      'total_hours'
    ));
  }

  /**
   * Process extra payment and complete checkout
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function processExtraPayment($ticket_number, Request $request)
  {
    $user = Auth::user();
    $parking_ticket = ParkingTicket::findOrFail($ticket_number);

    // Validate extra payment data
    $validator = Validator::make($request->all(), [
      'slot_multiplier' => 'required|integer|min:1',
      'total_amount' => 'required|numeric|min:0',
      'extra_amount' => 'required|numeric|min:0',
      'total_minutes' => 'required|integer|min:0',
      'total_hours' => 'required|integer|min:0',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $paidAmount = $parking_ticket->paid_amount + $request->extra_amount;

    // Complete checkout with extra payment
    $parking_ticket->update([
      'status' => 'checked_out',
      'exit_time' => now(),
      'total_minutes' => $request->total_minutes,
      'total_hours' => $request->total_hours,
      'slot_multiplier' => $request->slot_multiplier,
      'total_amount' => $request->total_amount,
      'extra_amount' => $request->extra_amount,
      'paid_amount' => $paidAmount,
    ]);

    // Create extra payment record for cash handover tracking
    \App\Models\ParkingTicketPayment::create([
      'parking_ticket_number' => $parking_ticket->ticket_number,
      'payment_type' => 'extra',
      'amount' => $request->extra_amount,
      'payment_date' => now()->toDateString(),
      'created_by' => $user->id,
      'parking_counter_id' => $user->parking_counter_id,
    ]);

    $message = "Extra payment collected. Slots used: {$request->slot_multiplier}. Total: " . number_format($request->total_amount, 2) . " Tk";
    return redirect()->route('parking_tickets.receipt', $ticket_number)->with('flash_success', $message);
  }

  /**
   * Convert time string (HH:MM:SS) to minutes
   *
   * @param string $time
   * @return int
   */
  private function timeToMinutes($time)
  {
    $parts = explode(':', $time);
    $hours = isset($parts[0]) ? (int)$parts[0] : 0;
    $minutes = isset($parts[1]) ? (int)$parts[1] : 0;
    return ($hours * 60) + $minutes;
  }

  /**
   * Show receipt after checkout
   *
   * @param  string  $ticket_number
   * @return \Illuminate\Http\Response
   */
  public function receipt($ticket_number)
  {
    $parking_ticket = ParkingTicket::with(['creator', 'vehicle'])->findOrFail($ticket_number);

    // Only show receipt for checked out tickets
    if ($parking_ticket->status != 'checked_out') {
      return redirect()->route('parking_tickets.show', $ticket_number)
        ->with('flash_error', 'Receipt is only available after checkout.');
    }

    return view('admin.parking_tickets.receipt', compact('parking_ticket'));
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
    if (!$permission) {
      return false;
    }

    return RolePermission::where('role_id', $role_id)
      ->where('permission_id', $permission->id)
      ->exists();
  }
}
