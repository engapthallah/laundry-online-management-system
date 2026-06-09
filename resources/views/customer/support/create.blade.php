@extends('layouts.customer')

@section('content')
<div class="container py-2">
    <!-- Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('customer.support.index') }}" class="text-decoration-none text-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to My Messages
        </a>
    </div>

    <!-- Page Title -->
    <h2 class="fw-bold text-dark mb-4">New Support Message</h2>

    <div class="row g-4">
        <!-- LEFT COLUMN: Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-regular fa-envelope me-2 text-primary"></i>Send a Support Message</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('customer.support.store') }}" id="support-form">
                        @csrf

                        <!-- User Profile Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold text-uppercase">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-regular fa-user"></i></span>
                                    <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>Your account information is used to identify your message.</small>
                            </div>
                        </div>

                        <!-- Predefined Subject Quick-Select Buttons -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Quick Select Subject</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1.5" data-subject="Order Issue">Order Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1.5" data-subject="Payment Issue">Payment Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1.5" data-subject="Delivery Issue">Delivery Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1.5" data-subject="General Inquiry">General Inquiry</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1.5" data-subject="Complaint">Complaint</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1.5" data-subject="Other">Other</button>
                            </div>
                        </div>

                        <!-- Subject Input -->
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-bold text-dark">Subject <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   placeholder="e.g. Issue with my order, Payment question, Service inquiry..."
                                   value="{{ old('subject') }}"
                                   required 
                                   maxlength="255">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden Input for Pre-selected Subject -->
                        <input type="hidden" name="selected_subject" id="selected_subject" value="{{ old('selected_subject') }}">

                        <!-- Message Textarea -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="message" class="form-label fw-bold text-dark mb-0">Message <span class="text-danger">*</span></label>
                                <small class="text-muted fw-semibold" id="char-counter">0 / 2000</small>
                            </div>
                            <textarea name="message" 
                                      id="message" 
                                      class="form-control @error('message') is-invalid @enderror" 
                                      rows="8" 
                                      placeholder="Please describe your issue or question in detail. Include your order number if applicable."
                                      required 
                                      minlength="20" 
                                      maxlength="2000">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-4 py-2.5 rounded-3 fw-semibold">
                                <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                            </button>
                            <a href="{{ route('customer.support.index') }}" class="text-decoration-none text-muted fw-semibold">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Info Panel -->
        <div class="col-lg-4">
            <!-- Support Information Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-regular fa-clock me-2 text-primary"></i>Support Information</h5>
                </div>
                <div class="card-body">
                    <!-- Business Hours -->
                    <h6 class="fw-bold text-dark mb-3">Business Hours</h6>
                    <ul class="list-unstyled mb-4 d-flex flex-column gap-2.5">
                        <li class="d-flex align-items-center text-secondary small">
                            <i class="fa-regular fa-clock me-2.5 text-primary fs-5"></i>
                            Sunday — Thursday: 8am – 8pm
                        </li>
                        <li class="d-flex align-items-center text-secondary small">
                            <i class="fa-regular fa-clock me-2.5 text-danger fs-5"></i>
                            Friday: Closed
                        </li>
                        <li class="d-flex align-items-center text-secondary small">
                            <i class="fa-regular fa-clock me-2.5 text-primary fs-5"></i>
                            Saturday: 9am – 6pm
                        </li>
                    </ul>

                    <!-- Response Time -->
                    <h6 class="fw-bold text-dark mb-3">Expected Response Time</h6>
                    <div class="d-flex align-items-start gap-2.5 mb-4 text-secondary small">
                        <i class="fa-solid fa-reply text-primary fs-5 mt-0.5"></i>
                        <span>We typically reply within 24 business hours.</span>
                    </div>

                    <!-- Contact Alternatives -->
                    <h6 class="fw-bold text-dark mb-3">Alternative Contact Channels</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2.5">
                        <li class="d-flex align-items-center text-secondary small">
                            <i class="fa-solid fa-phone me-2.5 text-primary fs-5"></i>
                            Call: +252-XX-XXXXXXX
                        </li>
                        <li class="d-flex align-items-center text-secondary small">
                            <i class="fa-solid fa-map-marker-alt me-2.5 text-primary fs-5"></i>
                            Visit: Hargeisa, Somaliland
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Helpful Tips Card -->
            <div class="alert alert-info border-0 shadow-sm rounded-4 p-3 mb-0" role="alert">
                <div class="d-flex gap-3">
                    <i class="fa-solid fa-lightbulb text-info fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-info-emphasis mb-1">Faster Resolution Tips</h6>
                        <p class="mb-0 small text-info-emphasis opacity-90">
                            For faster support, include your order number, payment transaction reference, or specific service requests in the message.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectInput = document.getElementById('subject');
        const hiddenSubjectInput = document.getElementById('selected_subject');
        const quickSelectButtons = document.querySelectorAll('.quick-subject-btn');

        // Quick select subject buttons handler
        quickSelectButtons.forEach(button => {
            button.addEventListener('click', function () {
                const value = this.getAttribute('data-subject');
                subjectInput.value = value;
                if (hiddenSubjectInput) {
                    hiddenSubjectInput.value = value;
                }
                
                // Toggle active styling for buttons
                quickSelectButtons.forEach(btn => {
                    btn.classList.remove('btn-secondary', 'text-white');
                    btn.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-secondary', 'text-white');
            });
        });

        // Live Character Counter
        const textarea = document.getElementById('message');
        const counter = document.getElementById('char-counter');

        function updateCounter() {
            const length = textarea.value.length;
            counter.textContent = length + ' / 2000';
            if (length > 1800) {
                counter.classList.add('text-danger');
                counter.classList.remove('text-muted');
            } else {
                counter.classList.remove('text-danger');
                counter.classList.add('text-muted');
            }
        }

        textarea.addEventListener('input', updateCounter);
        // Run once on load to handle pre-filled old input
        updateCounter();
    });
</script>
@endsection
