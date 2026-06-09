<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact & Support - {{ config('app.name', 'LOMS') }}</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.0 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .quick-subject-btn {
            font-size: 0.85rem;
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="min-vh-100 d-flex flex-column">

    <!-- Public Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-primary" href="/">
                <i class="fa-solid fa-soap me-2"></i>LOMS — Iimaan Dry Cleaner
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="publicNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-3 align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="{{ route('reviews.public') }}">
                            <i class="fa-solid fa-star text-warning me-1"></i>Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-primary active" href="{{ route('contact.create') }}">
                            <i class="fa-solid fa-envelope me-1"></i>Contact Us
                        </a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary fw-bold px-4 rounded-3">
                            <i class="fa-solid fa-gauge me-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary fw-semibold px-3 rounded-3">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary fw-bold px-3 rounded-3">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Hero -->
    <header class="bg-light py-5 border-bottom">
        <div class="container text-center">
            <h1 class="display-5 fw-bold text-dark mb-2">Contact & Support</h1>
            <p class="lead text-secondary mb-0">We are here to help. Send us a message and our support team will get back to you.</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5 flex-grow-1">
        
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4 p-3.5" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-check fs-4 text-success"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- LEFT COLUMN — Form -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h5 class="fw-bold text-dark mb-4"><i class="fa-regular fa-paper-plane me-2 text-primary"></i>Send us a Message</h5>
                    
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
                                   class="form-control @error('name') is-invalid @enderror" 
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
                                   class="form-control @error('email') is-invalid @enderror" 
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
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1" data-subject="Order Issue">Order Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1" data-subject="Payment Issue">Payment Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1" data-subject="Delivery Issue">Delivery Issue</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1" data-subject="General Inquiry">General Inquiry</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1" data-subject="Complaint">Complaint</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject-btn rounded-pill px-3 py-1" data-subject="Other">Other</button>
                            </div>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
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
                                      rows="7" 
                                      class="form-control @error('message') is-invalid @enderror" 
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
                            <button type="submit" class="btn btn-primary btn-lg px-4 py-2.5 rounded-3 fw-bold shadow-sm">
                                <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                            </button>
                            <span class="text-muted small"><i class="fa-solid fa-lock me-1 text-success"></i>🔒 Your message is secure</span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN — Contact Info -->
            <div class="col-md-5">
                <!-- Business Info Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-building me-2 text-primary"></i>Our Contact Details</h5>
                    
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3.5">
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fa-solid fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5">Location</h6>
                                <p class="text-secondary small mb-0">Hargeisa, Somaliland</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5">Phone</h6>
                                <p class="text-secondary small mb-0">+252-XX-XXXXXXX</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5">Email</h6>
                                <p class="text-secondary small mb-0">support@loms.com</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5">Operating Hours</h6>
                                <p class="text-secondary small mb-1">Sun–Thu: 8am–8pm | Sat: 9am–6pm</p>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Friday: Closed</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Response Time Alert -->
                <div class="alert alert-info border-0 shadow-sm rounded-4 p-3.5 mb-4" role="alert">
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-clock-rotate-left text-info fs-4"></i>
                        <div>
                            <h6 class="fw-bold text-info-emphasis mb-1">Typical Response Time</h6>
                            <p class="mb-0 small text-info-emphasis opacity-90">
                                We typically respond within 24 business hours during working days.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Logged In Info Banner -->
                @auth
                    <div class="card border-0 shadow-sm rounded-4 bg-primary-subtle border-start border-primary border-4 p-3.5">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="text-primary mt-0.5">
                                <i class="fa-solid fa-user-check fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-primary-emphasis mb-1">Logged In: {{ Auth::user()->name }}</h6>
                                <p class="mb-0 small text-primary-emphasis opacity-90">
                                    Your message will be automatically linked to your customer account, allowing you to track responses directly in your support dashboard.
                                </p>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-muted small">
            <p class="mb-1">© 2024 Iimaan Dry Cleaner — LOMS</p>
            <p class="mb-0">Hargeisa, Somaliland | info@loms.com</p>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page Scripts -->
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
</body>
</html>
