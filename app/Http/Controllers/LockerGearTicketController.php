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
use App\Models\Permission;
use App\Models\RolePermission;

class LockerGearTicketController extends Controller
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
     * Generate unique ticket number
     */
    private function generateTicketNumber()
    {
        $prefix = 'LG';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $date . $random;
    }

    /**
     * Generate unique QR code
     */
    private function generateQrCode()
    {
        return 'LGQR' . now()->format('YmdHis') . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        // Check if user can view all tickets or only their counter's tickets
        $canViewAll = $this->hasPermission($user->role_id, 'view_all');

        $query = LockerGearTicket::with(['items.locker','items.gear','creator', 'lockerGearCounter']);

        if (!$canViewAll) {
            // Get user's assigned locker gear counter IDs
            $userCounterIds = $user->lockerGearCounters()->pluck('locker_gear_counters.id')->toArray();
            $query->whereIn('locker_gear_counter_id', $userCounterIds);
        }

        // Apply counter filter
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $query->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }

        // Apply status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Apply date filter
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(30);

        // Get counters for filter dropdown (only locker gear counters)
        $counters = \App\Models\LockerGearCounter::where('status', 'active')->pluck('name', 'id')->toArray();

        return view('admin.locker_gear_tickets.index', compact('tickets', 'counters'));
    }

    /**
     * Show form to create new ticket
     */
    public function create()
    {
        $user = Auth::user();
        $userCounter = $user->lockerGearCounters()->first();
        $counterName = $userCounter->name ?? 'N/A';

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        // Get available lockers
        $lockers = LockerItem::where('status', 'available')
            ->pluck('name', 'id')
            ->toArray();

        // Get available gear items (with stock)
        $gears = GearItem::where('available_stock', '>', 0)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->name . ' (Stock: ' . $item->available_stock . ')'];
            })
            ->toArray();

        return view('admin.locker_gear_tickets.create', compact('lockers', 'gears', 'counterName'));
    }

    /**
     * Store new ticket
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        // Get user's assigned locker gear counter
        $userCounter = $user->lockerGearCounters()->first();

        if (!$userCounter) {
            return redirect()->back()
                ->with('flash_error', 'User has no assigned Locker & Gear counter. Please contact administrator.')
                ->withInput();
        }

        // Validation - locker is now optional
        $request->validate([
            'locker_id' => 'nullable|exists:locker_items,id',
            'gear_items' => 'nullable|array',
            'gear_items.*.id' => 'exists:gear_items,id',
            'gear_items.*.quantity' => 'integer|min:1',
        ]);

        // Ensure at least one item is selected (locker or gear)
        $hasLocker = $request->filled('locker_id');
        $hasGear = $request->has('gear_items') && !empty(array_filter($request->gear_items, function($item) {
            return !empty($item['id']);
        }));

        if (!$hasLocker && !$hasGear) {
            return redirect()->back()
                ->with('flash_error', 'Please select at least one item: a locker or gear items.')
                ->withInput();
        }

        // Validate locker if selected
        $locker = null;
        if ($hasLocker) {
            $locker = LockerItem::find($request->locker_id);
            if (!$locker || $locker->status !== 'available') {
                return redirect()->back()
                    ->with('flash_error', 'Selected locker is not available.')
                    ->withInput();
            }
        }

        // Validate gear stock
        $gearItems = [];
        $totalAmount = 0;

        if ($request->has('gear_items')) {
            foreach ($request->gear_items as $gearData) {
                if (!isset($gearData['id']) || !isset($gearData['quantity'])) {
                    continue;
                }

                $gear = GearItem::find($gearData['id']);
                if (!$gear) {
                    continue;
                }

                if ($gear->available_stock < $gearData['quantity']) {
                    return redirect()->back()
                        ->with('flash_error', 'Insufficient stock for ' . $gear->name . '. Available: ' . $gear->available_stock)
                        ->withInput();
                }

                // Get pricing
                $pricing = ItemPricing::where('item_type', 'gear')
                    ->where('item_id', $gear->id)
                    ->first();

                $gearItems[] = [
                    'item' => $gear,
                    'quantity' => $gearData['quantity'],
                    'pricing' => $pricing,
                ];

                if ($pricing) {
                    $totalAmount += $pricing->base_price * $gearData['quantity'];
                }
            }
        }

        // Get global locker pricing (item_id = NULL) if locker selected
        if ($hasLocker) {
            $lockerPricing = ItemPricing::where('item_type', 'locker')
                ->whereNull('item_id')
                ->first();

            if ($lockerPricing) {
                $totalAmount += $lockerPricing->base_price;
            }
        }

        DB::beginTransaction();

        try {
            // Create ticket
            $ticket = LockerGearTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'qr_code' => $this->generateQrCode(),
                'status' => 'checked_in',
                'entry_time' => now(),
                'total_amount' => $totalAmount,
                'extra_amount' => 0,
                'created_by' => $user->id,
                'locker_gear_counter_id' => $userCounter->id,
            ]);

            // Add locker item if selected
            if ($hasLocker) {
                LockerGearTicketItem::create([
                    'ticket_number' => $ticket->ticket_number,
                    'item_type' => 'locker',
                    'item_id' => $locker->id,
                    'quantity' => 1,
                ]);

                // Mark locker as occupied
                $locker->update(['status' => 'occupied']);
            }

            // Add gear items and reduce stock
            foreach ($gearItems as $gearData) {
                LockerGearTicketItem::create([
                    'ticket_number' => $ticket->ticket_number,
                    'item_type' => 'gear',
                    'item_id' => $gearData['item']->id,
                    'quantity' => $gearData['quantity'],
                ]);

                // Reduce stock
                $gearData['item']->update([
                    'available_stock' => $gearData['item']->available_stock - $gearData['quantity']
                ]);
            }

            DB::commit();

            return redirect()->route('locker_gear_tickets.show', $ticket->ticket_number)
                ->with('flash_success', 'Locker & Gear ticket created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('flash_error', 'Failed to create ticket: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified ticket
     */
    public function show($ticket_number)
    {
        $ticket = LockerGearTicket::with(['items.locker', 'items.gear', 'creator'])->where('ticket_number', $ticket_number)->firstOrFail();
        return view('admin.locker_gear_tickets.show', compact('ticket'));
    }

    /**
     * Show camera scanner
     */
    public function scanCamera()
    {
        if (!$this->hasPermission(Auth::user()->role_id, 'scanCamera')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.locker_gear_tickets.scan_camera');
    }

    /**
     * Show scanned ticket details
     */
    public function scan($ticket_number)
    {
        $ticket = LockerGearTicket::with(['items.locker', 'items.gear', 'creator'])->where('ticket_number', $ticket_number)->firstOrFail();
        return view('admin.locker_gear_tickets.scan', compact('ticket'));
    }

    /**
     * Check out - Calculate billing and release items
     */
    public function checkOut($ticket_number)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'checkOut')) {
            abort(403, 'Unauthorized access');
        }

        $ticket = LockerGearTicket::where('ticket_number', $ticket_number)->firstOrFail();

        if ($ticket->status === 'checked_out') {
            return redirect()->back()->with('flash_error', 'This ticket has already been checked out.');
        }

        DB::beginTransaction();

        try {
            $extraAmount = 0;
            $exitTime = now();
            $entryTime = $ticket->entry_time;

            // Calculate overtime for each item
            foreach ($ticket->items as $item) {
                // Get pricing: global for locker, per-item for gear
                $pricingQuery = ItemPricing::where('item_type', $item->item_type);
                if ($item->item_type === 'locker') {
                    $pricingQuery->whereNull('item_id');
                } else {
                    $pricingQuery->where('item_id', $item->item_id);
                }
                $pricing = $pricingQuery->first();

                if ($pricing && $entryTime) {
                    $durationMinutes = $exitTime->diffInMinutes($entryTime);
                    $allowedMinutes = $pricing->duration_minutes;

                    if ($durationMinutes > $allowedMinutes) {
                        $overtime = $durationMinutes - $allowedMinutes;
                        $extraUnits = ceil($overtime / $pricing->extra_unit_minutes);
                        $extraAmount += $extraUnits * $pricing->extra_unit_price * $item->quantity;
                    }
                }

                // Release items
                if ($item->item_type === 'locker') {
                    $locker = LockerItem::find($item->item_id);
                    if ($locker) {
                        $locker->update(['status' => 'available']);
                    }
                } else {
                    $gear = GearItem::find($item->item_id);
                    if ($gear) {
                        $gear->update([
                            'available_stock' => $gear->available_stock + $item->quantity
                        ]);
                    }
                }
            }

            // Update ticket with extra collector tracking
            // Entry payment belongs to ticket creator (created_by)
            // Extra payment belongs to checkout operator (extra_collected_by)
            $ticketUpdate = [
                'status' => 'checked_out',
                'exit_time' => $exitTime,
                'extra_amount' => $extraAmount,
                'total_amount' => $ticket->total_amount + $extraAmount,
            ];

            // Track extra payment collector if extra charges exist
            if ($extraAmount > 0) {
                $ticketUpdate['extra_collected_by'] = $user->id;
                $ticketUpdate['extra_collected_counter_id'] = $user->lockerGearCounters()->first()?->id;
                $ticketUpdate['extra_collected_at'] = now();
            }

            $ticket->update($ticketUpdate);

            DB::commit();

            return redirect()->route('locker_gear_tickets.scan', $ticket->ticket_number)
                ->with('flash_success', 'Check-out successful. Extra charges: ' . number_format($extraAmount, 2) . ' Tk');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('flash_error', 'Failed to check out: ' . $e->getMessage());
        }
    }
}
