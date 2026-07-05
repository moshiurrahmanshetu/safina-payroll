<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingCashHandover;
use App\Models\Counter;

class BookingCashHandoverCalculation
{
    /**
     * Calculate counter-wise available handover balance for bookings.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param int $userId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateCounterWiseBalance(int $userId, ?string $businessDate = null): array
    {
        $businessDate = $businessDate ?? now()->format('Y-m-d');

        // Calculate sales by counter (only confirmed bookings, status = 1)
        $salesByCounter = Booking::where('created_by', $userId)
            ->where('status', 1)
            ->whereDate('date', $businessDate)
            ->selectRaw('counter_id, SUM(final_price) as total_sales')
            ->groupBy('counter_id')
            ->get()
            ->keyBy('counter_id');

        // Calculate pending handovers by counter
        $pendingByCounter = BookingCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('counter_id, SUM(amount) as pending_amount')
            ->groupBy('counter_id')
            ->get()
            ->keyBy('counter_id');

        // Calculate approved handovers by counter
        $approvedByCounter = BookingCashHandover::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('counter_id, SUM(amount) as approved_amount')
            ->groupBy('counter_id')
            ->get()
            ->keyBy('counter_id');

        // Get all unique counter IDs from sales and handovers
        $allCounterIds = $salesByCounter->keys()
            ->merge($pendingByCounter->keys())
            ->merge($approvedByCounter->keys())
            ->unique();

        // Get counter names
        $counters = Counter::whereIn('id', $allCounterIds)
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
        $pendingHandovers = BookingCashHandover::where('user_id', $userId)
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
     * Calculate available handover balance for bookings (legacy method for backward compatibility).
     *
     * @param int $userId
     * @param int $counterId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateAvailableBalance(int $userId, int $counterId, ?string $businessDate = null): array
    {
        // Calculate total sales for the user/counter/date
        // Only count confirmed bookings (status = 1)
        $totalSalesQuery = Booking::where('created_by', $userId)
            ->where('counter_id', $counterId)
            ->where('status', 1); // Only confirmed bookings

        if ($businessDate) {
            $totalSalesQuery->whereDate('date', $businessDate);
        }

        $totalSales = $totalSalesQuery->sum('final_price');

        // Calculate pending handovers
        $pendingHandoversQuery = BookingCashHandover::where('user_id', $userId)
            ->where('counter_id', $counterId)
            ->where('status', 'pending');

        if ($businessDate) {
            $pendingHandoversQuery->whereDate('business_date', $businessDate);
        }

        $pendingHandovers = $pendingHandoversQuery->sum('amount');

        // Calculate approved handovers
        $approvedHandoversQuery = BookingCashHandover::where('user_id', $userId)
            ->where('counter_id', $counterId)
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
