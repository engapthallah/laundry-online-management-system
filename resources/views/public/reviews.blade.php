<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Reviews - {{ config('app.name', 'LOMS') }}</title>

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
        .bg-orange {
            background-color: #fd7e14 !important;
        }
        .star-gold {
            color: #ffc107;
        }
        .star-muted {
            color: #dee2e6;
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
                        <a class="nav-link fw-semibold text-primary active" href="{{ route('reviews.public') }}">
                            <i class="fa-solid fa-star text-warning me-1"></i>Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="{{ route('contact.create') }}">
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

    <!-- Hero Section -->
    <header class="bg-light py-5 border-bottom">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-6">
                    <h1 class="display-5 fw-bold text-dark mb-2">What Our Customers Say</h1>
                    <p class="lead text-secondary mb-0">Real reviews from real customers who processed their laundry with us.</p>
                </div>
                
                <!-- Overall Rating Display -->
                <div class="col-12 col-md-6 col-lg-3 text-center">
                    <div class="p-3 bg-white shadow-sm rounded-4 border">
                        <h2 class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['average'], 1) }}</h2>
                        <div class="my-2">
                            @php
                                $avg = $stats['average'];
                                $whole = floor($avg);
                                $fraction = $avg - $whole;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $whole)
                                    <i class="fa-solid fa-star star-gold fs-5"></i>
                                @elseif($i == $whole + 1 && $fraction >= 0.25 && $fraction <= 0.75)
                                    <i class="fa-solid fa-star-half-stroke star-gold fs-5"></i>
                                @elseif($i == $whole + 1 && $fraction > 0.75)
                                    <i class="fa-solid fa-star star-gold fs-5"></i>
                                @else
                                    <i class="fa-regular fa-star star-muted fs-5"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-muted small mb-0">{{ $stats['total'] }} verified reviews</p>
                    </div>
                </div>

                <!-- Rating Distribution Chart -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white shadow-sm rounded-4 border">
                        @php
                            $total = $stats['total'] ?: 1;
                            $pctFive = ($stats['five_star'] / $total) * 100;
                            $pctFour = ($stats['four_star'] / $total) * 100;
                            $pctThree = ($stats['three_star'] / $total) * 100;
                            $pctTwo = ($stats['two_star'] / $total) * 100;
                            $pctOne = ($stats['one_star'] / $total) * 100;
                        @endphp
                        
                        <!-- 5 Star -->
                        <div class="d-flex align-items-center mb-1 text-muted small">
                            <span style="width: 50px;">5 Star</span>
                            <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctFive }}%"></div>
                            </div>
                            <span style="width: 30px;" class="text-end">{{ $stats['five_star'] }}</span>
                        </div>
                        <!-- 4 Star -->
                        <div class="d-flex align-items-center mb-1 text-muted small">
                            <span style="width: 50px;">4 Star</span>
                            <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $pctFour }}%"></div>
                            </div>
                            <span style="width: 30px;" class="text-end">{{ $stats['four_star'] }}</span>
                        </div>
                        <!-- 3 Star -->
                        <div class="d-flex align-items-center mb-1 text-muted small">
                            <span style="width: 50px;">3 Star</span>
                            <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pctThree }}%"></div>
                            </div>
                            <span style="width: 30px;" class="text-end">{{ $stats['three_star'] }}</span>
                        </div>
                        <!-- 2 Star -->
                        <div class="d-flex align-items-center mb-1 text-muted small">
                            <span style="width: 50px;">2 Star</span>
                            <div class="progress flex-grow-1 mx-2" style="style: height: 8px;">
                                <div class="progress-bar bg-orange" role="progressbar" style="width: {{ $pctTwo }}%"></div>
                            </div>
                            <span style="width: 30px;" class="text-end">{{ $stats['two_star'] }}</span>
                        </div>
                        <!-- 1 Star -->
                        <div class="d-flex align-items-center text-muted small">
                            <span style="width: 50px;">1 Star</span>
                            <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pctOne }}%"></div>
                            </div>
                            <span style="width: 30px;" class="text-end">{{ $stats['one_star'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container py-5 flex-grow-1">
        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('reviews.public') }}" class="row g-3 align-items-center">
                    <div class="col-12 col-md-5 d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-bold me-2">Filter Rating:</span>
                        <a href="{{ route('reviews.public', ['rating' => '', 'sort_by' => request('sort_by')]) }}" class="btn btn-sm {{ !request('rating') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">All</a>
                        <a href="{{ route('reviews.public', ['rating' => 5, 'sort_by' => request('sort_by')]) }}" class="btn btn-sm {{ request('rating') == 5 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">5★</a>
                        <a href="{{ route('reviews.public', ['rating' => 4, 'sort_by' => request('sort_by')]) }}" class="btn btn-sm {{ request('rating') == 4 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">4★</a>
                        <a href="{{ route('reviews.public', ['rating' => 3, 'sort_by' => request('sort_by')]) }}" class="btn btn-sm {{ request('rating') == 3 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">3★</a>
                        <a href="{{ route('reviews.public', ['rating' => 2, 'sort_by' => request('sort_by')]) }}" class="btn btn-sm {{ request('rating') == 2 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">2★</a>
                        <a href="{{ route('reviews.public', ['rating' => 1, 'sort_by' => request('sort_by')]) }}" class="btn btn-sm {{ request('rating') == 1 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">1★</a>
                    </div>
                    
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                    
                    <div class="col-12 col-md-4 ms-auto d-flex align-items-center justify-content-md-end gap-2">
                        <label for="sort_by" class="text-muted small fw-bold text-nowrap">Sort By:</label>
                        <select name="sort_by" id="sort_by" class="form-select form-select-sm rounded-3" style="width: 180px;" onchange="this.form.submit()">
                            <option value="recent" {{ request('sort_by') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                            <option value="highest" {{ request('sort_by') == 'highest' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="lowest" {{ request('sort_by') == 'lowest' ? 'selected' : '' }}>Lowest Rated</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reviews Grid -->
        @if($reviews->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 bg-white text-center py-5 px-4">
                <div class="card-body">
                    <i class="fa-solid fa-comments text-muted fa-3x mb-3"></i>
                    <h4 class="fw-bold text-dark mb-2">No reviews yet.</h4>
                    <p class="text-muted mb-4">Be the first to review our service and help others decide!</p>
                    <a href="{{ route('register') }}" class="btn btn-primary fw-bold px-4 rounded-3">Place an Order</a>
                </div>
            </div>
        @else
            @php
                $ratingLabels = [
                    1 => 'Poor',
                    2 => 'Fair',
                    3 => 'Good',
                    4 => 'Very Good',
                    5 => 'Excellent'
                ];
            @endphp
            <div class="row g-4 mb-4">
                @foreach($reviews as $review)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white d-flex flex-column justify-content-between">
                            
                            <!-- Card Header -->
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                <div class="text-warning small">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fa-solid fa-star star-gold"></i>
                                        @else
                                            <i class="far fa-star star-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-muted small">{{ $review->created_at->format('M Y') }}</span>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body px-4 py-3">
                                @if($review->comment)
                                    <p class="mb-0 text-dark small italic">
                                        "{{ $review->comment }}"
                                    </p>
                                @else
                                    <p class="mb-0 text-muted small text-center italic py-2">
                                        ★ Rated us {{ $ratingLabels[$review->rating] ?? 'Good' }}
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer bg-light border-0 px-4 py-3 rounded-bottom-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1">
                                    @php
                                        $firstName = explode(' ', trim($review->customer->name ?? 'Customer'))[0];
                                    @endphp
                                    <span class="text-dark small fw-semibold">
                                        — {{ $firstName }}, verified customer
                                    </span>
                                    
                                    @if($review->order)
                                        <span class="badge bg-secondary-subtle text-secondary small py-1 rounded-pill" style="font-size: 0.7rem;">
                                            {{ $review->order->orderItems->map(fn($item) => $item->service->name ?? 'N/A')->first() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
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
</body>
</html>
