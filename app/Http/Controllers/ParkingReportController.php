<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ParkingTicket;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;

class ParkingReportController extends Controller
{
  /**
   * Constructor - call parent for menu_list
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('auth');
  }

  /**
   * Display parking reports with filters
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = Auth::user();
    // Check if user has 'view_all_parking_reports' permission
    $canViewAll = $this->hasPermission($user->role_id, 'view_all_parking_reports');

    // Check if force_all flag is set (from view_all_parking_reports method)
    $forceAll = $request->has('force_all') && $request->force_all === true;

    // Build query
    $query = ParkingTicket::with(['creator', 'vehicle']);

    // Permission-based filter - users without view_all permission see only own tickets
    // Unless force_all flag is set (internal call from view_all method)
    if (!$canViewAll && !$forceAll) {
      $query->where('created_by', $user->id);
    }

    // Apply date range filter
    if ($request->has('date_from') && $request->date_from) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->has('date_to') && $request->date_to) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Apply user filter (only for users with view_all permission)
    if ($canViewAll && $request->has('user_id') && $request->user_id) {
      $query->where('created_by', $request->user_id);
    }

    // Apply status filter
    if ($request->has('status') && $request->status) {
      $query->where('status', $request->status);
    }

    // Get results with pagination
    $parking_tickets = $query->orderBy('created_at', 'desc')->paginate(30);

    // Calculate summary
    $summaryQuery = ParkingTicket::query();
    if (!$canViewAll && !$forceAll) {
      $summaryQuery->where('created_by', $user->id);
    } elseif ($request->has('user_id') && $request->user_id) {
      $summaryQuery->where('created_by', $request->user_id);
    }

    // Apply same date filters to summary
    if ($request->has('date_from') && $request->date_from) {
      $summaryQuery->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->has('date_to') && $request->date_to) {
      $summaryQuery->whereDate('created_at', '<=', $request->date_to);
    }
    if ($request->has('status') && $request->status) {
      $summaryQuery->where('status', $request->status);
    }

    $total_tickets = $summaryQuery->count();
    $total_amount = $summaryQuery->sum('total_amount') ?? 0;

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

    return view('admin.parking_reports.index', compact(
      'parking_tickets',
      'total_tickets',
      'total_amount',
      'users',
      'canViewAll'
    ));
  }

  /**
   * View all parking reports (requires view_all_parking_reports permission)
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function view_all_parking_reports(Request $request)
  {
    $user = Auth::user();

    // Check permission
    if (!$this->hasPermission($user->role_id, 'view_all_parking_reports')) {
      abort(403, 'Unauthorized access');
    }

    // Set force_all flag and delegate to index()
    $request->merge(['force_all' => true]);
    return $this->index($request);
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
