@extends('layouts.website')

@section('title', 'Iimaan Dry Cleaner — Professional Laundry Services in Hargeisa')

@section('content')
<!-- Custom Page CSS -->
<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #fefce8 100%);
        padding: 6rem 0;
        position: relative;
        overflow: hidden;
    }

    .hero-badge {
        background: rgba(37,99,235,0.1);
        color: var(--primary);
        padding: 0.4rem 1.2rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .hero-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 800;
        line-height: 1.15;
        color: var(--dark);
        margin-bottom: 1.5rem;
    }

    .hero-sub {
        color: var(--muted);
        font-size: 1.1rem;
        margin-bottom: 2.5rem;
        max-width: 540px;
    }

    .btn-pill {
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-pill-primary {
        background: var(--primary);
        color: white;
        border: 2px solid var(--primary);
    }

    .btn-pill-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    .btn-pill-outline {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .btn-pill-outline:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    .trust-indicator {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--muted);
    }

    .trust-icon {
        color: var(--primary);
        margin-right: 0.25rem;
    }

    .hero-stat-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        border: none;
        transition: var(--transition);
        overflow: hidden;
    }

    .hero-stat-card:hover {
        transform: translateY(-4px);
    }

    /* How It Works Section */
    .section-padding {
        padding: 5rem 0;
    }

    .section-title {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .section-sub {
        color: var(--muted);
        margin-bottom: 3.5rem;
    }

    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(37,99,235,0.1);
        color: var(--primary);
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .step-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 1.25rem;
    }

    .how-it-works-step {
        position: relative;
    }

    @media (min-width: 992px) {
        .how-it-works-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 25%;
            right: -15%;
            width: 30%;
            border-top: 2px dashed var(--border);
            z-index: 1;
        }
    }

    /* Services Section */
    .service-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        border: none;
        transition: var(--transition);
        height: 100%;
    }

    .service-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
    }

    .service-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(37,99,235,0.1);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* Why Choose Us Section */
    .why-choose-us {
        background: var(--primary);
        color: white;
    }

    .why-title {
        color: white !important;
    }

    .why-sub {
        color: rgba(255,255,255,0.8);
    }

    .feature-card {
        background: rgba(255,255,255,0.1);
        border-radius: var(--radius);
        padding: 1.75rem;
        transition: var(--transition);
        border: 1px solid rgba(255,255,255,0.1);
        height: 100%;
    }

    .feature-card:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }

    .feature-icon {
        font-size: 2rem;
        color: var(--accent);
        margin-bottom: 1rem;
    }

    /* Reviews Section */
    .review-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        transition: var(--transition);
        height: 100%;
    }

    .review-card:hover {
        box-shadow: var(--shadow-md);
    }

    .review-stars {
        color: var(--accent);
        margin-bottom: 0.75rem;
    }

    .review-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 4rem 0;
    }

    .btn-cta-white {
        background: white;
        color: var(--primary);
        border: 2px solid white;
    }

    .btn-cta-white:hover {
        background: rgba(255,255,255,0.9);
        color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-cta-outline {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-cta-outline:hover {
        background: white;
        color: var(--primary);
        transform: translateY(-2px);
    }

    /* Stats Bar */
    .stats-bar {
        background: var(--dark);
        color: white;
        padding: 3.5rem 0;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--accent);
        margin-bottom: 0.25rem;
        line-height: 1;
    }

    .stat-label {
        color: #94a3b8;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<!-- SECTION 1 — Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left Hero Text -->
            <div class="col-lg-6">
                <span class="hero-badge">✨ Professional Laundry Services</span>
                <h1 class="hero-title">
                    Fresh & Clean,<br>Delivered to <span style="color:var(--primary)">Your Door</span>
                </h1>
                <p class="hero-sub text-muted">
                    Hargeisa's most trusted laundry and dry cleaning service. We pick up, clean, and deliver your clothes with care — so you can focus on what matters most.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    @auth
                        <a href="{{ route('customer.orders.create') }}" class="btn btn-pill btn-pill-primary">Book a Pickup</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-pill btn-pill-primary">Book a Pickup</a>
                    @endauth
                    <a href="{{ route('services.index') }}" class="btn btn-pill btn-pill-outline">View Our Services</a>
                </div>
                <div class="row g-3">
                    <div class="col-sm-4">
                        <span class="trust-indicator"><i class="fas fa-check-circle trust-icon"></i> Free pickup & delivery</span>
                    </div>
                    <div class="col-sm-4">
                        <span class="trust-indicator"><i class="fas fa-check-circle trust-icon"></i> 24-hour turnaround</span>
                    </div>
                    <div class="col-sm-4">
                        <span class="trust-indicator"><i class="fas fa-check-circle trust-icon"></i> Mobile money payments</span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual -->
            <div class="col-lg-6">
                <div class="card hero-stat-card p-4">
                    <h4 class="fw-bold mb-4 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>Our Track Record</h4>
                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-3">🧺</div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $ordersCompleted }}+</h5>
                                <p class="text-muted mb-0 small">Orders Completed</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-3">😊</div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $happyCustomers }}+</h5>
                                <p class="text-muted mb-0 small">Happy Customers</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-3">⭐</div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ number_format($avgRating, 1) }} / 5.0</h5>
                                <p class="text-muted mb-0 small">Average Customer Rating</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-3">🚚</div>
                            <div>
                                <h5 class="mb-0 fw-bold">Same-Day Delivery</h5>
                                <p class="text-muted mb-0 small">Available across Hargeisa</p>
                            </div>
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                            Place Your First Order →
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                            Join our happy customers →
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2 — How It Works -->
<section class="section-padding bg-white">
    <div class="container text-center">
        <h2 class="section-title">How It Works</h2>
        <p class="section-sub">Simple, fast, and reliable</p>

        <div class="row g-4 justify-content-center">
            <!-- Step 1 -->
            <div class="col-lg-3 col-md-6 how-it-works-step">
                <div class="step-number">1</div>
                <i class="fas fa-calendar-check step-icon"></i>
                <h5 class="fw-bold mb-2">Schedule Pickup</h5>
                <p class="text-muted small px-3">Choose your services, select a pickup time, and place your order online.</p>
            </div>

            <!-- Step 2 -->
            <div class="col-lg-3 col-md-6 how-it-works-step">
                <div class="step-number">2</div>
                <i class="fas fa-truck step-icon"></i>
                <h5 class="fw-bold mb-2">We Collect</h5>
                <p class="text-muted small px-3">Our team picks up your laundry from your doorstep at the scheduled time.</p>
            </div>

            <!-- Step 3 -->
            <div class="col-lg-3 col-md-6 how-it-works-step">
                <div class="step-number">3</div>
                <i class="fas fa-tshirt step-icon"></i>
                <h5 class="fw-bold mb-2">Expert Cleaning</h5>
                <p class="text-muted small px-3">Your clothes are professionally cleaned, pressed, and carefully packaged.</p>
            </div>

            <!-- Step 4 -->
            <div class="col-lg-3 col-md-6">
                <div class="step-number">4</div>
                <i class="fas fa-home step-icon"></i>
                <h5 class="fw-bold mb-2">Delivered Clean</h5>
                <p class="text-muted small px-3">Fresh laundry delivered back to your door, ready to wear.</p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 3 — Services Preview -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Services</h2>
            <p class="section-sub mb-0">Professional care for every garment</p>
        </div>

        @if($services->isEmpty())
            <div class="text-center py-4 text-muted">
                <p>Services coming soon. Check back!</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-md-4">
                        <div class="card service-card p-4">
                            <div class="card-body p-0 d-flex flex-column h-100">
                                <div class="service-icon-wrapper">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <h5 class="fw-bold mb-2 text-dark">{{ $service->name }}</h5>
                                <p class="text-muted small flex-grow-1 mb-4">{{ \Illuminate\Support\Str::limit($service->description, 100) }}</p>
                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">
                                        @if($service->price_per_kg > 0)
                                            From ${{ $service->price_per_kg }}/kg
                                        @else
                                            From ${{ $service->price_per_item }}/item
                                        @endif
                                    </span>
                                    <a href="{{ route('services.index') }}" class="text-decoration-none small fw-semibold text-secondary">
                                        Learn More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('services.index') }}" class="btn btn-pill btn-pill-outline">View All Services</a>
            </div>
        @endif
    </div>
</section>

<!-- SECTION 4 — Why Choose Us -->
<section class="section-padding why-choose-us">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title why-title">Why Hargeisa Trusts Us</h2>
            <p class="why-sub">Committed to premium quality, speed, and customer satisfaction</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h5 class="fw-bold mb-2">Safe & Secure</h5>
                    <p class="small mb-0 text-white-50">Your clothes are tracked throughout the entire cleaning process from collection to delivery.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <i class="fas fa-clock feature-icon"></i>
                    <h5 class="fw-bold mb-2">On-Time Delivery</h5>
                    <p class="small mb-0 text-white-50">We respect your time. Laundry is delivered fresh back to your door within the promised timeframe.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <i class="fas fa-mobile-alt feature-icon"></i>
                    <h5 class="fw-bold mb-2">Easy Payments</h5>
                    <p class="small mb-0 text-white-50">Pay securely with Zaad, Edahab, or cash on delivery. Fast mobile money integration.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <i class="fas fa-star feature-icon"></i>
                    <h5 class="fw-bold mb-2">Quality Guaranteed</h5>
                    <p class="small mb-0 text-white-50">We use premium eco-friendly detergents and professional machines to ensure garments look new.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <i class="fas fa-map-marker-alt feature-icon"></i>
                    <h5 class="fw-bold mb-2">Hargeisa Coverage</h5>
                    <p class="small mb-0 text-white-50">We service all major neighborhoods in Hargeisa with free collection and delivery services.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <i class="fas fa-headset feature-icon"></i>
                    <h5 class="fw-bold mb-2">24/7 Support</h5>
                    <p class="small mb-0 text-white-50">Our customer care staff are always available to handle custom instructions or orders.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 5 — Customer Reviews -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-sub mb-0">Real reviews from real people</p>
        </div>

        @if($reviews->isEmpty())
            <!-- Dummy Testimonials -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="review-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-muted italic small mb-4">"Excellent service! My clothes were cleaned perfectly and returned on time. The pickup and delivery service is so convenient."</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="review-avatar">A</div>
                            <div>
                                <h6 class="mb-0 fw-bold small text-dark">Ahmed</h6>
                                <span class="text-muted" style="font-size:0.75rem;">Verified Customer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="review-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-muted italic small mb-4">"Best dry cleaner in Hargeisa. The Zaad payment method integration works flawlessly and delivery is always on time."</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="review-avatar">F</div>
                            <div>
                                <h6 class="mb-0 fw-bold small text-dark">Faduma</h6>
                                <span class="text-muted" style="font-size:0.75rem;">Verified Customer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="review-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-muted italic small mb-4">"Professional service and excellent quality. I highly recommend Iimaan Dry Cleaner to everyone in the city."</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="review-avatar">M</div>
                            <div>
                                <h6 class="mb-0 fw-bold small text-dark">Mohamed</h6>
                                <span class="text-muted" style="font-size:0.75rem;">Verified Customer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($reviews as $review)
                    <div class="col-md-4">
                        <div class="review-card">
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                                @endfor
                                <span class="text-dark fw-bold ms-1 small">{{ number_format($review->rating, 1) }}</span>
                            </div>
                            <p class="text-muted italic small mb-4">"{{ $review->comment }}"</p>
                            <div class="d-flex align-items-center gap-3">
                                <div class="review-avatar">
                                    {{ strtoupper(substr($review->customer->name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold small text-dark">{{ explode(' ', $review->customer->name ?? 'Anonymous')[0] }}</h6>
                                    <span class="text-muted" style="font-size:0.75rem;">
                                        Verified Customer • {{ $review->created_at->format('M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-5">
            <div class="d-inline-block p-3 bg-light rounded-3 mb-4">
                <span class="fs-4 fw-extrabold text-dark me-2">{{ number_format($avgRating, 1) }}</span>
                <span class="review-stars me-2">
                    @php
                        $starsFloor = floor($avgRating);
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $starsFloor)
                            <i class="fas fa-star"></i>
                        @elseif($i - 0.5 <= $avgRating)
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </span>
                <span class="text-muted small">Based on real customer feedback</span>
            </div>
            <div>
                <a href="{{ route('reviews.public') }}" class="btn btn-pill btn-pill-outline">Read All Reviews</a>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 6 — CTA Banner -->
<section class="cta-section text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready for Fresh Laundry?</h2>
        <p class="lead mb-4 opacity-90 max-width-600 mx-auto">Join hundreds of happy customers in Hargeisa who trust us with their garments.</p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            @auth
                <a href="{{ route('customer.orders.create') }}" class="btn btn-pill btn-cta-white">Book Now — It's Free</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-pill btn-cta-white">Book Now — It's Free</a>
            @endauth
            <a href="tel:+252634444444" class="btn btn-pill btn-cta-outline"><i class="fas fa-phone me-2"></i>Call Us: +252-63-4444444</a>
        </div>
    </div>
</section>

<!-- SECTION 7 — Stats Bar -->
<section class="stats-bar text-center">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-number">{{ $ordersCompleted }}+</div>
                <div class="stat-label">Orders Completed</div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-number">{{ $happyCustomers }}+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-number">{{ $servicesCount }}</div>
                <div class="stat-label">Services Available</div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-number">{{ number_format($avgRating, 1) }} / 5</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>
    </div>
</section>
@endsection
