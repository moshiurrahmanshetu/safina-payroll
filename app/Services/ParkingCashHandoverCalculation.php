<?php

namespace App\Services;

use App\Models\ParkingTicketPayment;
use App\Models\ParkingCashHandover;
use App\Models\ParkingCounter;

class ParkingCashHandoverCalculation
{
    /**
     * Calculate counter-wise available handover balance for parking tickets.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param int $userId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateCounterWiseBalance(int $userId, ?string $businessDate = null): array
    {
        $businessDate = $businessDate ?? now()->format('Y-m-d');

        // Calculate sales by counter (uses parking_ticket_payments table)
        $salesByCounter = ParkingTicketPayment::where('created_by', $userId)
            ->whereDate('payment_date', $businessDate)
            ->selectRaw('parking_counter_id, SUM(amount) as total_sales')
            ->groupBy('parking_counter_id')
            ->get()
            ->keyBy('parking_counter_id');

        // Calculate pending handovers by counter
        $pendingByCounter = ParkingCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('parking_counter_id, SUM(amount) as pending_amount')
            ->groupBy('parking_counter_id')
            ->get()
            ->keyBy('parking_counter_id');

        // Calculate approved handovers by counter
        $approvedByCounter = ParkingCashHandover::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('parking_counter_id, SUM(amount) as approved_amount')
            ->groupBy('parking_counter_id')
            ->get()
            ->keyBy('parking_counter_id');

        // Get all unique counter IDs from sales and handovers
        $allCounterIds = $salesByCounter->keys()
            ->merge($pendingByCounter->keys())
            ->merge($approvedByCounter->keys())
            ->unique();

        // Get counter names
        $counters = ParkingCounter::whereIn('id', $allCounterIds)
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
        $pendingHandovers = ParkingCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->with('counter')
            ->get();

        return [
            'counter_wise_balance' => $counterWiseBalance,
            'pending_handovers' => $pendingHandovers,
            'has_pending_handover' => $pendingHandovers->isNotEmpty(),
        ];
    }

    /**
     * Calculate available handover balance for parking tickets (legacy method for backward compatibility).
     *
     * @param int $userId
     * @param int $parkingCounterId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateAvailableBalance(int $userId, int $parkingCounterId, ?string $businessDate = null): array
    {
        // Calculate total cash collected for the user/counter/date
        // Uses parking_ticket_payments table to track daily cash collection
        $totalSalesQuery = ParkingTicketPayment::where('created_by', $userId)
            ->where('parking_counter_id', $parkingCounterId);

        if ($businessDate) {
            $totalSalesQuery->whereDate('payment_date', $businessDate);
        }

        $totalSales = $totalSalesQuery->sum('amount');

        // Calculate pending handovers
        $pendingHandoversQuery = ParkingCashHandover::where('user_id', $userId)
            ->where('parking_counter_id', $parkingCounterId)
            ->where('status', 'pending');

        if ($businessDate) {
            $pendingHandoversQuery->whereDate('business_date', $businessDate);
        }

        $pendingHandovers = $pendingHandoversQuery->sum('amount');

        // Calculate approved handovers
        $approvedHandoversQuery = ParkingCashHandover::where('user_id', $userId)
            ->where('parking_counter_id', $parkingCounterId)
            ->where('status', 'approved');

        if ($businessDate) {
            $approvedHandoversQuery->whereDate('business_date', $businessDate);
        }

        $approvedHandovers = $approvedHandoversQuery->sum('amount');

        // Calculate available balance
        // Available Balance = Total Cash Collected - Pending Handovers - Approved Handovers
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
