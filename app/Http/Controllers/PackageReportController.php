<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\Package;
use App\Models\PackageBooking;
use App\Models\PackageBookingItem;
use App\Models\Counter;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;

class PackageReportController extends Controller
{
  /**
   * Display the report view with filters
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = Auth::user();

    // Get filter dropdown data - Load all Package Counters for analysis
    $counters = \App\Models\PackageCounter::where('status', 1)
      ->pluck('name', 'id')
      ->toArray();

    // Get all packages for filter
    $packages = Package::where('status', 1)
      ->pluck('name', 'id')
      ->toArray();

    // Get users assigned to Package Counters
    $userIds = \App\Models\PackageCounter::where('status', 1)
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

    // Default date range (current month)
    $fromDate = date('01-m-Y');
    $toDate = date('d-m-Y');

    return view('admin.package_reports.index', compact(
      'packages',
      'counters',
      'users',
      'fromDate',
      'toDate'
    ));
  }

  /**
   * Generate report data based on filters
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function generate(Request $request)
  {
    $user = Auth::user();

    // Get filters
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');
    $packageId = $request->get('package_id');
    $counterId = $request->get('counter_id');
    $userId = $request->get('user_id');

    // Convert dates
    $fromDateFormatted = null;
    $toDateFormatted = null;

    if ($fromDate) {
      try {
        $fromDateFormatted = Carbon::createFromFormat('d-m-Y', $fromDate)->format('d-m-Y');
      } catch (\Exception $e) {}
    }

    if ($toDate) {
      try {
        $toDateFormatted = Carbon::createFromFormat('d-m-Y', $toDate)->format('d-m-Y');
      } catch (\Exception $e) {}
    }

    // Base query - load all package bookings for analysis
    $query = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator']);

    // Apply date filter
    if ($fromDateFormatted) {
      $query->where('date', '>=', $fromDateFormatted);
    }

    if ($toDateFormatted) {
      $query->where('date', '<=', $toDateFormatted);
    }

    // Apply package filter
    if ($packageId) {
      $query->where('package_id', $packageId);
    }

    // Apply counter filter (if specific counter selected)
    if ($counterId) {
      $query->where('package_counter_id', $counterId);
    }

    // Apply user filter
    if ($userId) {
      $query->where('created_by', $userId);
    }

    $bookings = $query->orderBy('date', 'desc')->get();

    // Calculate totals
    $totalBookings = $bookings->count();
    $totalRevenue = $bookings->sum('final_amount');
    $totalBaseAmount = $bookings->sum('base_amount');
    $totalExtraAmount = $bookings->sum('extra_amount');

    // Package-wise summary
    $packageSummary = $bookings->groupBy(function($booking) {
      return $booking->package ? $booking->package->name : 'Unknown';
    })->map(function($group) {
      return [
        'count' => $group->count(),
        'revenue' => $group->sum('final_amount'),
        'base_amount' => $group->sum('base_amount'),
        'extra_amount' => $group->sum('extra_amount')
      ];
    });

    // Counter-wise summary
    $counterSummary = $bookings->groupBy(function($booking) {
      return $booking->packageCounter ? $booking->packageCounter->name : 'No Counter';
    })->map(function($group) {
      return [
        'count' => $group->count(),
        'revenue' => $group->sum('final_amount')
      ];
    });

    // User-wise summary
    $userSummary = $bookings->groupBy(function($booking) {
      return $booking->creator ? $booking->creator->name : 'Unknown';
    })->map(function($group) {
      return [
        'count' => $group->count(),
        'revenue' => $group->sum('final_amount')
      ];
    });

    // Get filter dropdown data for view
    $counters = \App\Models\PackageCounter::where('status', 1)
      ->pluck('name', 'id')
      ->toArray();

    $packages = Package::where('status', 1)
      ->pluck('name', 'id')
      ->toArray();

    // Get users assigned to Package Counters
    $userIds = \App\Models\PackageCounter::where('status', 1)
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

    return view('admin.package_reports.index', compact(
      'bookings',
      'totalBookings',
      'totalRevenue',
      'totalBaseAmount',
      'totalExtraAmount',
      'packageSummary',
      'counterSummary',
      'userSummary',
      'packages',
      'counters',
      'users',
      'fromDate',
      'toDate',
      'packageId',
      'counterId',
      'userId'
    ));
  }

  /**
   * Print report
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function print(Request $request)
  {
    // Reuse generate logic
    $user = Auth::user();

    // Get filters
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');
    $packageId = $request->get('package_id');
    $counterId = $request->get('counter_id');
    $userId = $request->get('user_id');

    // Convert dates
    $fromDateFormatted = null;
    $toDateFormatted = null;

    if ($fromDate) {
      try {
        $fromDateFormatted = Carbon::createFromFormat('d-m-Y', $fromDate)->format('d-m-Y');
      } catch (\Exception $e) {}
    }

    if ($toDate) {
      try {
        $toDateFormatted = Carbon::createFromFormat('d-m-Y', $toDate)->format('d-m-Y');
      } catch (\Exception $e) {}
    }

    // Base query - load all package bookings for analysis
    $query = PackageBooking::with(['package', 'items.ticket', 'packageCounter', 'creator']);

    // Apply date filter
    if ($fromDateFormatted) {
      $query->where('date', '>=', $fromDateFormatted);
    }

    if ($toDateFormatted) {
      $query->where('date', '<=', $toDateFormatted);
    }

    if ($packageId) {
      $query->where('package_id', $packageId);
    }

    // Apply counter filter (if specific counter selected)
    if ($counterId) {
      $query->where('package_counter_id', $counterId);
    }

    if ($userId) {
      $query->where('created_by', $userId);
    }

    $bookings = $query->orderBy('date', 'desc')->get();

    // Calculate summaries
    $totalBookings = $bookings->count();
    $totalRevenue = $bookings->sum('final_amount');

    $packageSummary = $bookings->groupBy(function($booking) {
      return $booking->package ? $booking->package->name : 'Unknown';
    })->map(function($group) {
      return [
        'count' => $group->count(),
        'revenue' => $group->sum('final_amount')
      ];
    });

    $counterSummary = $bookings->groupBy(function($booking) {
      return $booking->packageCounter ? $booking->packageCounter->name : 'No Counter';
    })->map(function($group) {
      return [
        'count' => $group->count(),
        'revenue' => $group->sum('final_amount')
      ];
    });

    return view('admin.package_reports.print', compact(
      'bookings',
      'totalBookings',
      'totalRevenue',
      'packageSummary',
      'counterSummary',
      'fromDate',
      'toDate',
      'packageId',
      'counterId',
      'userId'
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
    if (!$permission) {
      return false;
    }

    return RolePermission::where('role_id', $role_id)
      ->where('permission_id', $permission->id)
      ->exists();
  }
}
