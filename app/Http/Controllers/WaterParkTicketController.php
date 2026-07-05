<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WaterParkTicket;
use App\Models\WaterParkSetting;
use App\Models\WaterParkCounter;
use App\Models\Counter;
use App\Models\Permission;
use App\Models\RolePermission;

class WaterParkTicketController extends Controller
{
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

    /**
     * Display list of water park tickets
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if user has 'index' permission
        $canViewAll = $this->hasPermission($user->role_id, 'index');

        // Build query
        $query = WaterParkTicket::with(['waterParkCounter', 'creator']);

        // Permission-based filter
        if (!$canViewAll) {
            // Get user's assigned water park counter IDs
            $userCounterIds = $user->waterParkCounters()->pluck('water_park_counters.id')->toArray();
            $query->whereIn('water_park_counter_id', $userCounterIds);
        }

        // Apply counter filter
        if ($request->has('water_park_counter_id') && $request->water_park_counter_id) {
            $query->where('water_park_counter_id', $request->water_park_counter_id);
        }

        // Apply status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Apply date filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(30);

        // Get counters for filter dropdown
        $counters = [];
        if ($canViewAll) {
            $counters = WaterParkCounter::where('status', 1)->pluck('name', 'id')->toArray();
        } else {
            $counters = $user->waterParkCounters()->where('status', 1)->pluck('name', 'water_park_counters.id')->toArray();
        }

        return view('admin.water_park_tickets.index', compact(
            'tickets',
            'counters',
            'canViewAll'
        ));
    }

    /**
     * Show form to create new water park ticket
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        // Get user's assigned water park counter
        $userCounter = $user->waterParkCounters()->first();
        $counterName = $userCounter->name ?? 'N/A';

        if (!$userCounter) {
            return redirect()->route('water_park_tickets.index')
                ->with('flash_error', 'User has no assigned water park counter');
        }

        return view('admin.water_park_tickets.create', compact('counterName'));
    }

    /**
     * Store new water park ticket
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        // Validate quantity
        $request->validate([
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        // Get user's assigned water park counter
        $userCounter = $user->waterParkCounters()->first();

        if (!$userCounter) {
            return redirect()->back()
                ->with('flash_error', 'User has no assigned water park counter')
                ->withInput();
        }

        // Get water park settings (single config)
        $settings = WaterParkSetting::first();
        if (!$settings) {
            $settings = WaterParkSetting::create([
                'duration_minutes' => 120,
                'price' => 350,
                'extra_unit_minutes' => 30,
                'extra_unit_price' => 100,
            ]);
        }

        $createdTickets = [];
        $quantity = $request->quantity;

        // Loop to create multiple tickets
        for ($i = 0; $i < $quantity; $i++) {
            // Generate unique ticket number for each ticket
            $ticketNumber = $this->generateTicketNumber();

            $ticket = WaterParkTicket::create([
                'ticket_number' => $ticketNumber,
                'water_park_counter_id' => $userCounter->id,
                'price' => $settings->price,
                'duration_minutes' => $settings->duration_minutes,
                'extra_unit_minutes' => $settings->extra_unit_minutes,
                'extra_unit_price' => $settings->extra_unit_price,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            if ($ticket) {
                $createdTickets[] = $ticket->ticket_number;
            }
        }

        if (count($createdTickets) > 0) {
            // Redirect to bulk print page
            return redirect()->route('water_park_tickets.bulk_print', ['ids' => implode(',', $createdTickets)])
                ->with('flash_success', $quantity . ' Water Park Ticket(s) created successfully.');
        }

        return redirect()->back()->with('flash_error', 'Failed to create tickets');
    }

    /**
     * Display the specified ticket
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($ticket_number)
    {
        $ticket = WaterParkTicket::with(['waterParkCounter', 'creator'])->findOrFail($ticket_number);
        return view('admin.water_park_tickets.show', compact('ticket'));
    }

    /**
     * Bulk print multiple tickets
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function bulkPrint(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));

        if (empty($ids)) {
            return redirect()->route('water_park_tickets.index')
                ->with('flash_error', 'No tickets to print');
        }

        $tickets = WaterParkTicket::with(['waterParkCounter', 'creator'])
            ->whereIn('ticket_number', $ids)
            ->get();

        return view('admin.water_park_tickets.bulk_print', compact('tickets'));
    }

    /**
     * Show camera scanner for water park tickets
     *
     * @return \Illuminate\Http\Response
     */
    public function scanCamera()
    {
        if (!$this->hasPermission(Auth::user()->role_id, 'scanCamera')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.water_park_tickets.scan_camera');
    }

    /**
     * Show scanned ticket details with check-in/check-out buttons
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function scan($ticket_number)
    {
        $ticket = WaterParkTicket::with(['waterParkCounter', 'creator'])->findOrFail($ticket_number);
        return view('admin.water_park_tickets.scan', compact('ticket'));
    }

    /**
     * Check In - Record entry time
     *
     * @param string $ticket_number
     * @return \Illuminate\Http\Response
     */
    public function checkIn($ticket_number)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPermission($user->role_id, 'checkIn')) {
            abort(403, 'Unauthorized access');
        }

        $ticket = WaterParkTicket::findOrFail($ticket_number);

        // Validation: Prevent double check-in
        if ($ticket->status == 'checked_in') {
            return redirect()->back()->with('flash_error', 'This ticket is already checked in.');
        }

        if ($ticket->status == 'checked_out') {
            return redirect()->back()->with('flash_error', 'This ticket has already been checked out.');
        }

        // Update status and entry time
        $ticket->update([
            'status' => 'checked_in',
            'entry_time' => now(),
        ]);

        return redirect()->route('water_park_tickets.scan', $ticket_number)
            ->with('flash_success', 'Check-in successful. Entry time recorded.');
    }

    /**
     * Check Out - Calculate overtime and billing
     *
     * @param string $ticket_number
     * @return \Illuminate\Http\Response
     */
    public function checkOut($ticket_number)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPermission($user->role_id, 'checkOut')) {
            abort(403, 'Unauthorized access');
        }

        $ticket = WaterParkTicket::findOrFail($ticket_number);

        // Validation: Must be checked in first
        if ($ticket->status == 'pending') {
            return redirect()->back()->with('flash_error', 'This ticket has not been checked in yet.');
        }

        if ($ticket->status == 'checked_out') {
            return redirect()->back()->with('flash_error', 'This ticket has already been checked out.');
        }

        if (!$ticket->entry_time) {
            return redirect()->back()->with('flash_error', 'Entry time not found. Cannot process checkout.');
        }

        // Calculate time difference
        $exit_time = now();
        $entry_time = $ticket->entry_time;
        $actual_minutes = $exit_time->diffInMinutes($entry_time);

        // Get allowed time from ticket settings
        $allowed_minutes = $ticket->duration_minutes;
        $extra_unit_minutes = $ticket->extra_unit_minutes;
        $extra_unit_price = $ticket->extra_unit_price;

        // Calculate overtime billing
        $extra_minutes = 0;
        $extra_amount = 0;
        $has_overtime = false;

        if ($actual_minutes > $allowed_minutes) {
            $has_overtime = true;
            $overtime = $actual_minutes - $allowed_minutes;
            $extra_units = ceil($overtime / $extra_unit_minutes);
            $extra_amount = $extra_units * $extra_unit_price;
            $extra_minutes = $overtime;
        }

        // Update ticket with checkout details
        $ticket->update([
            'status' => 'checked_out',
            'exit_time' => $exit_time,
            'extra_minutes' => $extra_minutes,
            'extra_amount' => $extra_amount,
        ]);

        // Determine message based on overtime
        if ($has_overtime) {
            return redirect()->route('water_park_tickets.scan', $ticket_number)
                ->with('flash_warning', 'Check-out complete. Extra charges: ' . number_format($extra_amount, 2) . ' Tk for ' . $extra_minutes . ' extra minutes.');
        }

        return redirect()->route('water_park_tickets.scan', $ticket_number)
            ->with('flash_success', 'Check-out successful. No extra charges.');
    }

    /**
     * Generate unique ticket number
     *
     * @return string
     */
    private function generateTicketNumber()
    {
        
        $time = time();
        $random = strtoupper(substr(uniqid(), -3));
        $random2 = strtolower(substr(uniqid(), -3));
        return $random . $time . $random2;
    }

    /**
     * Generate unique QR code
     *
     * @return string
     */
    private function generateQrCode()
    {
        return 'WPQR' . now()->format('YmdHis') . strtoupper(substr(uniqid(), -6));
    }
}
