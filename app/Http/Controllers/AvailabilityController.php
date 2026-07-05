<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\Booking;

class AvailabilityController extends Controller
{
  /**
   * Display availability calendar
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $year = $request->query('year', Carbon::now()->year);
    $month = $request->query('month', Carbon::now()->month);

    $currentDate = Carbon::create($year, $month, 1);
    $daysInMonth = $currentDate->daysInMonth;
    $firstDayOfMonth = $currentDate->dayOfWeek; // 0 = Sunday, 6 = Saturday

    // Get all active services
    $services = Service::where('status', 1)
      ->with(['service_category'])
      ->orderBy('name')
      ->get();

    // Get all bookings for the month (confirmed only)
    $bookings = Booking::where('status', 1)
      ->whereYear('check_in_date', $year)
      ->whereMonth('check_in_date', $month)
      ->with(['service', 'timeSlot'])
      ->get()
      ->keyBy(function($booking) {
        return $booking->service_id . '_' . $booking->check_in_date->format('Y-m-d');
      });

    // Build availability grid
    $availabilityGrid = [];
    foreach ($services as $service) {
      $serviceAvailability = [
        'service' => $service,
        'days' => []
      ];

      for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = Carbon::create($year, $month, $day)->format('Y-m-d');
        $key = $service->id . '_' . $date;

        if (isset($bookings[$key])) {
          $serviceAvailability['days'][$day] = [
            'status' => 'booked',
            'booking' => $bookings[$key]
          ];
        } else {
          $serviceAvailability['days'][$day] = [
            'status' => 'available',
            'booking' => null
          ];
        }
      }

      $availabilityGrid[] = $serviceAvailability;
    }

    // Calculate occupancy summary for today
    $today = Carbon::now()->format('Y-m-d');
    $totalServices = $services->count();
    $bookingsToday = Booking::where('status', 1)
      ->where('check_in_date', $today)
      ->count();
    $availableToday = $totalServices - $bookingsToday;
    $occupancyPercent = $totalServices > 0 ? round(($bookingsToday / $totalServices) * 100, 1) : 0;

    // Month navigation
    $previousMonth = $currentDate->copy()->subMonth();
    $nextMonth = $currentDate->copy()->addMonth();

    return view('admin.availability.index', compact(
      'availabilityGrid',
      'currentDate',
      'daysInMonth',
      'firstDayOfMonth',
      'totalServices',
      'bookingsToday',
      'availableToday',
      'occupancyPercent',
      'previousMonth',
      'nextMonth'
    ));
  }

  /**
   * Get booking details for a specific service and date (AJAX)
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function getBookingDetails(Request $request)
  {
    $serviceId = $request->query('service_id');
    $date = $request->query('date');

    if (!$serviceId || !$date) {
      return response()->json([
        'status' => false,
        'message' => 'Missing required parameters'
      ], 400);
    }

    try {
      $bookingDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'message' => 'Invalid date format'
      ], 400);
    }

    $booking = Booking::where('service_id', $serviceId)
      ->where('check_in_date', $bookingDate)
      ->where('status', 1)
      ->with(['service', 'timeSlot'])
      ->first();

    if (!$booking) {
      return response()->json([
        'status' => false,
        'message' => 'No booking found'
      ], 404);
    }

    return response()->json([
      'status' => true,
      'data' => [
        'id' => $booking->id,
        'customer_name' => $booking->name,
        'phone' => $booking->phone,
        'check_in_date' => $booking->check_in_date->format('d-m-Y'),
        'check_in_time' => $booking->check_in_time,
        'check_out_date' => $booking->check_out_date ? $booking->check_out_date->format('d-m-Y') : 'N/A',
        'check_out_time' => $booking->check_out_time,
        'time_slot' => $booking->timeSlot ? $booking->timeSlot->name : 'N/A',
        'status' => config('myhelpers.status')[$booking->status],
        'final_price' => number_format($booking->final_price, 2),
        'service_name' => $booking->service->name
      ]
    ]);
  }
}
