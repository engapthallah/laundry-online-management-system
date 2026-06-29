@extends('layouts.staff')

@section('content')
<div class="container py-2">
    <!-- Page Title & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Support Messages</h2>
            <p class="text-muted mb-0">Review, manage, reply, and track guest and customer support messages.</p>
        </div>
    </div>

    @php
        $totalCount = \App\Models\SupportMessage::count();
        $pendingCount = \App\Models\SupportMessage::where('status', 'pending')->count();
        $resolvedCount = \App\Models\SupportMessage::where('status', 'resolved')->count();
        $ignoredCount = \App\Models\SupportMessage::where('status', 'ignored')->count();
    @endphp

    <!-- Summary Stat Chips (4 in a row) -->
    <div class="row g-3 mb-4">
        <!-- Chip 1: Total -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-envelope fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">{{ $totalCount }}</h4>
                        <span class="text-muted small">Total Messages</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chip 2: Pending (Needs Action) -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-hourglass-half fa-lg"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fw-bold text-dark mb-0">{{ $pendingCount }}</h4>
                            @if($pendingCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">Needs Action</span>
                            @endif
                        </div>
                        <span class="text-muted small">Pending Support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chip 3: Resolved -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">{{ $resolvedCount }}</h4>
                        <span class="text-muted small">Resolved Tickets</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chip 4: Ignored -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary-subtle text-secondary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-circle-minus fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">{{ $ignoredCount }}</h4>
                        <span class="text-muted small">Ignored Tickets</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('staff.support.index') }}" class="row g-3 align-items-end">
                <!-- Search Input -->
                <div class="col-lg-4 col-md-6">
                    <label for="search" class="form-label fw-bold text-dark small">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-search"></i></span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               class="form-control bg-light border-start-0" 
                               placeholder="Search by name, email, or subject..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Status Filter Dropdown -->
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label fw-bold text-dark small">Status</label>
                    <select name="status" id="status" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="ignored" {{ request('status') === 'ignored' ? 'selected' : '' }}>Ignored</option>
                    </select>
                </div>

                <!-- Date Range: From -->
                <div class="col-lg-2.5 col-md-6">
                    <label for="date_from" class="form-label fw-bold text-dark small">Submitted From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control bg-light" value="{{ request('date_from') }}">
                </div>

                <!-- Date Range: To -->
                <div class="col-lg-2.5 col-md-6">
                    <label for="date_to" class="form-label fw-bold text-dark small">Submitted To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control bg-light" value="{{ request('date_to') }}">
                </div>

                <!-- Actions -->
                <div class="col-lg-2 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fa-solid fa-filter me-1.5"></i>Filter
                    </button>
                    <a href="{{ route('staff.support.index') }}" class="btn btn-outline-secondary w-100 fw-bold" title="Clear All Filters">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        @if($messages->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="border-0 px-4 py-3" style="width: 60px;">#</th>
                            <th class="border-0 py-3" style="width: 250px;">From</th>
                            <th class="border-0 py-3">Subject & Preview</th>
                            <th class="border-0 py-3" style="width: 150px;">Status</th>
                            <th class="border-0 py-3" style="width: 200px;">Submitted At</th>
                            <th class="border-0 py-3" style="width: 200px;">Replied At</th>
                            <th class="border-0 px-4 py-3 text-end" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            @php
                                // Row styling based on status
                                $rowClass = '';
                                if ($message->status === 'pending') {
                                    $rowClass = 'table-warning bg-warning bg-opacity-10';
                                } elseif ($message->status === 'ignored') {
                                    $rowClass = 'table-light text-muted fst-italic';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="px-4 fw-bold text-secondary">
                                    {{ $message->id }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-secondary-subtle text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($message->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $message->name }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 180px;">{{ $message->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 320px;">
                                        {{ Str::limit($message->subject, 50) }}
                                    </div>
                                    <div class="small text-secondary text-truncate" style="max-width: 320px;">
                                        {{ Str::limit($message->message, 60) }}
                                    </div>
                                </td>
                                <td>
                                    @if($message->status === 'pending')
                                        <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fw-semibold">
                                            Awaiting Reply
                                        </span>
                                    @elseif($message->status === 'resolved')
                                        <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill fw-semibold">
                                            Resolved
                                        </span>
                                    @elseif($message->status === 'ignored')
                                        <span class="badge bg-secondary text-white px-2.5 py-1.5 rounded-pill fw-semibold">
                                            Ignored
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-secondary">
                                    {{ $message->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="small text-secondary">
                                    @if($message->replied_at)
                                        {{ $message->replied_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-inline-flex gap-2">
                                        @if($message->status === 'pending')
                                            <a href="{{ route('staff.support.show', $message) }}" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                                <i class="fa-solid fa-reply me-1"></i>Reply
                                            </a>
                                        @endif
                                        <a href="{{ route('staff.support.show', $message) }}" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($messages->hasPages())
                <div class="card-footer bg-white border-0 py-3.5 px-4 border-top">
                    {{ $messages->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                    <i class="fa-solid fa-inbox fa-3x text-muted"></i>
                </div>
                <h4 class="fw-bold text-dark">No support messages found.</h4>
                <p class="text-muted mx-auto mb-0" style="max-width: 400px;">
                    No tickets match your filters, or all customer and guest support queries have been handled!
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
