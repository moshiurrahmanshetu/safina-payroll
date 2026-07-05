<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LockerGearTicket;
use App\Models\LockerGearTicketItem;
use App\Models\LockerItem;
use App\Models\GearItem;
use App\Models\ItemPricing;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\LockerGearCounter;
use Carbon\Carbon;

class LockerGearReportController extends Controller
{
    /**
     * Check if role has specific permission
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

    /**
     * Dashboard with summary statistics
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        // Check if user can view all or only their counter's data
        $canViewAll = $this->hasPermission($user->role_id, 'view_all');

        // Get user's counter IDs
        $userCounterIds = $user->lockerGearCounters()->pluck('locker_gear_counters.id')->toArray();

        // Base query with counter filtering
        $baseQuery = LockerGearTicket::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        if (!$canViewAll && !empty($userCounterIds)) {
            $baseQuery->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $baseQuery->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }

        // Dashboard statistics
        $totalTickets = $baseQuery->clone()->count();
        $totalRevenue = $baseQuery->clone()->sum('total_amount');
        $totalExtra = $baseQuery->clone()->sum('extra_amount');

        // Active rentals (no date filter for active)
        $activeQuery = LockerGearTicket::where('status', 'checked_in');
        if (!$canViewAll && !empty($userCounterIds)) {
            $activeQuery->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        $activeRentals = $activeQuery->count();

        // Completed rentals
        $completedQuery = LockerGearTicket::where('status', 'checked_out')
            ->whereBetween('exit_time', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        if (!$canViewAll && !empty($userCounterIds)) {
            $completedQuery->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        $completedRentals = $completedQuery->count();

        // Recent tickets
        $recentQuery = LockerGearTicket::with(['items.locker', 'items.gear', 'creator', 'lockerGearCounter']);
        if (!$canViewAll && !empty($userCounterIds)) {
            $recentQuery->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        $recentTickets = $recentQuery->orderBy('created_at', 'desc')->limit(10)->get();

        // Get counters for filter (only locker gear counters)
        $counters = LockerGearCounter::where('status', 'active')->pluck('name', 'id')->toArray();

        return view('admin.locker_gear_reports.dashboard', compact(
            'totalTickets', 'totalRevenue', 'totalExtra', 'activeRentals', 'completedRentals',
            'recentTickets', 'fromDate', 'toDate', 'counters'
        ));
    }

    /**
     * Item usage report
     */
    public function itemReport(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        // Check permissions
        $canViewAll = $this->hasPermission($user->role_id, 'view_all');
        $userCounterIds = $user->lockerGearCounters()->pluck('locker_gear_counters.id')->toArray();

        // Get ticket IDs in date range with counter filtering
        $ticketQuery = LockerGearTicket::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        if (!$canViewAll && !empty($userCounterIds)) {
            $ticketQuery->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $ticketQuery->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }
        $ticketNumbers = $ticketQuery->pluck('ticket_number')->toArray();

        // Group by item type and item_id
        $itemStats = LockerGearTicketItem::whereIn('ticket_number', $ticketNumbers)
            ->select('item_type', 'item_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(*) as rental_count'))
            ->groupBy('item_type', 'item_id')
            ->get();

        // Enrich with item details and calculate revenue
        $report = $itemStats->map(function ($stat) use ($ticketNumbers) {
            $item = null;
            $pricing = null;

            if ($stat->item_type === 'locker') {
                $item = LockerItem::find($stat->item_id);
                // Use global locker pricing (item_id = NULL)
                $pricing = ItemPricing::where('item_type', 'locker')->whereNull('item_id')->first();
            } else {
                $item = GearItem::find($stat->item_id);
                $pricing = ItemPricing::where('item_type', 'gear')->where('item_id', $stat->item_id)->first();
            }

            // Calculate revenue from tickets
            $revenue = LockerGearTicket::whereIn('ticket_number', $ticketNumbers)
                ->whereHas('items', function ($q) use ($stat) {
                    $q->where('item_type', $stat->item_type)->where('item_id', $stat->item_id);
                })
                ->sum('total_amount');

            return [
                'item_type' => $stat->item_type,
                'item_id' => $stat->item_id,
                'item_name' => $item ? $item->name : 'Unknown',
                'total_rented' => $stat->rental_count,
                'total_quantity' => $stat->total_quantity,
                'base_price' => $pricing ? $pricing->base_price : 0,
                'total_revenue' => $revenue,
            ];
        });

        // Get counters for filter (only locker gear counters)
        $counters = LockerGearCounter::where('status', 'active')->pluck('name', 'id')->toArray();

        return view('admin.locker_gear_reports.item_report', compact('report', 'fromDate', 'toDate', 'counters'));
    }

    /**
     * Stock report
     */
    public function stockReport()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        // Locker statistics
        $lockerStats = [
            'total' => LockerItem::count(),
            'available' => LockerItem::where('status', 'available')->count(),
            'occupied' => LockerItem::where('status', 'occupied')->count(),
        ];

        // Gear statistics
        $gearItems = GearItem::all()->map(function ($gear) {
            return [
                'id' => $gear->id,
                'name' => $gear->name,
                'total_stock' => $gear->total_stock,
                'available_stock' => $gear->available_stock,
                'in_use' => $gear->total_stock - $gear->available_stock,
            ];
        });

        $gearStats = [
            'total_items' => $gearItems->count(),
            'total_stock' => $gearItems->sum('total_stock'),
            'total_available' => $gearItems->sum('available_stock'),
            'total_in_use' => $gearItems->sum('in_use'),
        ];

        return view('admin.locker_gear_reports.stock_report', compact('lockerStats', 'gearItems', 'gearStats'));
    }

    /**
     * User report
     */
    public function userReport(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        // Check permissions
        $canViewAll = $this->hasPermission($user->role_id, 'view_all');
        $userCounterIds = $user->lockerGearCounters()->pluck('locker_gear_counters.id')->toArray();

        $query = LockerGearTicket::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        if (!$canViewAll && !empty($userCounterIds)) {
            $query->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $query->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }

        $report = $query->select('created_by', DB::raw('COUNT(*) as total_tickets'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('created_by')
            ->with('creator')
            ->get();

        // Get counters for filter (only locker gear counters)
        $counters = LockerGearCounter::where('status', 'active')->pluck('name', 'id')->toArray();

        return view('admin.locker_gear_reports.user_report', compact('report', 'fromDate', 'toDate', 'counters'));
    }

    /**
     * Active rentals report
     */
    public function activeRentals(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        // Check permissions
        $canViewAll = $this->hasPermission($user->role_id, 'view_all');
        $userCounterIds = $user->lockerGearCounters()->pluck('locker_gear_counters.id')->toArray();

        $query = LockerGearTicket::with(['items.locker', 'items.gear', 'creator', 'lockerGearCounter'])
            ->where('status', 'checked_in');

        if (!$canViewAll && !empty($userCounterIds)) {
            $query->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $query->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }

        $activeRentals = $query
            ->orderBy('entry_time', 'desc')
            ->get();

        // Get counters for filter (only locker gear counters)
        $counters = LockerGearCounter::where('status', 'active')->pluck('name', 'id')->toArray();

        return view('admin.locker_gear_reports.active_rentals', compact('activeRentals', 'counters'));
    }

    /**
     * Overdue report
     */
    public function overdueReport(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        // Check permissions
        $canViewAll = $this->hasPermission($user->role_id, 'view_all');
        $userCounterIds = $user->lockerGearCounters()->pluck('locker_gear_counters.id')->toArray();

        $now = Carbon::now();

        // Get all active rentals with counter filtering
        $query = LockerGearTicket::with(['items.locker', 'items.gear', 'creator', 'lockerGearCounter'])
            ->where('status', 'checked_in');

        if (!$canViewAll && !empty($userCounterIds)) {
            $query->whereIn('locker_gear_counter_id', $userCounterIds);
        }
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $query->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }

        $activeTickets = $query->get();

        $overdueItems = [];

        foreach ($activeTickets as $ticket) {
            $entryTime = Carbon::parse($ticket->entry_time);

            foreach ($ticket->items as $item) {
                // Get pricing: global for locker, per-item for gear
                $pricingQuery = ItemPricing::where('item_type', $item->item_type);
                if ($item->item_type === 'locker') {
                    $pricingQuery->whereNull('item_id');
                } else {
                    $pricingQuery->where('item_id', $item->item_id);
                }
                $pricing = $pricingQuery->first();

                if ($pricing) {
                    $durationMinutes = $pricing->duration_minutes;
                    $expectedReturnTime = $entryTime->copy()->addMinutes($durationMinutes);

                    if ($now->greaterThan($expectedReturnTime)) {
                        $overtimeMinutes = $now->diffInMinutes($expectedReturnTime);
                        $extraUnits = ceil($overtimeMinutes / $pricing->extra_unit_minutes);
                        $expectedExtra = $extraUnits * $pricing->extra_unit_price * $item->quantity;

                        $overdueItems[] = [
                            'ticket' => $ticket,
                            'item' => $item,
                            'item_name' => $item->item ? $item->item->name : 'Unknown',
                            'entry_time' => $entryTime,
                            'duration_minutes' => $durationMinutes,
                            'expected_return' => $expectedReturnTime,
                            'overtime_minutes' => $overtimeMinutes,
                            'expected_extra' => $expectedExtra,
                        ];
                    }
                }
            }
        }

        // Sort by overtime (most overdue first)
        usort($overdueItems, function ($a, $b) {
            return $b['overtime_minutes'] - $a['overtime_minutes'];
        });

        // Get counters for filter (only locker gear counters)
        $counters = LockerGearCounter::where('status', 'active')->pluck('name', 'id')->toArray();

        return view('admin.locker_gear_reports.overdue', compact('overdueItems', 'counters'));
    }
}
