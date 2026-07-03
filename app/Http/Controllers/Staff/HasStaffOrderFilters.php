<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait HasStaffOrderFilters
{
    /**
     * Apply standard search and filter parameters to the staff orders query.
     */
    protected function applyOrderFilters(Builder $query, Request $request): Builder
    {
        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // Filter by status (including "active" status parameter from sidebar link)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->whereIn('status', ['delivered_to_laundry', 'processing']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by date range (pickup_time)
        if ($request->filled('date_from')) {
            $query->whereDate('pickup_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pickup_time', '<=', $request->date_to);
        }

        return $query;
    }
}
