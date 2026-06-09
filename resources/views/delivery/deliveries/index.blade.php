@extends('layouts.delivery')

@section('content')
<style>
    /* Status Badge styling */
    .badge-delivery {
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-delivery-assigned { background-color: #ffc107; color: #212529; }
    .badge-delivery-picked_up { background-color: #0d6efd; color: #fff; }
    .badge-delivery-on_the_way { background-color: #fd7e14; color: #fff; }
    .badge-delivery-delivered { background-color: #198754; color: #fff; }

    /* Custom hover transition for table rows */
    .table-hover tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(211, 84, 0, 0.04) !important;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-dark mb-1">My Transit Assignments</h3>
        <p class="text-secondary mb-0">Manage and execute pickup and delivery tasks assigned to you.</p>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs border-bottom-2">
            <li class="nav-item">
                <a class="nav-link fw-bold py-3 px-4 {{ $tab === 'active' ? 'active text-primary border-primary border-bottom-3' : 'text-secondary' }}" 
                   href="{{ route('delivery.deliveries.index', ['tab' => 'active', 'search' => request('search'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                    <i class="fa-solid fa-map-marker-alt me-2"></i>Active Deliveries
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold py-3 px-4 {{ $tab === 'completed' ? 'active text-primary border-primary border-bottom-3' : 'text-secondary' }}" 
                   href="{{ route('delivery.deliveries.index', ['tab' => 'completed', 'search' => request('search')]) }}">
                    <i class="fa-solid fa-check-double me-2"></i>Completed Deliveries
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Filters Panel Card (Applies to active tab / searches both tabs) -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white p-4">
    <form method="GET" action="{{ route('delivery.deliveries.index') }}" class="row g-3">
        <!-- Hidden tab indicator to maintain state -->
        <input type="hidden" name="tab" value="{{ $tab }}">

        <!-- Order Number Search -->
        <div class="col-12 col-md-4">
            <label for="search" class="form-label fw-semibold text-secondary small">Search Order No.</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="search" name="search" placeholder="e.g. LOMS-1004" value="{{ request('search') }}">
            </div>
        </div>

        <!-- Date Range Filter (Only makes sense for assigned_at date filters on the active tab) -->
        <div class="col-12 col-md-3">
            <label for="start_date" class="form-label fw-semibold text-secondary small">Start Date (Assigned)</label>
            <input type="date" class="form-control bg-light" id="start_date" name="start_date" value="{{ request('start_date') }}">
        </div>

        <div class="col-12 col-md-3">
            <label for="end_date" class="form-label fw-semibold text-secondary small">End Date (Assigned)</label>
            <input type="date" class="form-control bg-light" id="end_date" name="end_date" value="{{ request('end_date') }}">
        </div>

        <!-- Action Buttons -->
        <div class="col-12 col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold text-white">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                <a href="{{ route('delivery.deliveries.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary w-100 fw-semibold" title="Clear Filters">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Deliveries Table Card -->
<div class="card border-0 shadow-sm rounded-3 bg-white">
    <div class="card-body p-0">
        @if($assignments->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                    @if($tab === 'active')
                        <i class="fa-solid fa-truck-ramp-box text-muted" style="font-size: 3rem;"></i>
                    @else
                        <i class="fa-solid fa-box-open text-muted" style="font-size: 3rem;"></i>
                    @endif
                </div>
                <h5 class="text-dark fw-bold mb-1">
                    @if($tab === 'active')
                        No Active Deliveries Found
                    @else
                        No Completed Deliveries Found
                    @endif
                </h5>
                <p class="text-secondary mb-0">
                    @if($tab === 'active')
                        No active deliveries. Check back soon!
                    @else
                        No completed deliveries yet.
                    @endif
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small" style="width: 60px;">#</th>
                            <th class="py-3 text-secondary small">Order No</th>
                            <th class="py-3 text-secondary small">Customer</th>
                            <th class="py-3 text-secondary small">Delivery Address</th>
                            <th class="py-3 text-secondary small text-center" style="width: 140px;">Payment Method</th>
                            <th class="py-3 text-secondary small text-center" style="width: 150px;">Status</th>
                            <th class="py-3 text-secondary small" style="width: 200px;">Assigned At</th>
                            <th class="py-3 text-secondary small" style="width: 200px;">Delivered At</th>
                            <th class="py-3 text-center text-secondary small" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $index => $assignment)
                            <tr>
                                <td class="ps-4 fw-medium text-secondary small font-monospace">
                                    {{ ($assignments->currentPage() - 1) * $assignments->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">#{{ $assignment->order->order_number }}</span>
                                </td>
                                <td class="fw-semibold text-secondary">
                                    {{ explode(' ', $assignment->order->customer->name)[0] }}
                                </td>
                                <td class="text-secondary small">
                                    {{ Str::limit($assignment->order->delivery_address, 55) }}
                                </td>
                                <td class="text-center">
                                    @if($assignment->order->payment_method === 'cash')
                                        <span class="badge bg-danger">Collect Cash</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge-delivery badge-delivery-{{ $assignment->status }}">
                                        {{ str_replace('_', ' ', $assignment->status) }}
                                    </span>
                                </td>
                                <td class="text-secondary small">
                                    <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                    {{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                                <td class="text-secondary small">
                                    <i class="fa-regular fa-calendar-check me-1 text-muted"></i>
                                    {{ $assignment->delivered_at ? $assignment->delivered_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('delivery.deliveries.show', $assignment) }}" class="btn btn-sm btn-primary text-white fw-semibold px-3">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-between align-items-center p-4 border-top border-light">
                <div class="text-muted small">
                    Showing {{ $assignments->firstItem() }} to {{ $assignments->lastItem() }} of {{ $assignments->total() }} assignments
                </div>
                <div>
                    {{ $assignments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
