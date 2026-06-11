@extends('layouts.website')

@section('title', 'Our Services — Iimaan Dry Cleaner')

@section('content')
<!-- Page Hero Section -->
<section style="background: linear-gradient(135deg, #eff6ff, #f0f9ff); padding: 4rem 0;">
    <div class="container text-center text-md-start">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 justify-content-center justify-content-md-start">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
        <h1 class="fw-extrabold display-5 text-dark mb-2">Our Services</h1>
        <p class="text-muted lead mb-0">Professional care for every garment</p>
    </div>
</section>

<!-- Services Grid -->
<section class="py-5 bg-white">
    <div class="container py-4">
        @if($services->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-tshirt fa-3x text-muted mb-3"></i>
                <h3 class="fw-bold text-dark">Services Coming Soon</h3>
                <p class="text-muted">We are setting up our services. Please check back later!</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm position-relative h-100" style="border-radius: 16px; overflow: hidden; transition: var(--transition); border-top: 4px solid var(--primary) !important;">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-4" style="width: 3rem; height: 3rem; font-size: 1.25rem;">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">{{ $service->name }}</h4>
                                <p class="text-muted small flex-grow-1 mb-4">{{ $service->description }}</p>
                                
                                <hr class="my-3 text-muted opacity-25">
                                
                                <div class="mb-4">
                                    @if($service->price_per_kg > 0)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">Price per KG:</span>
                                            <span class="fw-bold text-dark">${{ number_format($service->price_per_kg, 2) }}</span>
                                        </div>
                                    @endif
                                    @if($service->price_per_item > 0)
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Price per Item:</span>
                                            <span class="fw-bold text-dark">${{ number_format($service->price_per_item, 2) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    @auth
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                            Order This Service
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                            Order This Service
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Pricing FAQ Section -->
<section class="py-5" style="background: var(--light);">
    <div class="container py-4" style="max-width: 800px;">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark mb-2">Pricing & FAQ</h2>
            <p class="text-muted">Common questions about our laundry process and rates</p>
        </div>

        <div class="accordion accordion-flush shadow-sm rounded-3 overflow-hidden border border-light" id="faqAccordion">
            <!-- FAQ 1 -->
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fw-600 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        How is pricing calculated?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Pricing is calculated depending on the service category. Everyday laundry, bed sheets, and towels are typically weighed and billed per KG. Formal garments, suits, dresses, and delicate items are charged individually per item. The final bill is computed once items are verified at our cleaning facility.
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed fw-600 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Is there a minimum order?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Yes, we require a minimum order value of $5.00 for free pickup and delivery across Hargeisa. Orders below $5.00 can still be scheduled, but a minor transport surcharge may apply depending on your location.
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed fw-600 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        What payment methods do you accept?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        We accept mobile money payments including Telesom ZAAD and Somtel Edahab for seamless, contactless checkout. You can also pay via cash upon delivery if preferred.
                    </div>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed fw-600 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        How long does it take?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Our standard turnaround time is 24 hours from collection to delivery. We also offer same-day express service for orders received before 10:00 AM, subject to a minor express surcharge.
                    </div>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed fw-600 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Do you offer bulk discounts?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Yes! We offer discounted corporate and bulk laundry programs for hotels, restaurants, health clinics, and high-volume households. Please reach out via our Contact Page to discuss custom packages.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; padding: 4rem 0;" class="text-center">
    <div class="container">
        <h2 class="fw-bold mb-3 text-white">Ready for Fresh Laundry?</h2>
        <p class="lead mb-4 opacity-90 max-width-600 mx-auto">Join hundreds of happy customers in Hargeisa who trust us with their garments.</p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            @auth
                <a href="{{ route('customer.orders.create') }}" class="btn btn-pill btn-cta-white px-4 py-2 text-decoration-none" style="border-radius: 50px; background: white; color: var(--primary); font-weight: 600;">Book Now — It's Free</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-pill btn-cta-white px-4 py-2 text-decoration-none" style="border-radius: 50px; background: white; color: var(--primary); font-weight: 600;">Book Now — It's Free</a>
            @endauth
            <a href="tel:+252634444444" class="btn btn-pill btn-cta-outline px-4 py-2 text-decoration-none" style="border-radius: 50px; border: 2px solid white; color: white; font-weight: 600;"><i class="fas fa-phone me-2"></i>Call Us: +252-63-4444444</a>
        </div>
    </div>
</section>
@endsection
