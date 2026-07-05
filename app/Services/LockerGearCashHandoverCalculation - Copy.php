<?php

namespace App\Services;

use App\Models\LockerGearTicket;
use App\Models\LockerGearCashHandover;
use App\Models\LockerGearCounter;

class LockerGearCashHandoverCalculation
{
    /**
     * Calculate counter-wise available handover balance for locker & gear tickets.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param int $userId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateCounterWiseBalance(int $userId, ?string $businessDate = null): array
    {
        $businessDate = $businessDate ?? now()->format('Y-m-d');

        // Calculate sales by counter (total_amount + extra_amount)
        $salesByCounter = LockerGearTicket::where('created_by', $userId)
            ->whereDate('created_at', $businessDate)
            ->selectRaw('locker_gear_counter_id, SUM(total_amount + extra_amount) as total_sales')
            ->groupBy('locker_gear_counter_id')
            ->get()
            ->keyBy('locker_gear_counter_id');

        // Calculate pending handovers by counter
        $pendingByCounter = LockerGearCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('locker_gear_counter_id, SUM(amount) as pending_amount')
            ->groupBy('locker_gear_counter_id')
            ->get()
            ->keyBy('locker_gear_counter_id');

        // Calculate approved handovers by counter
        $approvedByCounter = LockerGearCashHandover::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('locker_gear_counter_id, SUM(amount) as approved_amount')
            ->groupBy('locker_gear_counter_id')
            ->get()
            ->keyBy('locker_gear_counter_id');

        // Get all unique counter IDs from sales and handovers
        $allCounterIds = $salesByCounter->keys()
            ->merge($pendingByCounter->keys())
            ->merge($approvedByCounter->keys())
            ->unique();

        // Get counter names
        $counters = LockerGearCounter::whereIn('id', $allCounterIds)
            ->pluck('name', 'id');

        // Build counter-wise balance data
        $counterWiseBalance = [];
        foreach ($allCounterIds as $counterId) {
            $totalSales = $salesByCounter->get($counterId)?->total_sales ?? 0;
            $pendingAmount = $pendingByCounter->get($counterId)?->pending_amount ?? 0;
            $approvedAmount = $approvedByCounter->get($counterId)?->approved_amount ?? 0;
            $availableBalance = max(0, $totalSales - $pendingAmount - $approvedAmount);

            $counterWiseBalance[] = [
                'counter_id' => $counterId,
                'counter_name' => $counters->get($counterId, 'Unknown Counter'),
                'business_date' => $businessDate,
                'total_sales' => $totalSales,
                'pending_amount' => $pendingAmount,
                'approved_amount' => $approvedAmount,
                'available_amount' => $availableBalance,
            ];
        }

        // Check if there are any pending handovers (with counter relationship eager loaded)
        $pendingHandovers = LockerGearCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->with('lockerGearCounter')
            ->get();

        return [
            'counter_wise_balance' => $counterWiseBalance,
            'pending_handovers' => $pendingHandovers,
            'has_pending_handover' => $pendingHandovers->isNotEmpty(),
        ];
    }

    /**
     * Calculate available handover balance for locker & gear tickets (legacy method for backward compatibility).
     *
     * @param int $userId
     * @param int $lockerGearCounterId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateAvailableBalance(int $userId, int $lockerGearCounterId, ?string $businessDate = null): array
    {
        // Calculate total sales for the user/counter/date
        $totalSalesQuery = LockerGearTicket::where('created_by', $userId)
            ->where('locker_gear_counter_id', $lockerGearCounterId);

        if ($businessDate) {
            $totalSalesQuery->whereDate('created_at', $businessDate);
        }

        $totalSales = $totalSalesQuery->sum('total_amount');

        // Calculate pending handovers
        $pendingHandoversQuery = LockerGearCashHandover::where('user_id', $userId)
            ->where('locker_gear_counter_id', $lockerGearCounterId)
            ->where('status', 'pending');

        if ($businessDate) {
            $pendingHandoversQuery->whereDate('business_date', $businessDate);
        }

        $pendingHandovers = $pendingHandoversQuery->sum('amount');

        // Calculate approved handovers
        $approvedHandoversQuery = LockerGearCashHandover::where('user_id', $userId)
            ->where('locker_gear_counter_id', $lockerGearCounterId)
            ->where('status', 'approved');

        if ($businessDate) {
            $approvedHandoversQuery->whereDate('business_date', $businessDate);
        }

        $approvedHandovers = $approvedHandoversQuery->sum('amount');

        // Calculate available balance
        // Available Balance = Total Sales - Pending Handovers - Approved Handovers
        // Rejected handovers do NOT reduce balance
        $availableBalance = $totalSales - $pendingHandovers - $approvedHandovers;

        return [
            'total_sales' => $totalSales,
            'pending_handovers' => $pendingHandovers,
            'approved_handovers' => $approvedHandovers,
            'available_balance' => max(0, $availableBalance),
        ];
    }
}
