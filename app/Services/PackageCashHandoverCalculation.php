<?php

namespace App\Services;

use App\Models\PackageBooking;
use App\Models\PackageCashHandover;
use App\Models\PackageCounter;

class PackageCashHandoverCalculation
{
    /**
     * Calculate counter-wise available handover balance for package bookings.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param int $userId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateCounterWiseBalance(int $userId, ?string $businessDate = null): array
    {
        $businessDate = $businessDate ?? now()->format('Y-m-d');

        // Calculate sales by counter (using package_counter_id)
        $salesByCounter = PackageBooking::where('created_by', $userId)
            ->whereDate('date', $businessDate)
            ->selectRaw('package_counter_id, SUM(final_amount) as total_sales')
            ->groupBy('package_counter_id')
            ->get()
            ->keyBy('package_counter_id');

        // Calculate pending handovers by counter (using counter_id from package_cash_handovers table)
        $pendingByCounter = PackageCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('counter_id, SUM(amount) as pending_amount')
            ->groupBy('counter_id')
            ->get()
            ->keyBy('counter_id');

        // Calculate approved handovers by counter (using counter_id from package_cash_handovers table)
        $approvedByCounter = PackageCashHandover::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('counter_id, SUM(amount) as approved_amount')
            ->groupBy('counter_id')
            ->get()
            ->keyBy('counter_id');

        // Get all unique counter IDs from sales (package_counter_id) and handovers (counter_id)
        // Note: Sales use package_counter_id, handovers use counter_id - need to map them
        $salesCounterIds = $salesByCounter->keys();
        $handoverCounterIds = $pendingByCounter->keys()
            ->merge($approvedByCounter->keys())
            ->unique();

        // For now, use sales counter IDs since that's where the actual sales data comes from
        $allCounterIds = $salesCounterIds;

        // Get counter names from PackageCounter table
        $counters = PackageCounter::whereIn('id', $allCounterIds)
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
        $pendingHandovers = PackageCashHandover::where('user_id', $userId)
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
     * Calculate available handover balance for package bookings (legacy method for backward compatibility).
     *
     * @param int $userId
     * @param int $packageCounterId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateAvailableBalance(int $userId, int $packageCounterId, ?string $businessDate = null): array
    {
        // Calculate total sales for the user/counter/date (using package_counter_id)
        $totalSalesQuery = PackageBooking::where('created_by', $userId)
            ->where('package_counter_id', $packageCounterId);

        if ($businessDate) {
            $totalSalesQuery->whereDate('date', $businessDate);
        }

        $totalSales = $totalSalesQuery->sum('final_amount');

        // Calculate pending handovers (using counter_id from package_cash_handovers table)
        $pendingHandoversQuery = PackageCashHandover::where('user_id', $userId)
            ->where('counter_id', $packageCounterId)
            ->where('status', 'pending');

        if ($businessDate) {
            $pendingHandoversQuery->whereDate('business_date', $businessDate);
        }

        $pendingHandovers = $pendingHandoversQuery->sum('amount');

        // Calculate approved handovers (using counter_id from package_cash_handovers table)
        $approvedHandoversQuery = PackageCashHandover::where('user_id', $userId)
            ->where('counter_id', $packageCounterId)
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
