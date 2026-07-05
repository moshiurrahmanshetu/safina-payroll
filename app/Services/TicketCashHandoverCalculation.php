<?php

namespace App\Services;

use App\Models\TicketSale;
use App\Models\TicketCashHandover;
use App\Models\Gate;

class TicketCashHandoverCalculation
{
    /**
     * Calculate counter-wise available handover balance for ticket sales.
     * Cash ownership is based on User + Module + Counter, not current counter assignment.
     *
     * @param int $userId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateCounterWiseBalance(int $userId, ?string $businessDate = null): array
    {
        $businessDate = $businessDate ?? now()->format('Y-m-d');

        // Calculate sales by counter
        $salesByCounter = TicketSale::where('created_by', $userId)
            ->whereDate('date', $businessDate)
            ->selectRaw('gate_id, SUM(total_price) as total_sales')
            ->groupBy('gate_id')
            ->get()
            ->keyBy('gate_id');

        // Calculate pending handovers by counter
        $pendingByCounter = TicketCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('gate_id, SUM(amount) as pending_amount')
            ->groupBy('gate_id')
            ->get()
            ->keyBy('gate_id');

        // Calculate approved handovers by counter
        $approvedByCounter = TicketCashHandover::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('business_date', $businessDate)
            ->selectRaw('gate_id, SUM(amount) as approved_amount')
            ->groupBy('gate_id')
            ->get()
            ->keyBy('gate_id');

        // Get all unique gate IDs from sales and handovers
        $allGateIds = $salesByCounter->keys()
            ->merge($pendingByCounter->keys())
            ->merge($approvedByCounter->keys())
            ->unique();

        // Get gate names
        $gates = Gate::whereIn('id', $allGateIds)
            ->pluck('name', 'id');

        // Build counter-wise balance data
        $counterWiseBalance = [];
        foreach ($allGateIds as $gateId) {
            $totalSales = $salesByCounter->get($gateId)?->total_sales ?? 0;
            $pendingAmount = $pendingByCounter->get($gateId)?->pending_amount ?? 0;
            $approvedAmount = $approvedByCounter->get($gateId)?->approved_amount ?? 0;
            $availableBalance = max(0, $totalSales - $pendingAmount - $approvedAmount);

            $counterWiseBalance[] = [
                'counter_id' => $gateId,
                'counter_name' => $gates->get($gateId, 'Unknown Gate'),
                'business_date' => $businessDate,
                'total_sales' => $totalSales,
                'pending_amount' => $pendingAmount,
                'approved_amount' => $approvedAmount,
                'available_amount' => $availableBalance,
            ];
        }

        // Check if there are any pending handovers (with gate relationship eager loaded)
        $pendingHandovers = TicketCashHandover::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('business_date', $businessDate)
            ->with('gate')
            ->get();

        return [
            'counter_wise_balance' => $counterWiseBalance,
            'pending_handovers' => $pendingHandovers,
            'has_pending_handover' => $pendingHandovers->isNotEmpty(),
        ];
    }

    /**
     * Calculate available handover balance for ticket sales (legacy method for backward compatibility).
     *
     * @param int $userId
     * @param int $gateId
     * @param string|null $businessDate
     * @return array
     */
    public function calculateAvailableBalance(int $userId, int $gateId, ?string $businessDate = null): array
    {
        // Calculate total sales for the user/gate/date
        $totalSalesQuery = TicketSale::where('created_by', $userId)
            ->where('gate_id', $gateId);

        if ($businessDate) {
            $totalSalesQuery->whereDate('date', $businessDate);
        }

        $totalSales = $totalSalesQuery->sum('total_price');

        // Calculate pending handovers
        $pendingHandoversQuery = TicketCashHandover::where('user_id', $userId)
            ->where('gate_id', $gateId)
            ->where('status', 'pending');

        if ($businessDate) {
            $pendingHandoversQuery->whereDate('business_date', $businessDate);
        }

        $pendingHandovers = $pendingHandoversQuery->sum('amount');

        // Calculate approved handovers
        $approvedHandoversQuery = TicketCashHandover::where('user_id', $userId)
            ->where('gate_id', $gateId)
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
