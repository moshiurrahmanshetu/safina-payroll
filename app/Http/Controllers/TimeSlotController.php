<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeSlotController extends Controller
{
  public function index()
  {
    $time_slots = TimeSlot::with('service')->orderBy('id', 'desc')->get();
    return view('admin.time_slots.index', compact('time_slots'));
  }

  public function create()
  {
    $categories = ServiceCategory::where('status', 1)->pluck('name', 'id');
    $services = Service::where('status', 1)->get(['id', 'name', 'service_category_id']);
    $serviceCategories = $services->pluck('service_category_id', 'id')->toArray();
    $services = $services->pluck('name', 'id');
    return view('admin.time_slots.create', compact('categories', 'services', 'serviceCategories'));
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'service_ids' => 'required|array|min:1',
      'service_ids.*' => 'required|integer|exists:services,id',
      'name' => 'required|string|max:255',
      'start_time' => 'required',
      'end_time' => 'required',
      'price' => 'required|numeric|min:0',
      'status' => 'required'
    ]);

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $serviceIds = $request->service_ids;
    $createdCount = 0;
    $skippedCount = 0;

    foreach ($serviceIds as $serviceId) {
      // Check if slot already exists for this service with same name and time
      $existingSlot = TimeSlot::where('service_id', $serviceId)
        ->where('name', $request->name)
        ->where('start_time', $request->start_time)
        ->where('end_time', $request->end_time)
        ->first();

      if ($existingSlot) {
        $skippedCount++;
        continue;
      }

      // Create slot for this service
      TimeSlot::create([
        'service_id' => $serviceId,
        'name' => $request->name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'price' => $request->price,
        'status' => $request->status
      ]);

      $createdCount++;
    }

    $message = "Time slot created successfully for {$createdCount} service(s)";
    if ($skippedCount > 0) {
      $message .= ". Skipped {$skippedCount} duplicate(s).";
    }

    return redirect()->route('time-slots.index')->with('flash_success', $message);
  }

  public function edit($id)
  {
    $time_slot = TimeSlot::findOrFail($id);
    $services = Service::where('status', 1)->pluck('name', 'id');
    return view('admin.time_slots.edit', compact('time_slot', 'services'));
  }

  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'service_id' => 'required',
      'name' => 'required|string|max:255',
      'start_time' => 'required',
      'end_time' => 'required',
      'price' => 'required|numeric|min:0',
      'status' => 'required'
    ]);

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Note: end_time can be before start_time for overnight slots (e.g., 20:00 to 08:00 next day)
    $time_slot = TimeSlot::findOrFail($id);
    $time_slot->update($request->all());

    return redirect()->route('time-slots.index')->with('flash_success', 'Time slot updated successfully');
  }

  public function destroy($id)
  {
    $time_slot = TimeSlot::findOrFail($id);
    $time_slot->delete();

    return redirect()->route('time-slots.index')->with('flash_success', 'Time slot deleted successfully');
  }

  public function getSlotsByService(Request $request, $service_id)
  {
    $date = $request->get('date');
    $excludeBookingId = $request->get('exclude_bookingId');

    // Get all active slots for this service
    $slots = TimeSlot::where('service_id', $service_id)
      ->where('status', 1)
      ->orderBy('start_time', 'asc')
      ->get(['id', 'name', 'start_time', 'end_time', 'price']);

    // If date provided, filter slots based on resource availability
    if($date){
      // Convert date format from DD-MM-YYYY to YYYY-MM-DD
      try {
        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
      } catch(\Exception $e) {
        // If conversion fails, try Y-m-d format (already in correct format)
        try {
          $date = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        } catch(\Exception $e2) {
          // Keep original date if both conversions fail
        }
      }

      // Get existing bookings with their slots for this service and date
      $existingBookings = \App\Models\Booking::where('service_id', $service_id)
        ->where('check_in_date', $date)
        ->whereIn('status', [0, 1]) // Pending and Confirmed
        ->whereNotNull('time_slot_id')
        ->when($excludeBookingId, function($query) use ($excludeBookingId) {
          $query->where('id', '!=', $excludeBookingId);
        })
        ->with('timeSlot')
        ->get();

      // Filter slots - show only if service is available (no overlapping bookings)
      $slots = $slots->filter(function($slot) use ($existingBookings) {
        // Normalize slot times (handle overnight)
        $slotTimes = $this->normalizeTimeRange($slot->start_time, $slot->end_time);

        // Find bookings that overlap with this slot's time range
        $overlappingBookings = $existingBookings->filter(function($booking) use ($slotTimes) {
          if(!$booking->timeSlot) return false;

          // Normalize existing booking slot times
          $bookTimes = $this->normalizeTimeRange($booking->timeSlot->start_time, $booking->timeSlot->end_time);

          // Check overlap: slot_start < booking_end AND slot_end > booking_start
          return ($slotTimes['start'] < $bookTimes['end'] && $slotTimes['end'] > $bookTimes['start']);
        });

        // Service is available if no bookings overlap
        return $overlappingBookings->count() == 0;
      })->values(); // Reset array keys
    }

    return response()->json($slots);
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
}
