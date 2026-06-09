@extends('layouts.customer')

@section('content')
<div class="container py-2">
    <!-- Back Button & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('customer.support.index') }}" class="text-decoration-none text-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>My Messages
        </a>
        <div>
            @if($support->status === 'pending')
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">
                    <i class="fa-regular fa-clock me-1"></i>Awaiting Reply
                </span>
            @elseif($support->status === 'resolved')
                <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-semibold">
                    <i class="fa-solid fa-check me-1"></i>Resolved
                </span>
            @elseif($support->status === 'ignored')
                <span class="badge bg-secondary text-white px-3 py-2 rounded-pill fw-semibold">
                    <i class="fa-solid fa-circle-minus me-1"></i>Closed
                </span>
            @endif
        </div>
    </div>

    <!-- Message Details Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom py-3.5 px-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Support Ticket #SUP-{{ str_pad($support->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1">{{ $support->subject }}</h4>
                </div>
                <div class="text-muted small">
                    <i class="fa-regular fa-calendar me-1"></i>Opened: {{ $support->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="card-body bg-light p-4" style="min-height: 350px;">
            <div class="d-flex flex-column gap-4">
                
                <!-- 1. Customer Message Bubble (Right-aligned) -->
                <div class="d-flex justify-content-end align-items-start gap-3">
                    <!-- Text bubble -->
                    <div class="d-flex flex-column align-items-end text-end">
                        <div class="text-muted small mb-1 fw-semibold">
                            {{ $support->name }} <span class="fw-normal">(You)</span>
                        </div>
                        <div class="p-3 shadow-sm border text-start text-dark" 
                             style="background-color: #e3f2fd; max-width: 85%; border-radius: 1.25rem 1.25rem 0 1.25rem; white-space: pre-wrap; font-size: 0.95rem;">
                            <h6 class="fw-bold text-primary mb-2">{{ $support->subject }}</h6>
                            {{ $support->message }}
                        </div>
                        <small class="text-muted mt-1" style="font-size: 0.75rem;">
                            <i class="fa-regular fa-clock me-1"></i>Sent on: {{ $support->created_at->format('M d, Y h:i A') }}
                        </small>
                    </div>

                    <!-- Avatar -->
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                         style="width: 40px; height: 40px; min-width: 40px; font-size: 0.95rem;">
                        {{ strtoupper(substr($support->name, 0, 1)) }}
                    </div>
                </div>

                <!-- 2. Admin Reply Section (Left-aligned) -->
                @if($support->admin_reply)
                    <div class="d-flex justify-content-start align-items-start gap-3">
                        <!-- Avatar -->
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" 
                             style="width: 40px; height: 40px; min-width: 40px; font-size: 0.95rem;">
                            <i class="fa-solid fa-headset"></i>
                        </div>

                        <!-- Text bubble -->
                        <div class="d-flex flex-column align-items-start">
                            <div class="text-success small mb-1 fw-bold">
                                Support Team <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">Staff</span>
                            </div>
                            <div class="p-3 shadow-sm bg-white border-start border-success border-4 text-dark" 
                                 style="max-width: 85%; border-radius: 0 1.25rem 1.25rem 1.25rem; white-space: pre-wrap; font-size: 0.95rem;">
                                <p class="mb-0">{{ $support->admin_reply }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <i class="fa-regular fa-clock me-1"></i>Replied on: {{ $support->replied_at ? $support->replied_at->format('M d, Y h:i A') : $support->updated_at->format('M d, Y h:i A') }}
                                </small>
                                <span class="badge bg-success text-white font-monospace rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Resolved</span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Awaiting Reply Card (Centered or Left-aligned) -->
                    <div class="d-flex justify-content-start align-items-start gap-3">
                        <!-- Avatar -->
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm animate-pulse" 
                             style="width: 40px; height: 40px; min-width: 40px;">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>

                        <div class="d-flex flex-column align-items-start">
                            <div class="text-muted small mb-1 fw-semibold">
                                Support Queue
                            </div>
                            <div class="card border-0 shadow-sm p-4 text-center" style="max-width: 450px; border-radius: 0 1.25rem 1.25rem 1.25rem;">
                                <div class="mb-3 text-warning">
                                    <i class="fa-solid fa-hourglass-half fa-2x"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Your message is awaiting a response.</h6>
                                <p class="text-secondary small mb-3">
                                    Our representative will check your request and respond soon. We typically reply within 24 business hours.
                                </p>
                                <div>
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                        Awaiting Staff Response
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- Action Bar / Footer -->
        <div class="card-footer bg-white border-top p-3 text-end">
            <a href="{{ route('customer.support.create') }}" class="btn btn-primary px-4 py-2 fw-semibold rounded-3">
                <i class="fa-solid fa-paper-plane me-2"></i>Send Another Message
            </a>
        </div>
    </div>
</div>
@endsection
