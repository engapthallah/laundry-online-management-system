@extends('layouts.website')

@section('title', 'About Us — Iimaan Dry Cleaner')

@section('content')
<!-- Page Hero Section -->
<section style="background: linear-gradient(135deg, #eff6ff, #f0f9ff); padding: 4rem 0;">
    <div class="container text-center text-md-start">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 justify-content-center justify-content-md-start">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
        <h1 class="fw-extrabold display-5 text-dark mb-2">About Iimaan Dry Cleaner</h1>
        <p class="text-muted lead mb-0">Our story, our mission, our values</p>
    </div>
</section>

<!-- SECTION 1 — Our Story -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <!-- Left Text Content -->
            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2" style="font-size: 0.85rem; border-radius: 50px;">
                    Est. 2023 — Hargeisa, Somaliland
                </span>
                <h2 class="fw-bold text-dark mb-4">Built for the People of Hargeisa</h2>
                <p class="text-muted mb-3">
                    Iimaan Dry Cleaner was founded in 2023 with a simple mission: to bring professional laundry and dry cleaning services to the people of Hargeisa through technology and dedication.
                </p>
                <p class="text-muted mb-3">
                    We noticed that most laundry businesses in Hargeisa relied on manual, paper-based methods — making it hard for customers to track their orders, pay digitally, or get timely updates. We set out to change that.
                </p>
                <p class="text-muted mb-0">
                    Today, we serve hundreds of customers across Hargeisa, offering seamless online ordering, real-time tracking, and mobile money payments through Zaad and Edahab.
                </p>
            </div>

            <!-- Right Stats Card -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg p-4 bg-light" style="border-radius: 16px;">
                    <div class="row g-4 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm">
                                <h3 class="fw-extrabold text-primary mb-1">2023</h3>
                                <p class="text-muted small mb-0">Year Founded</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm">
                                <h3 class="fw-extrabold text-primary mb-1">500+</h3>
                                <p class="text-muted small mb-0">Orders Completed</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm">
                                <h3 class="fw-extrabold text-primary mb-1">98%</h3>
                                <p class="text-muted small mb-0">Satisfaction Rate</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm">
                                <h3 class="fw-extrabold text-primary mb-1">2</h3>
                                <p class="text-muted small mb-0">Mobile Payments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2 — Our Mission & Vision -->
<section class="py-5" style="background: var(--light);">
    <div class="container py-3">
        <div class="row g-4">
            <!-- Mission Card -->
            <div class="col-md-6">
                <div class="card h-100 border-0 border-start border-4 border-primary shadow-sm p-4 bg-white" style="border-radius: 8px;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-primary"><i class="fas fa-bullseye"></i></div>
                        <h4 class="fw-bold mb-0 text-dark">Our Mission</h4>
                    </div>
                    <p class="text-muted mb-0 small">
                        To make professional laundry services accessible to every household in Hargeisa through reliable technology, quality service, and affordable pricing.
                    </p>
                </div>
            </div>

            <!-- Vision Card -->
            <div class="col-md-6">
                <div class="card h-100 border-0 border-start border-4 border-warning shadow-sm p-4 bg-white" style="border-radius: 8px;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-3 text-warning"><i class="fas fa-eye"></i></div>
                        <h4 class="fw-bold mb-0 text-dark">Our Vision</h4>
                    </div>
                    <p class="text-muted mb-0 small">
                        To become Somaliland's leading digital laundry platform, connecting customers with trusted cleaning professionals across all major cities.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 3 — Our Values -->
<section class="py-5 bg-white">
    <div class="container py-4 text-center">
        <h2 class="fw-bold text-dark mb-5">Our Core Values</h2>
        <div class="row g-4">
            <!-- Value 1 -->
            <div class="col-md-4">
                <div class="p-4 rounded-3 border border-light shadow-sm h-100">
                    <div class="text-danger fs-1 mb-3"><i class="fas fa-heart"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Customer First</h5>
                    <p class="text-muted mb-0 small">We tailor our schedule and options to match your convenience. Customer service is our topmost priority.</p>
                </div>
            </div>

            <!-- Value 2 -->
            <div class="col-md-4">
                <div class="p-4 rounded-3 border border-light shadow-sm h-100">
                    <div class="text-warning fs-1 mb-3"><i class="fas fa-star"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Quality Always</h5>
                    <p class="text-muted mb-0 small">We utilize premium washing solutions, modern processes, and strict inspections to maintain clothes fresh and pristine.</p>
                </div>
            </div>

            <!-- Value 3 -->
            <div class="col-md-4">
                <div class="p-4 rounded-3 border border-light shadow-sm h-100">
                    <div class="text-success fs-1 mb-3"><i class="fas fa-shield-alt"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Trust & Transparency</h5>
                    <p class="text-muted mb-0 small">No hidden fees, real-time progress tracking, secure mobile payments, and full support at all stages.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 4 — Our Team -->
<section class="py-5" style="background: var(--light);">
    <div class="container py-4 text-center">
        <h2 class="fw-bold text-dark mb-5">Meet Our Team</h2>
        <div class="row g-4">
            <!-- Member 1 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.75rem; font-weight: 700;">
                        AM
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Ahmed M.</h5>
                    <p class="text-primary small mb-3">Founder & Manager</p>
                    <p class="text-muted small mb-0">Driving our vision forward, overseeing clean strategies and local growth.</p>
                </div>
            </div>

            <!-- Member 2 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.75rem; font-weight: 700;">
                        KD
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Khader D.</h5>
                    <p class="text-success small mb-3">Head of Operations</p>
                    <p class="text-muted small mb-0">Managing machinery, quality assurance, collection fleets, and schedules.</p>
                </div>
            </div>

            <!-- Member 3 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.75rem; font-weight: 700;">
                        AY
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Asma Y.</h5>
                    <p class="text-warning small mb-3">Customer Service Lead</p>
                    <p class="text-muted small mb-0">Resolving support tickets, answering calls, and ensuring user happiness.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 5 — Location -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <h2 class="fw-bold text-dark mb-5 text-center">Find Us in Hargeisa</h2>
        <div class="row g-4 align-items-center">
            <!-- Left Info Card -->
            <div class="col-lg-5">
                <div class="card border-0 shadow p-4 bg-light" style="border-radius: 12px;">
                    <h4 class="fw-bold text-dark mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Store Information</h4>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <span class="fs-5 text-primary"><i class="fas fa-map-marker-alt"></i></span>
                            <div>
                                <h6 class="mb-0 fw-bold">Address</h6>
                                <p class="text-muted small mb-0">Main Street, Shaab Area, Hargeisa, Somaliland</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span class="fs-5 text-primary"><i class="fas fa-phone"></i></span>
                            <div>
                                <h6 class="mb-0 fw-bold">Phone</h6>
                                <p class="text-muted small mb-0">+252-63-4444444</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span class="fs-5 text-primary"><i class="fas fa-envelope"></i></span>
                            <div>
                                <h6 class="mb-0 fw-bold">Email</h6>
                                <p class="text-muted small mb-0">info@iimaan.com</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span class="fs-5 text-primary"><i class="fas fa-clock"></i></span>
                            <div>
                                <h6 class="mb-0 fw-bold">Business Hours</h6>
                                <p class="text-muted small mb-0">Sunday – Thursday: 8:00 AM – 8:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Map Placeholder -->
            <div class="col-lg-7">
                <div class="d-flex flex-column align-items-center justify-content-center bg-light border" style="height: 320px; border-radius: 12px;">
                    <div class="text-primary fs-1 mb-2"><i class="fas fa-map-marked-alt"></i></div>
                    <h5 class="fw-bold text-dark">Hargeisa, Somaliland</h5>
                    <p class="text-muted small px-4 text-center">Shaab Area Branch Office Location. Drop-in visitors are welcome!</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
