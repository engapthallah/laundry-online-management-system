@extends('layouts.customer')

@section('content')
<div class="container py-2">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">My Support Messages</h2>
            <p class="text-muted mb-0">Get help with your laundry orders, payments, and other inquiries.</p>
        </div>
        <a href="{{ route('customer.support.create') }}" class="btn btn-primary px-4 py-2 fw-semibold rounded-3">
            <i class="fa-solid fa-plus me-2"></i>New Message
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-1">
                @php
                    $currentStatus = request('status');
                @endphp
                <li class="nav-item">
                    <a class="nav-link rounded-3 fw-semibold py-2.5 {{ is_null($currentStatus) ? 'active bg-primary text-white' : 'text-secondary bg-transparent' }}" 
                       href="{{ route('customer.support.index') }}">
                        <i class="fa-solid fa-list me-2"></i>All Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-3 fw-semibold py-2.5 {{ $currentStatus === 'pending' ? 'active bg-warning text-dark' : 'text-secondary bg-transparent' }}" 
                       href="{{ route('customer.support.index', ['status' => 'pending']) }}">
                        <i class="fa-solid fa-clock me-2"></i>Pending
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-3 fw-semibold py-2.5 {{ $currentStatus === 'resolved' ? 'active bg-success text-white' : 'text-secondary bg-transparent' }}" 
                       href="{{ route('customer.support.index', ['status' => 'resolved']) }}">
                        <i class="fa-solid fa-check-circle me-2"></i>Resolved
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-3 fw-semibold py-2.5 {{ $currentStatus === 'ignored' ? 'active bg-secondary text-white' : 'text-secondary bg-transparent' }}" 
                       href="{{ route('customer.support.index', ['status' => 'ignored']) }}">
                        <i class="fa-solid fa-times-circle me-2"></i>Closed
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Messages List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        @if($messages->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($messages as $message)
                    @php
                        // Highlight resolved messages with admin replies that the customer should see
                        $isHighlighted = $message->status === 'resolved' && !is_null($message->admin_reply);
                    @endphp
                    <div class="list-group-item list-group-item-action p-4 transition-all {{ $isHighlighted ? 'border-start border-primary border-4 bg-primary-subtle bg-opacity-25' : '' }}">
                        <div class="row align-items-center g-3">
                            <!-- Left side info -->
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fw-bold text-dark mb-0">{{ $message->subject }}</h5>
                                </div>
                                <p class="text-secondary mb-2 small">
                                    {{ \Illuminate\Support\Str::limit($message->message, 80) }}
                                </p>
                                <div class="d-flex align-items-center gap-3 text-muted small">
                                    <span>
                                        <i class="fa-regular fa-clock me-1"></i>
                                        Submitted: {{ $message->created_at->format('M d, Y h:i A') }}
                                    </span>
                                    @if($message->replied_at)
                                        <span class="text-success fw-medium">
                                            <i class="fa-solid fa-reply me-1"></i>
                                            Replied: {{ $message->replied_at->format('M d, Y h:i A') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Right side action and badges -->
                            <div class="col-md-4 text-md-end d-flex flex-column align-items-md-end justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Status Badge -->
                                    @if($message->status === 'pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">
                                            <i class="fa-regular fa-hourglass me-1"></i>Awaiting Reply
                                        </span>
                                    @elseif($message->status === 'resolved')
                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-semibold">
                                            <i class="fa-solid fa-check me-1"></i>Replied
                                        </span>
                                    @elseif($message->status === 'ignored')
                                        <span class="badge bg-secondary text-white px-3 py-2 rounded-pill fw-semibold">
                                            <i class="fa-solid fa-circle-minus me-1"></i>Closed
                                        </span>
                                    @endif

                                    <!-- Reply Indicator -->
                                    @if($message->admin_reply)
                                        <span class="text-success small fw-semibold ms-2">
                                            <i class="fa-solid fa-circle-check me-1"></i>Reply received
                                        </span>
                                    @else
                                        <span class="text-muted small ms-2">
                                            <i class="fa-solid fa-circle-notch fa-spin me-1"></i>Waiting...
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2 mt-md-0">
                                    <a href="{{ route('customer.support.show', $message) }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">
                                        View Conversation <i class="fa-solid fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($messages->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $messages->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-4">
                    <i class="fa-regular fa-envelope-open fa-3x text-muted"></i>
                </div>
                <h4 class="fw-bold text-dark">No support messages yet.</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 400px;">
                    Have a question or issue regarding your laundry order, payment status, or pickup schedules? We are here to help you.
                </p>
                <a href="{{ route('customer.support.create') }}" class="btn btn-primary px-4 py-2.5 fw-semibold rounded-3">
                    <i class="fa-solid fa-paper-plane me-2"></i>Send Your First Message
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
