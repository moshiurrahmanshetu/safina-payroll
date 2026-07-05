<?php

namespace App\Http\Controllers;

use App\Models\LockerGearCashHandover;
use App\Models\LockerGearCounter;
use App\Services\LockerGearCashHandoverCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LockerGearCashHandoverController extends Controller
{
    protected $calculationService;

    public function __construct(LockerGearCashHandoverCalculation $calculationService)
    {
        parent::__construct();
        $this->calculationService = $calculationService;
    }

    /**
     * Display cash handover dashboard for locker & gear tickets.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $businessDate = $request->get('business_date', now()->format('Y-m-d'));

        // Calculate counter-wise balance (no counter assignment check)
        $balanceData = $this->calculationService->calculateCounterWiseBalance(
            $user->id,
            $businessDate
        );

        return view('admin.locker_gear_cash_handovers.index', compact('balanceData', 'businessDate'));
    }

    /**
     * Store a newly created cash handover request.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $businessDate = $request->business_date ?? now()->format('Y-m-d');
        $lockerGearCounterId = $request->locker_gear_counter_id;

        // Validation: locker_gear_counter_id is required
        if (!$lockerGearCounterId) {
            return redirect()->back()->with('flash_error', 'Locker Gear Counter ID is required');
        }

        // Calculate available balance for specific counter
        $balanceData = $this->calculationService->calculateAvailableBalance(
            $user->id,
            $lockerGearCounterId,
            $businessDate
        );

        $availableBalance = $balanceData['available_balance'];

        // Validation
        if ($availableBalance <= 0) {
            return redirect()->back()->with('flash_error', 'No available balance to handover for this counter');
        }

        // Check if pending handover already exists for this counter
        $existingPending = LockerGearCashHandover::where('user_id', $user->id)
            ->where('locker_gear_counter_id', $lockerGearCounterId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->exists();

        if ($existingPending) {
            return redirect()->back()->with('flash_error', 'Pending handover already exists for this counter. Please wait for approval or rejection.');
        }

        // Validate amount equals full available balance (no partial handovers)
        if ($request->amount != $availableBalance) {
            return redirect()->back()->with('flash_error', 'Handover amount must equal full available balance: ' . $availableBalance);
        }

        // Create handover request
        LockerGearCashHandover::create([
            'user_id' => $user->id,
            'locker_gear_counter_id' => $lockerGearCounterId,
            'amount' => $availableBalance,
            'status' => 'pending',
            'business_date' => $businessDate,
            'requested_at' => now(),
        ]);

        return redirect()->route('locker_gear_cash_handovers.index', ['business_date' => $businessDate])
            ->with('flash_success', 'Cash handover request created successfully');
    }

    /**
     * Display approval dashboard for locker & gear cash handovers.
     *
     * @return \Illuminate\Http\Response
     */
    public function approval(Request $request)
    {
        $user = Auth::user();

        // Build query with filters
        $query = LockerGearCashHandover::with(['user', 'counter']);

        // Filter by date
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('business_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('business_date', '<=', $request->to_date);
        }

        // Filter by counter
        if ($request->has('locker_gear_counter_id') && $request->locker_gear_counter_id) {
            $query->where('locker_gear_counter_id', $request->locker_gear_counter_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Get users from the filtered handover records (before user filter is applied)
        $users = (clone $query)
            ->select('user_id')
            ->distinct()
            ->with('user')
            ->get()
            ->pluck('user.name', 'user.id')
            ->filter();

        // Filter by user (applied after building dropdown)
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $handovers = $query->orderBy('requested_at', 'desc')->get();
        $counters = \App\Models\LockerGearCounter::where('status', 1)->pluck('name', 'id');

        return view('admin.locker_gear_cash_handovers.approval', compact('handovers', 'users', 'counters'));
    }

    /**
     * Approve a cash handover request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $user = Auth::user();

        $handover = LockerGearCashHandover::findOrFail($id);

        // Validation: Only pending records can be approved
        if ($handover->status !== 'pending') {
            return redirect()->back()->with('flash_error', 'Only pending handovers can be approved');
        }

        // Update handover
        $handover->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'receiver_user_id' => $user->id,
        ]);

        return redirect()->back()->with('flash_success', 'Cash handover approved successfully');
    }

    /**
     * Reject a cash handover request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        $handover = LockerGearCashHandover::findOrFail($id);

        // Validation: Only pending records can be rejected
        if ($handover->status !== 'pending') {
            return redirect()->back()->with('flash_error', 'Only pending handovers can be rejected');
        }

        // Update handover
        $handover->update([
            'status' => 'rejected',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'remark' => $request->remark,
        ]);

        return redirect()->back()->with('flash_success', 'Cash handover rejected successfully');
    }
}
