<?php

namespace App\Services;

use App\Models\WaterParkTicket;
use App\Models\WaterParkCashHandover;
use App\Models\WaterParkCounter;

class WaterParkCashHandoverCalculation
{
    /**
     * Calculate counter-wise available handover balance for water park tickets.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param int $userId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateCounterWiseBalance(int $userId, ?string $businessDate = null): array
    {
        $businessDate = $businessDate ?? now()->format('Y-m-d');

        // Calculate sales by counter (final payable amount = price + extra_amount)
        $salesByCounter = WaterParkTicket::where('created_by', $userId)
            ->whereDate('created_at', $businessDate)
            ->selectRaw('water_park_counter_id, SUM(price + extra_amount) as total_sales')
            ->groupBy('water_park_counter_id')
            ->get()
            ->keyBy('water_park_counter_id');

        // Calculate pending handovers by counter
        $pendingByCounter = WaterParkCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('water_park_counter_id, SUM(amount) as pending_amount')
            ->groupBy('water_park_counter_id')
            ->get()
            ->keyBy('water_park_counter_id');

        // Calculate approved handovers by counter
        $approvedByCounter = WaterParkCashHandover::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('water_park_counter_id, SUM(amount) as approved_amount')
            ->groupBy('water_park_counter_id')
            ->get()
            ->keyBy('water_park_counter_id');

        // Get all unique counter IDs from sales and handovers
        $allCounterIds = $salesByCounter->keys()
            ->merge($pendingByCounter->keys())
            ->merge($approvedByCounter->keys())
            ->unique();

        // Get counter names
        $counters = WaterParkCounter::whereIn('id', $allCounterIds)
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
        $pendingHandovers = WaterParkCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->with('waterParkCounter')
            ->get();

        return [
            'counter_wise_balance' => $counterWiseBalance,
            'pending_handovers' => $pendingHandovers,
            'has_pending_handover' => $pendingHandovers->isNotEmpty(),
        ];
    }

    /**
     * Calculate available handover balance for water park tickets (legacy method for backward compatibility).
     *
     * @param int $userId
     * @param int $waterParkCounterId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateAvailableBalance(int $userId, int $waterParkCounterId, ?string $businessDate = null): array
    {
        // Calculate total sales for the user/counter/date
        // Final payable amount = price + extra_amount
        $totalSalesQuery = WaterParkTicket::where('created_by', $userId)
            ->where('water_park_counter_id', $waterParkCounterId);

        if ($businessDate) {
            $totalSalesQuery->whereDate('created_at', $businessDate);
        }

        // Calculate final payable amount: price + extra_amount
        $totalSales = $totalSalesQuery->selectRaw('SUM(price + extra_amount) as total')
            ->value('total') ?? 0;

        // Calculate pending handovers
        $pendingHandoversQuery = WaterParkCashHandover::where('user_id', $userId)
            ->where('water_park_counter_id', $waterParkCounterId)
            ->where('status', 'pending');

        if ($businessDate) {
            $pendingHandoversQuery->whereDate('business_date', $businessDate);
        }

        $pendingHandovers = $pendingHandoversQuery->sum('amount');

        // Calculate approved handovers
        $approvedHandoversQuery = WaterParkCashHandover::where('user_id', $userId)
            ->where('water_park_counter_id', $waterParkCounterId)
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
