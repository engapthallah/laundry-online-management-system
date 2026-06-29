@extends('layouts.staff')

@section('content')
<div class="container py-2">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('staff.support.index') }}" class="text-decoration-none text-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to All Messages
        </a>
    </div>

    <!-- Main Workspace: Two-column layout -->
    <div class="row g-4">
        <!-- Left Column: Header + Message History -->
        <div class="col-lg-8">
            
            <!-- Ticket Header Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($message->status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-semibold">Awaiting Reply</span>
                        @elseif($message->status === 'resolved')
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-semibold">Resolved</span>
                        @elseif($message->status === 'ignored')
                            <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill fw-semibold">Ignored</span>
                        @endif
                        <span class="text-muted small">Support Ticket #SUP-{{ str_pad($message->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-3">{{ $message->subject }}</h3>
                    <div class="d-flex flex-column gap-1 text-secondary small">
                        <div>
                            <i class="fa-regular fa-calendar-plus me-2 text-primary"></i><strong>Submitted:</strong> {{ $message->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div>
                            <i class="fa-regular fa-clock me-2 text-primary"></i><strong>Replied At:</strong> 
                            @if($message->replied_at)
                                <span class="text-success fw-semibold">{{ $message->replied_at->format('M d, Y h:i A') }}</span>
                            @else
                                <span class="text-muted">Not yet replied</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message History (Chat layout) -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-regular fa-comments me-2 text-primary"></i>Message History</h5>
                </div>
                <div class="card-body bg-light p-4" style="min-height: 250px;">
                    <div class="d-flex flex-column gap-4">
                        
                        <!-- Customer Message (Right-aligned bubble) -->
                        <div class="d-flex justify-content-end align-items-start gap-3">
                            <div class="d-flex flex-column align-items-end text-end">
                                <div class="text-muted small mb-1 fw-semibold">
                                    {{ $message->name }}
                                </div>
                                <div class="p-3 shadow-sm border text-start text-dark" 
                                     style="background-color: #e3f2fd; max-width: 90%; border-radius: 1.25rem 1.25rem 0 1.25rem; white-space: pre-wrap; font-size: 0.95rem;">
                                    <h6 class="fw-bold text-primary mb-2">{{ $message->subject }}</h6>
                                    {{ $message->message }}
                                </div>
                                <small class="text-muted mt-1" style="font-size: 0.75rem;">
                                    Sent: {{ $message->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                 style="width: 40px; height: 40px; min-width: 40px; font-size: 0.95rem;">
                                {{ strtoupper(substr($message->name, 0, 1)) }}
                            </div>
                        </div>

                        <!-- Support Reply (Left-aligned bubble) -->
                        @if($message->admin_reply)
                            <div class="d-flex justify-content-start align-items-start gap-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" 
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <div class="d-flex flex-column align-items-start">
                                    <div class="text-success small mb-1 fw-bold">
                                        Support Team Replied:
                                    </div>
                                    <div class="p-3 shadow-sm bg-white border-start border-success border-4 text-dark" 
                                         style="max-width: 90%; border-radius: 0 1.25rem 1.25rem 1.25rem; white-space: pre-wrap; font-size: 0.95rem;">
                                        {{ $message->admin_reply }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            Replied: {{ $message->replied_at ? $message->replied_at->format('M d, Y h:i A') : $message->updated_at->format('M d, Y h:i A') }}
                                        </small>
                                        <span class="badge bg-success text-white font-monospace rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Resolved</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Customer Info + Manage This Message -->
        <div class="col-lg-4">

            <!-- Customer Information Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-regular fa-user me-2 text-primary"></i>Customer Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-2.5 text-secondary small">
                        <div>
                            <strong>Name:</strong> <span class="text-dark fw-semibold">{{ $message->name }}</span>
                        </div>
                        <div>
                            <strong>Email:</strong> <span class="text-dark">{{ $message->email }}</span>
                        </div>
                        <div>
                            <strong>Phone:</strong> 
                            @if($message->user && $message->user->phone)
                                <span class="text-dark">{{ $message->user->phone }}</span>
                            @else
                                <span class="text-muted">N/A (Guest Submission)</span>
                            @endif
                        </div>
                        <div>
                            <strong>Member Since:</strong> 
                            @if($message->user)
                                <span class="text-dark">{{ $message->user->created_at->format('M Y') }}</span>
                            @else
                                <span class="text-muted">N/A (Guest)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage This Message Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-gears me-2 text-primary"></i>Manage This Message</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if($message->status === 'pending')
                        <!-- Reply Form -->
                        <div>
                            <h6 class="fw-bold text-dark mb-3">Send Reply to Customer</h6>
                            <form method="POST" action="{{ route('staff.support.reply', $message->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="reply_content" class="form-label small text-muted fw-semibold mb-0">Reply content <span class="text-danger">*</span></label>
                                        <small id="staff-char-counter" class="text-muted">0 / 3000</small>
                                    </div>
                                    <textarea name="reply_content" 
                                              id="reply_content" 
                                              rows="6" 
                                              class="form-control @error('reply_content') is-invalid @enderror" 
                                              placeholder="Type your response here. Be clear, professional, and helpful."
                                              required 
                                              maxlength="3000">{{ old('reply_content') }}</textarea>
                                    @error('reply_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success w-100 fw-bold py-2 rounded-3 shadow-sm mb-2">
                                    <i class="fa-solid fa-reply me-2"></i>Send Reply & Resolve
                                </button>
                                <small class="text-muted d-block text-center" style="font-size: 0.75rem;">
                                    Sending a reply will automatically mark this message as Resolved.
                                </small>
                            </form>
                        </div>
                    
                    @elseif($message->status === 'resolved')
                        <!-- Resolved Status Info -->
                        <div class="alert alert-success border-0 rounded-4 p-3.5 mb-0 text-center">
                            <div class="text-success mb-2">
                                <i class="fa-solid fa-circle-check fa-2x"></i>
                            </div>
                            <h6 class="fw-bold mb-1">This ticket has been resolved.</h6>
                            <p class="small mb-0 text-success-emphasis opacity-90">
                                Replied on {{ $message->replied_at ? $message->replied_at->format('M d, Y h:i A') : $message->updated_at->format('M d, Y h:i A') }}
                            </p>
                        </div>

                    @elseif($message->status === 'ignored')
                        <!-- Ignored Status Info -->
                        <div class="alert alert-secondary border-0 rounded-4 p-3.5 mb-0 text-center">
                            <div class="text-secondary mb-2">
                                <i class="fa-solid fa-circle-minus fa-2x"></i>
                            </div>
                            <h6 class="fw-bold mb-1">This message was marked as ignored.</h6>
                            <p class="small mb-0 text-secondary-emphasis opacity-90">
                                It was closed without sending an admin reply.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('reply_content');
        const counter = document.getElementById('staff-char-counter');

        if (textarea && counter) {
            function updateCounter() {
                const length = textarea.value.length;
                counter.textContent = length + ' / 3000';
                if (length > 2700) {
                    counter.classList.add('text-danger');
                    counter.classList.remove('text-muted');
                } else {
                    counter.classList.remove('text-danger');
                    counter.classList.add('text-muted');
                }
            }

            textarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
</script>
@endsection
