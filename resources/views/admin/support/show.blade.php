@extends('layouts.admin')

@section('content')
<div class="container py-2">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.support.index') }}" class="text-decoration-none text-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to All Messages
        </a>
    </div>

    <!-- Section 1: Message Header -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <!-- Left Details -->
                <div class="col-md-7 border-md-end">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($support->status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-semibold">Awaiting Reply</span>
                        @elseif($support->status === 'resolved')
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-semibold">Resolved</span>
                        @elseif($support->status === 'ignored')
                            <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill fw-semibold">Ignored</span>
                        @endif
                        <span class="text-muted small">Support Ticket #SUP-{{ str_pad($support->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-3">{{ $support->subject }}</h3>
                    <div class="d-flex flex-column gap-1 text-secondary small">
                        <div>
                            <i class="fa-regular fa-calendar-plus me-2 text-primary"></i><strong>Submitted:</strong> {{ $support->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div>
                            <i class="fa-regular fa-clock me-2 text-primary"></i><strong>Replied At:</strong> 
                            @if($support->replied_at)
                                <span class="text-success fw-semibold">{{ $support->replied_at->format('M d, Y h:i A') }}</span>
                            @else
                                <span class="text-muted">Not yet replied</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Submitter Details -->
                <div class="col-md-5">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-regular fa-user me-2 text-primary"></i>Customer Information</h5>
                    <div class="d-flex flex-column gap-2.5 text-secondary small">
                        <div>
                            <strong>Name:</strong> <span class="text-dark fw-semibold">{{ $support->name }}</span>
                        </div>
                        <div>
                            <strong>Email:</strong> <span class="text-dark">{{ $support->email }}</span>
                        </div>
                        <div>
                            <strong>Phone:</strong> 
                            @if($support->user && $support->user->phone)
                                <span class="text-dark">{{ $support->user->phone }}</span>
                            @else
                                <span class="text-muted">N/A (Guest Submission)</span>
                            @endif
                        </div>
                        <div>
                            <strong>Member Since:</strong> 
                            @if($support->user)
                                <span class="text-dark">{{ $support->user->created_at->format('M Y') }}</span>
                            @else
                                <span class="text-muted">N/A (Guest)</span>
                            @endif
                        </div>
                        
                        @if($support->user)
                            <div class="mt-2">
                                <a href="{{ route('admin.users.show', $support->user) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                                    View Customer Profile
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workspace: Thread & Action Panel -->
    <div class="row g-4">
        <!-- Section 2: Thread (Chat layout) -->
        <div class="col-lg-8">
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
                                    {{ $support->name }}
                                </div>
                                <div class="p-3 shadow-sm border text-start text-dark" 
                                     style="background-color: #e3f2fd; max-width: 90%; border-radius: 1.25rem 1.25rem 0 1.25rem; white-space: pre-wrap; font-size: 0.95rem;">
                                    <h6 class="fw-bold text-primary mb-2">{{ $support->subject }}</h6>
                                    {{ $support->message }}
                                </div>
                                <small class="text-muted mt-1" style="font-size: 0.75rem;">
                                    Sent: {{ $support->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                 style="width: 40px; height: 40px; min-width: 40px; font-size: 0.95rem;">
                                {{ strtoupper(substr($support->name, 0, 1)) }}
                            </div>
                        </div>

                        <!-- Admin Reply (Left-aligned bubble) -->
                        @if($support->admin_reply)
                            <div class="d-flex justify-content-start align-items-start gap-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" 
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <div class="d-flex flex-column align-items-start">
                                    <div class="text-success small mb-1 fw-bold">
                                        You Replied:
                                    </div>
                                    <div class="p-3 shadow-sm bg-white border-start border-success border-4 text-dark" 
                                         style="max-width: 90%; border-radius: 0 1.25rem 1.25rem 1.25rem; white-space: pre-wrap; font-size: 0.95rem;">
                                        {{ $support->admin_reply }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            Replied: {{ $support->replied_at ? $support->replied_at->format('M d, Y h:i A') : $support->updated_at->format('M d, Y h:i A') }}
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

        <!-- Section 3: Admin Action Panel -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-gears me-2 text-primary"></i>Manage This Message</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if($support->status === 'pending')
                        <!-- Reply Form -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3">Send Reply to Customer</h6>
                            <form method="POST" action="{{ route('admin.support.reply', $support) }}">
                                @csrf
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="admin_reply" class="form-label small text-muted fw-semibold mb-0">Reply content <span class="text-danger">*</span></label>
                                        <small id="admin-char-counter" class="text-muted">0 / 3000</small>
                                    </div>
                                    <textarea name="admin_reply" 
                                              id="admin_reply" 
                                              rows="6" 
                                              class="form-control @error('admin_reply') is-invalid @enderror" 
                                              placeholder="Type your response here. Be clear, professional, and helpful."
                                              required 
                                              minlength="10" 
                                              maxlength="3000">{{ old('admin_reply') }}</textarea>
                                    @error('admin_reply')
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

                        <hr class="opacity-25">

                        <!-- Quick Actions -->
                        <div>
                            <h6 class="fw-bold text-dark mb-3">Quick Actions</h6>
                            <button type="button" class="btn btn-secondary w-100 fw-bold py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#ignoreModal">
                                <i class="fa-solid fa-circle-minus me-2"></i>Mark as Ignored
                            </button>
                        </div>
                    
                    @elseif($support->status === 'resolved')
                        <!-- Resolved Status Info -->
                        <div class="alert alert-success border-0 rounded-4 p-3.5 mb-4 text-center">
                            <div class="text-success mb-2">
                                <i class="fa-solid fa-circle-check fa-2x animate-bounce"></i>
                            </div>
                            <h6 class="fw-bold mb-1">This message has been resolved.</h6>
                            <p class="small mb-0 text-success-emphasis opacity-90">
                                Replied on {{ $support->replied_at ? $support->replied_at->format('M d, Y h:i A') : $support->updated_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        
                        <!-- Reopen Action -->
                        <form action="{{ route('admin.support.updateStatus', $support) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 rounded-3 shadow-sm">
                                <i class="fa-solid fa-envelope-open me-2"></i>Reopen Message
                            </button>
                        </form>

                    @elseif($support->status === 'ignored')
                        <!-- Ignored Status Info -->
                        <div class="alert alert-secondary border-0 rounded-4 p-3.5 mb-4 text-center">
                            <div class="text-secondary mb-2">
                                <i class="fa-solid fa-circle-minus fa-2x"></i>
                            </div>
                            <h6 class="fw-bold mb-1">This message was marked as ignored.</h6>
                            <p class="small mb-0 text-secondary-emphasis opacity-90">
                                It was closed without sending an admin reply.
                            </p>
                        </div>

                        <!-- Reopen Action -->
                        <form action="{{ route('admin.support.updateStatus', $support) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 rounded-3 shadow-sm">
                                <i class="fa-solid fa-envelope-open me-2"></i>Reopen Message
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Action Forms -->
@if($support->status === 'pending')
    <!-- Ignore Form -->
    <form id="ignore-form" action="{{ route('admin.support.updateStatus', $support) }}" method="POST" class="d-none">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="ignored">
    </form>

    <!-- Ignore Confirmation Bootstrap Modal -->
    <div class="modal fade" id="ignoreModal" tabindex="-1" aria-labelledby="ignoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="ignoreModalLabel">
                        <i class="fa-solid fa-circle-exclamation text-warning me-2"></i>Mark Message as Ignored?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <p class="text-secondary">Are you sure you want to mark this message as ignored? No reply will be sent to the customer.</p>
                    <p class="text-muted small">You can reopen this support message back to pending status at any time later.</p>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" onclick="document.getElementById('ignore-form').submit();">Confirm & Ignore</button>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('admin_reply');
        const counter = document.getElementById('admin-char-counter');

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
