@extends('layouts.staff')

@section('content')
<style>
    /* Status Badge styling */
    .badge-status {
        padding: 5px 10px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-status-confirmed { background-color: #0d6efd; color: #fff; }
    .badge-status-washing { background-color: #6610f2; color: #fff; }
    .badge-status-drying { background-color: #6f42c1; color: #fff; }
    .badge-status-ironing { background-color: #8f00ff; color: #fff; }
    .badge-status-folding { background-color: #20c997; color: #fff; }
    .badge-status-ready_for_delivery { background-color: #198754; color: #fff; }

    /* Custom hover transition for table rows */
    .table-hover tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(26, 82, 118, 0.04) !important;
    }

    /* Column headers styling */
    .sort-header {
        text-decoration: none;
        color: #495057;
        font-weight: 600;
    }
    .sort-header:hover {
        color: #1a5276;
    }
</style>

@php
    $currentSortBy = request('sort_by', 'updated_at');
    $currentSortDir = request('sort_dir', 'desc');

    function getSortUrl($column, $currentSortBy, $currentSortDir) {
        $newDir = ($currentSortBy === $column && $currentSortDir === 'asc') ? 'desc' : 'asc';
        return route('staff.orders.index', array_merge(request()->query(), [
            'sort_by' => $column,
            'sort_dir' => $newDir
        ]));
    }

    function getSortIcon($column, $currentSortBy, $currentSortDir) {
        if ($currentSortBy !== $column) {
            return '<i class="fa-solid fa-sort text-muted ms-1 small"></i>';
        }
        return $currentSortDir === 'asc' 
            ? '<i class="fa-solid fa-sort-up text-primary ms-1"></i>' 
            : '<i class="fa-solid fa-sort-down text-primary ms-1"></i>';
    }
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold text-dark mb-1">My Assigned Orders</h3>
                <p class="text-secondary mb-0">View all orders assigned to you and update their status in the laundry cycle.</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Panel Card -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white p-4">
    <form method="GET" action="{{ route('staff.orders.index') }}" class="row g-3">
        <!-- Preserve sorting when filtering -->
        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'updated_at') }}">
        <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

        <!-- Order Number Search -->
        <div class="col-12 col-md-3">
            <label for="search" class="form-label fw-semibold text-secondary small">Search Order No.</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="search" name="search" placeholder="e.g. LOMS-1002" value="{{ request('search') }}">
            </div>
        </div>

        <!-- Status Filter -->
        <div class="col-12 col-md-3">
            <label for="status" class="form-label fw-semibold text-secondary small">Filter Status</label>
            <select class="form-select bg-light" id="status" name="status">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Processes (Washing-Folding)</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="washing" {{ request('status') === 'washing' ? 'selected' : '' }}>Washing</option>
                <option value="drying" {{ request('status') === 'drying' ? 'selected' : '' }}>Drying</option>
                <option value="ironing" {{ request('status') === 'ironing' ? 'selected' : '' }}>Ironing</option>
                <option value="folding" {{ request('status') === 'folding' ? 'selected' : '' }}>Folding</option>
                <option value="ready_for_delivery" {{ request('status') === 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
            </select>
        </div>

        <!-- Date From Filter -->
        <div class="col-12 col-md-2">
            <label for="date_from" class="form-label fw-semibold text-secondary small">Pickup From</label>
            <input type="date" class="form-control bg-light" id="date_from" name="date_from" value="{{ request('date_from') }}">
        </div>

        <!-- Date To Filter -->
        <div class="col-12 col-md-2">
            <label for="date_to" class="form-label fw-semibold text-secondary small">Pickup To</label>
            <input type="date" class="form-control bg-light" id="date_to" name="date_to" value="{{ request('date_to') }}">
        </div>

        <!-- Action Buttons -->
        <div class="col-12 col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary w-100 fw-semibold" title="Clear Filters">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Orders Listing Card -->
<div class="card border-0 shadow-sm rounded-3 bg-white">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                    <i class="fa-solid fa-soap text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-dark fw-bold mb-1">No Orders Found</h5>
                <p class="text-secondary mb-0">No orders have been assigned to you yet, or they don't match the active filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small font-monospace" style="width: 60px;">#</th>
                            <th class="py-3 text-secondary small">Order No</th>
                            <th class="py-3 text-secondary small">Customer</th>
                            <th class="py-3 text-secondary small">Services</th>
                            <th class="py-3 text-secondary small text-end">Total Price</th>
                            <th class="py-3 text-secondary text-center small">
                                <a href="{!! getSortUrl('status', $currentSortBy, $currentSortDir) !!}" class="sort-header">
                                    Status {!! getSortIcon('status', $currentSortBy, $currentSortDir) !!}
                                </a>
                            </th>
                            <th class="py-3 text-secondary small">
                                <a href="{!! getSortUrl('pickup_time', $currentSortBy, $currentSortDir) !!}" class="sort-header">
                                    Pickup Date {!! getSortIcon('pickup_time', $currentSortBy, $currentSortDir) !!}
                                </a>
                            </th>
                            <th class="py-3 text-secondary small">Last Updated</th>
                            <th class="py-3 text-center text-secondary small" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $index => $order)
                            <tr>
                                <td class="ps-4 fw-medium text-secondary small font-monospace">
                                    {{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">#{{ $order->order_number }}</span>
                                </td>
                                <td class="fw-semibold text-secondary">
                                    {{ explode(' ', $order->customer->name)[0] }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 small rounded">
                                        {{ $order->orderItems->count() }} Service(s)
                                    </span>
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    ${{ number_format($order->total_price, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge-status badge-status-{{ $order->status }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="text-secondary small">
                                    <i class="fa-regular fa-calendar-days me-1 text-muted"></i>
                                    {{ $order->pickup_time->format('M d, Y h:i A') }}
                                </td>
                                <td class="text-secondary small">
                                    {{ $order->updated_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('staff.orders.show', $order) }}" class="btn btn-sm btn-primary fw-semibold px-3">
                                        <i class="fa-solid fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            <div class="d-flex justify-content-between align-items-center p-4 border-top border-light">
                <div class="text-muted small">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </div>
                <div>
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
