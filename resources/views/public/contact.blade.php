@extends('layouts.website')

@section('title', 'Contact Us — Iimaan Dry Cleaner')

@section('content')
<!-- Custom Styles for Contact Page -->
<style>
    .contact-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #fefce8 100%);
        padding: 4rem 0;
    }
    .contact-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: none;
    }
    .quick-subject-btn {
        font-size: 0.8rem;
        transition: var(--transition);
        border-radius: 50px;
    }
</style>

<!-- Hero Section -->
<section class="contact-hero border-bottom text-center text-md-start">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 justify-content-center justify-content-md-start">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-extrabold text-dark mb-2">Contact & Support</h1>
        <p class="lead text-muted mb-0">We are here to help. Send us a message and our support team will get back to you.</p>
    </div>
</section>

<!-- Main Form Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Left Column: Form -->
            <div class="col-lg-7">
                <div class="contact-card p-4">
                    <h4 class="fw-bold text-dark mb-4"><i class="far fa-paper-plane me-2 text-primary"></i>Send us a Message</h4>
                    
                    <form method="POST" action="{{ route('contact.store') }}" id="contact-form">
                        @csrf
                        
                        <!-- Honeypot Spam Protection -->
                        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                        <!-- Name Input -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold text-dark small">Your Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control rounded-3 py-2 px-3 @error('name') is-invalid @enderror" 
                                   placeholder="Enter your full name" 
                                   value="{{ old('name', $prefilledName) }}" 
                                   required 
                                   maxlength="100">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold text-dark small">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control rounded-3 py-2 px-3 @error('email') is-invalid @enderror" 
                                   placeholder="Enter your email address" 
                                   value="{{ old('email', $prefilledEmail) }}" 
                                   required 
                                   maxlength="255">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject Input with Quick Select Buttons -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="subject" class="form-label fw-bold text-dark small mb-0">Subject <span class="text-danger">*</span></label>
                            </div>
                            <!-- Quick Select Buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn px-3 py-1" data-subject="Order Issue">Order Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn px-3 py-1" data-subject="Payment Issue">Payment Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn px-3 py-1" data-subject="Delivery Issue">Delivery Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn px-3 py-1" data-subject="General Inquiry">General Inquiry</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn px-3 py-1" data-subject="Complaint">Complaint</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn px-3 py-1" data-subject="Other">Other</button>
                            </div>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   class="form-control rounded-3 py-2 px-3 @error('subject') is-invalid @enderror" 
                                   placeholder="e.g. Question about my booking, special pricing query" 
                                   value="{{ old('subject') }}" 
                                   required 
                                   maxlength="255">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message TextArea -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="message" class="form-label fw-bold text-dark small mb-0">Message <span class="text-danger">*</span></label>
                                <small class="text-muted fw-semibold" id="char-counter">0 / 2000</small>
                            </div>
                            <textarea name="message" 
                                      id="message" 
                                      rows="6" 
                                      class="form-control rounded-3 py-2 px-3 @error('message') is-invalid @enderror" 
                                      placeholder="Describe your question or issue in detail here..." 
                                      required 
                                      minlength="20" 
                                      maxlength="2000">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit and Security Indicator -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                            <span class="text-muted small"><i class="fas fa-lock me-1 text-success"></i> Your message is secure</span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Contact Details -->
            <div class="col-lg-5">
                <!-- Our Contact Details -->
                <div class="contact-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4"><i class="fas fa-building me-2 text-primary"></i>Our Contact Details</h5>
                    
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Location</h6>
                                <p class="text-muted small mb-0">Main Street, Shaab Area, Hargeisa, Somaliland</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Phone</h6>
                                <p class="text-muted small mb-0">+252-63-4444444</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Email</h6>
                                <p class="text-muted small mb-0">info@iimaan.com</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Operating Hours</h6>
                                <p class="text-muted small mb-1">Sun–Thu: 8am–8pm | Sat: 9am–6pm</p>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">Friday: Closed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typical Response Alert -->
                <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 mb-4" role="alert" style="background-color: #f0fdf4 !important; border-left: 4px solid #16a34a !important; color: #14532d;">
                    <div class="d-flex gap-3">
                        <span class="fs-4 text-success"><i class="fas fa-history"></i></span>
                        <div>
                            <h6 class="fw-bold mb-1">Typical Response Time</h6>
                            <p class="mb-0 small opacity-90">
                                We review submissions constantly and typically respond within 24 business hours.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Auth Banner -->
                @auth
                    <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color: #f0f9ff; border-left: 4px solid var(--primary) !important;">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="text-primary mt-1">
                                <i class="fas fa-user-check fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Logged In: {{ Auth::user()->name }}</h6>
                                <p class="mb-0 small text-muted">
                                    Your support message will be linked directly to your customer account, allowing you to track agent responses inside your customer portal dashboard under the Support section.
                                </p>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectInput = document.getElementById('subject');
        const quickButtons = document.querySelectorAll('.quick-subject-btn');

        quickButtons.forEach(button => {
            button.addEventListener('click', function () {
                const value = this.getAttribute('data-subject');
                subjectInput.value = value;

                quickButtons.forEach(btn => {
                    btn.classList.remove('btn-secondary', 'text-white');
                    btn.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-secondary', 'text-white');
            });
        });

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
        updateCounter();
    });
</script>
@endsection
