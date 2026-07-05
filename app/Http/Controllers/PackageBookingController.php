<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Package;
use App\Models\PackageBooking;
use App\Models\PackageBookingItem;
use App\Models\Ticket;
use App\Models\Counter;
use App\Models\Permission;
use App\Models\RolePermission;
use Validator;

class PackageBookingController extends Controller
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

    // Check permission to view all package bookings
    $canViewAll = $this->hasPermission($user->role_id, 'view_all_package_bookings');

    $query = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator']);

    // Apply visibility filtering
    if (!$canViewAll) {
      // Regular users see only bookings they created
      $query->where('created_by', $user->id);
    }

    $bookings = $query->orderBy('id', 'desc')->get();

    return view('admin.package_bookings.index', compact('bookings'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $user = Auth::user();

    // Get packages based on user access - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    $packageCounterName = $user->packageCounters()->first()->name ?? 'N/A';

    if (!empty($userPackageCounterIds)) {
      // Get all package IDs from user's package counters
      $packageIds = \App\Models\PackageCounter::whereIn('id', $userPackageCounterIds)
        ->whereHas('packages')
        ->with('packages')
        ->get()
        ->pluck('packages')
        ->flatten()
        ->pluck('id')
        ->unique()
        ->toArray();

      if (!empty($packageIds)) {
        $packages = Package::where('status', 1)
          ->whereIn('id', $packageIds)
          ->pluck('name', 'id')
          ->toArray();
      } else {
        // User's package counters have no packages assigned
        return redirect()->route('package_bookings.index')->with('flash_error', 'Your Package Counter has no packages assigned. Please contact administrator.');
      }
    } else {
      // User has no package counters assigned
      return redirect()->route('package_bookings.index')->with('flash_error', 'You must be assigned to a Package Counter to create bookings.');
    }

    return view('admin.package_bookings.create', compact('packages', 'packageCounterName'));
  }

  /**
   * Get package details with items via AJAX
   *
   * @param  int  $package_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function getPackageDetails($package_id)
  {
    $package = Package::with('items.ticket')->find($package_id);

    if (!$package) {
      return response()->json(['success' => false, 'message' => 'Package not found']);
    }

    return response()->json([
      'success' => true,
      'package' => [
        'id' => $package->id,
        'name' => $package->name,
        'base_price' => $package->base_price,
        'default_person' => $package->default_person,
        'extra_person_price' => $package->extra_person_price,
        'status' => $package->status
      ],
      'items' => $package->items->map(function($item) {
        return [
          'id' => $item->id,
          'ticket_id' => $item->service_id,
          'ticket_name' => $item->ticket ? $item->ticket->name : 'N/A'
        ];
      })
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();

    $validator = Validator::make($data, [
      'package_id'       => 'required|exists:packages,id',
      'date'             => 'required|date_format:d-m-Y',
      'quantity'         => 'required|integer|min:1',
      'extra_person'     => 'nullable|integer|min:0'
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = Auth::user();

    // Check if user has package counters assigned
    $userPackageCounters = $user->packageCounters()->where('status', 1)->get();
    if ($userPackageCounters->isEmpty()) {
      return redirect()->back()->with('flash_error', 'You must be assigned to a Package Counter to create bookings.');
    }

    // Get package details
    $package = Package::find($data['package_id']);

    // Calculate amounts
    $quantity = intval($data['quantity']);
    $extraPerson = intval($data['extra_person'] ?? 0);

    $baseAmount = $package->base_price * $quantity;
    $defaultPersonTotal = $package->default_person * $quantity;
    $totalPerson = $defaultPersonTotal + $extraPerson;
    $extraAmount = $extraPerson * $package->extra_person_price;

    $finalAmount = $baseAmount + $extraAmount;

    // Convert date format
    $bookingDate = \DateTime::createFromFormat('d-m-Y', $data['date'])->format('d-m-Y');

    // Determine package counter based on user's assigned package counters
    $packageCounterId = null;

    if ($userPackageCounters->count() === 1) {
      // User has exactly one package counter, use it
      $packageCounterId = $userPackageCounters->first()->id;
    } elseif ($userPackageCounters->count() > 1) {
      // User has multiple package counters, use the first one as default
      $packageCounterId = $userPackageCounters->first()->id;
    }

    // Generate unique QR code and booking token
    $qrCode = PackageBooking::generateQrCode();
    $bookingToken = PackageBooking::generateBookingToken();

    // Generate ticket data JSON
    $ticketData = PackageBooking::generateTicketData($package, $quantity);

    $booking = PackageBooking::create([
      'package_id'        => $package->id,
      'date'              => $bookingDate,
      'quantity'          => $quantity,
      'total_person'      => $totalPerson,
      'base_amount'       => $baseAmount,
      'extra_person'      => $extraPerson,
      'extra_amount'      => $extraAmount,
      'final_amount'      => $finalAmount,
      'qr_code'           => $qrCode,
      'booking_token'     => $bookingToken,
      'is_used'           => 0,
      'package_counter_id'=> $packageCounterId,
      'created_by'        => $user->id,
      'ticket_data'       => $ticketData
    ]);

    if ($booking) {
      // Save included package items (without creating individual ticket records)
      foreach ($package->items as $item) {
        PackageBookingItem::create([
          'package_booking_id' => $booking->id,
          'service_id'       => $item->ticket_id,
          'quantity'         => $quantity,
          'price'            => 0,
          'source'           => 'package'
        ]);
      }

      // Return to print view directly
      return redirect()->route('package_bookings.print', $booking->id);
    } else {
      return redirect()->back()->withErrors(['error' => 'Failed to create booking'])->withInput();
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
    $user = Auth::user();

    $booking = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator'])
      ->findOrFail($id);

    // Apply access restrictions - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    // User can view if they created the booking or it belongs to their package counters
    $hasAccess = $booking->created_by === $user->id;

    if (!$hasAccess && !empty($userPackageCounterIds)) {
      $hasAccess = in_array($booking->package_counter_id, $userPackageCounterIds);
    }

    if (!$hasAccess) {
      abort(403, 'You do not have permission to view this booking.');
    }

    return view('admin.package_bookings.show', compact('booking'));
  }

  /**
   * Print package booking tickets
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function print($id)
  {
    $user = Auth::user();

    $booking = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator'])
      ->findOrFail($id);

    // Apply access restrictions - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    // User can view if they created the booking or it belongs to their package counters
    $hasAccess = $booking->created_by === $user->id;

    if (!$hasAccess && !empty($userPackageCounterIds)) {
      $hasAccess = in_array($booking->package_counter_id, $userPackageCounterIds);
    }

    if (!$hasAccess) {
      abort(403, 'You do not have permission to view this booking.');
    }

    // Get tickets from ticket_data JSON
    $tickets = $booking->ticket_data['tickets'] ?? [];

    return view('admin.package_bookings.print', compact('booking', 'tickets'));
  }

  /**
   * Preview tickets for a booking
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function previewTickets($id)
  {
    $user = Auth::user();

    $booking = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator'])
      ->findOrFail($id);

    // Apply access restrictions - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    // User can view if they created the booking or it belongs to their package counters
    $hasAccess = $booking->created_by === $user->id;

    if (!$hasAccess && !empty($userPackageCounterIds)) {
      $hasAccess = in_array($booking->package_counter_id, $userPackageCounterIds);
    }

    if (!$hasAccess) {
      abort(403, 'You do not have permission to view this booking.');
    }

    // Get tickets from ticket_data JSON
    $tickets = $booking->ticket_data['tickets'] ?? [];

    return view('admin.package_bookings.preview', compact('booking', 'tickets'));
  }

  /**
   * Print tickets for a booking
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function printTickets($id)
  {
    $user = Auth::user();

    $booking = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator'])
      ->findOrFail($id);

    // Apply access restrictions - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    // User can view if they created the booking or it belongs to their package counters
    $hasAccess = $booking->created_by === $user->id;

    if (!$hasAccess && !empty($userPackageCounterIds)) {
      $hasAccess = in_array($booking->package_counter_id, $userPackageCounterIds);
    }

    if (!$hasAccess) {
      abort(403, 'You do not have permission to view this booking.');
    }

    // Mark tickets as printed
    if ($booking->ticket_status === 'draft') {
      $booking->ticket_status = 'printed';
      $booking->save();
    }

    // Get tickets from ticket_data JSON
    $tickets = $booking->ticket_data['tickets'] ?? [];

    return view('admin.package_bookings.print', compact('booking', 'tickets'));
  }

  /**
   * Show QR scan form for token-based validation
   *
   * @return \Illuminate\Http\Response
   */
  public function showScanForm()
  {
    return view('admin.package_bookings.scan');
  }

  /**
   * Scan QR code by token (new architecture)
   * URL: /package/scan/{package_token}/{ticket_token}
   *
   * @param  string  $package_token
   * @param  string  $ticket_token
   * @return \Illuminate\Http\Response
   */
  public function scanByToken($package_token, $ticket_token)
  {
    if (!$package_token || !$ticket_token) {
      return view('admin.package_bookings.scan_result', [
        'status' => 'invalid',
        'message' => 'Invalid QR Code - Missing parameters'
      ]);
    }

    // Find booking by booking_token
    $booking = PackageBooking::with(['package', 'items.ticket'])
      ->where('booking_token', $package_token)
      ->first();

    if (!$booking) {
      return view('admin.package_bookings.scan_result', [
        'status' => 'invalid',
        'message' => 'Invalid Booking Token'
      ]);
    }

    // Find ticket in ticket_data JSON
    $ticket = $booking->findTicketByToken($ticket_token);

    if (!$ticket) {
      return view('admin.package_bookings.scan_result', [
        'status' => 'invalid',
        'message' => 'Invalid Ticket Token',
        'booking' => $booking
      ]);
    }

    // Check if ticket is already used
    if (isset($ticket['is_used']) && $ticket['is_used']) {
      return view('admin.package_bookings.scan_result', [
        'status' => 'used',
        'message' => 'Ticket Already Used',
        'ticket' => $ticket,
        'booking' => $booking,
        'used_at' => isset($ticket['used_at']) && $ticket['used_at'] ? date('d-m-Y h:i A', strtotime($ticket['used_at'])) : null
      ]);
    }

    // Check if booking date is today
    $bookingDate = $booking->date->toDateString();
    $today = now()->toDateString();
    if ($bookingDate != $today) {
      return view('admin.package_bookings.scan_result', [
        'status' => 'expired',
        'message' => 'Expired Booking',
        'ticket' => $ticket,
        'booking' => $booking
      ]);
    }

    // Mark ticket as used
    $booking->markTicketAsUsed($ticket_token);

    return view('admin.package_bookings.scan_result', [
      'status' => 'success',
      'message' => 'Entry Validated Successfully',
      'ticket' => $ticket,
      'booking' => $booking
    ]);
  }

  /**
   * Validate ticket by token via POST (AJAX)
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function validateByToken(Request $request)
  {
    $bookingToken = $request->input('p');
    $ticketToken = $request->input('t');

    if (!$bookingToken || !$ticketToken) {
      return response()->json([
        'status' => 'invalid',
        'message' => 'QR code is required'
      ]);
    }

    // Find booking by booking_token
    $booking = PackageBooking::with(['package', 'items.ticket'])
      ->where('booking_token', $bookingToken)
      ->first();

    if (!$booking) {
      return response()->json([
        'status' => 'invalid',
        'message' => 'Invalid Booking Token'
      ]);
    }

    // Find ticket in ticket_data JSON
    $ticket = $booking->findTicketByToken($ticketToken);

    if (!$ticket) {
      return response()->json([
        'status' => 'invalid',
        'message' => 'Invalid Ticket Token'
      ]);
    }

    // Check if ticket is already used
    if (isset($ticket['is_used']) && $ticket['is_used']) {
      return response()->json([
        'status' => 'used',
        'message' => 'This ticket is already used',
        'used_at' => isset($ticket['used_at']) && $ticket['used_at'] ? date('d-m-Y h:i A', strtotime($ticket['used_at'])) : null
      ]);
    }

    // Check if booking date is today
    $bookingDate = $booking->date->toDateString();
    $today = now()->toDateString();
    if ($bookingDate != $today) {
      return response()->json([
        'status' => 'expired',
        'message' => 'Expired Booking'
      ]);
    }

    // Mark ticket as used
    $booking->markTicketAsUsed($ticketToken);

    return response()->json([
      'status' => 'valid',
      'message' => 'Entry Allowed',
      'package_name' => $booking->package ? $booking->package->name : 'Package',
      'ticket_name' => $ticket['ticket_name'],
      'ticket_token' => $ticket['ticket_token'],
      'booking_id' => $booking->id,
      'scanned_at' => now()->format('d-m-Y h:i A')
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = Auth::user();

    $booking = PackageBooking::findOrFail($id);

    // Apply access restrictions - ALL users must have package counter assignment
    $userPackageCounterIds = $user->packageCounters()->where('status', 1)->pluck('package_counter_id')->toArray();

    // User can delete if they created the booking or it belongs to their package counters
    $hasAccess = $booking->created_by === $user->id;

    if (!$hasAccess && !empty($userPackageCounterIds)) {
      $hasAccess = in_array($booking->package_counter_id, $userPackageCounterIds);
    }

    if (!$hasAccess) {
      abort(403, 'You do not have permission to delete this booking.');
    }

    // Delete related items first
    PackageBookingItem::where('package_booking_id', $id)->delete();

    // Delete booking
    $deleted = $booking->delete();

    if ($deleted) {
      return redirect()->back()->with('flash_success', 'Package booking deleted successfully');
    } else {
      return redirect()->back()->with('flash_error', 'Failed to delete booking');
    }
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
