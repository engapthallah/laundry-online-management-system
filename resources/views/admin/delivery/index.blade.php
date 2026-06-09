@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Delivery Assignments</h1>
    <a href="{{ route('admin.delivery.create') }}" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-shipping-fast me-2"></i>Assign Agent
    </a>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.delivery.index') }}" class="row g-3">
            <!-- Status filter -->
            <div class="col-12 col-md-6">
                <label for="status" class="form-label fw-semibold text-muted small">Filter by Delivery Status</label>
                <select name="status" id="status" class="form-select bg-light">
                    <option value="">All Delivery Statuses</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="picked_up" {{ request('status') === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                    <option value="on_the_way" {{ request('status') === 'on_the_way' ? 'selected' : '' }}>On The Way</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="col-12 col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-bold">Filter</button>
                <a href="{{ route('admin.delivery.index') }}" class="btn btn-outline-secondary px-4 fw-bold">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Assignments Table Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">#</th>
                        <th class="border-0">Order No</th>
                        <th class="border-0">Customer</th>
                        <th class="border-0">Delivery Agent</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Assigned At</th>
                        <th class="border-0">Delivered At</th>
                        <th class="border-0 px-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="px-4 text-muted">{{ $assignment->id }}</td>
                            <td class="fw-semibold text-primary">#{{ $assignment->order->order_number ?? 'N/A' }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $assignment->order->customer->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $assignment->order->delivery_address ?? 'No address' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $assignment->deliveryAgent->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $assignment->deliveryAgent->phone ?? 'No phone' }}</div>
                            </td>
                            <td>
                                @switch($assignment->status)
                                    @case('assigned')
                                        <span class="badge bg-warning text-dark text-capitalize px-2.5 py-1.5">Assigned</span>
                                        @break
                                    @case('picked_up')
                                        <span class="badge bg-info text-dark text-capitalize px-2.5 py-1.5">Picked Up</span>
                                        @break
                                    @case('on_the_way')
                                        <span class="badge bg-primary text-capitalize px-2.5 py-1.5">On The Way</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success text-capitalize px-2.5 py-1.5">Delivered</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-muted small">
                                {{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y \a\t g:i A') : 'N/A' }}
                            </td>
                            <td class="text-muted small">
                                {{ $assignment->delivered_at ? $assignment->delivered_at->format('M d, Y \a\t g:i A') : 'Not delivered' }}
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{ route('admin.delivery.show', $assignment->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="fa-solid fa-eye me-1"></i>Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-truck-ramp-box fs-2 mb-2 d-block text-secondary"></i>
                                No delivery assignments logged.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($assignments->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $assignments->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
