<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Iimaan Dry Cleaner — Professional Laundry Services in Hargeisa')</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0ea5e9;
            --accent: #f59e0b;
            --dark: #1e293b;
            --muted: #64748b;
            --light: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1),
                      0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.07),
                         0 2px 4px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1),
                         0 4px 6px rgba(0,0,0,0.05);
            --radius: 8px;
            --radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--white);
            color: var(--dark);
            line-height: 1.6;
            min-vh: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar-website {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }

        .navbar-brand-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary) !important;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .navbar-brand-logo span {
            color: var(--accent);
        }

        .nav-link-custom {
            color: var(--dark) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            border-radius: var(--radius);
            transition: var(--transition);
            text-decoration: none;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--primary) !important;
            background: rgba(37,99,235,0.08);
        }

        .btn-nav-login {
            color: var(--primary) !important;
            border: 1.5px solid var(--primary);
            border-radius: var(--radius);
            padding: 0.4rem 1.2rem !important;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-nav-login:hover {
            background: var(--primary);
            color: white !important;
        }

        .btn-nav-register {
            background: var(--primary);
            color: white !important;
            border-radius: var(--radius);
            padding: 0.4rem 1.2rem !important;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            text-decoration: none;
            border: 1.5px solid var(--primary);
        }

        .btn-nav-register:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Logged-in nav dropdown */
        .user-dropdown-btn {
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 50px;
            padding: 0.3rem 1rem 0.3rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--dark) !important;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Footer */
        .footer-website {
            background: var(--dark);
            color: #94a3b8;
            padding: 4rem 0 2rem;
            margin-top: auto;
        }

        .footer-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.75rem;
        }

        .footer-brand span {
            color: var(--accent);
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer-link:hover {
            color: white;
        }

        .footer-heading {
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-divider {
            border-color: #334155;
            margin: 2rem 0 1.5rem;
        }

        /* Flash messages */
        .alert-website {
            border: none;
            border-radius: var(--radius);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Scroll to top button */
        .scroll-top-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 44px;
            height: 44px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            z-index: 999;
        }

        .scroll-top-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
        
        .fw-600 {
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar-website">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between w-100">

                <!-- Brand -->
                <a href="{{ route('home') }}" class="navbar-brand-logo">
                    Iimaan<span>.</span>
                </a>

                <!-- Desktop Nav Links (hidden on mobile) -->
                <div class="d-none d-lg-flex align-items-center gap-1">
                    <a href="{{ route('home') }}" class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('services.index') }}" class="nav-link-custom {{ request()->routeIs('services*') ? 'active' : '' }}">
                        Services
                    </a>
                    <a href="{{ route('about') }}" class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}">
                        About Us
                    </a>
                    <a href="{{ route('reviews.public') }}" class="nav-link-custom {{ request()->routeIs('reviews*') ? 'active' : '' }}">
                        Reviews
                    </a>
                    <a href="{{ route('contact.create') }}" class="nav-link-custom {{ request()->routeIs('contact*') ? 'active' : '' }}">
                        Contact
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="d-none d-lg-flex align-items-center gap-2">
                    @auth
                        <!-- Logged in: show user dropdown -->
                        <div class="dropdown">
                            <a class="user-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                Welcome back, {{ explode(' ', Auth::user()->name)[0] }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width:220px; border-radius:12px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-600 text-dark small">{{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size:0.8rem">{{ Auth::user()->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.orders.index') }}">
                                        <i class="fas fa-shopping-bag me-2 text-primary"></i>My Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.payments.index') }}">
                                        <i class="fas fa-credit-card me-2 text-primary"></i>Payments
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.notifications.index') }}">
                                        <i class="fas fa-bell me-2 text-primary"></i>Notifications
                                        @php
                                            $unread = \App\Models\Notification::where('user_id', Auth::id())
                                                ->where('is_read', false)->count();
                                        @endphp
                                        @if($unread > 0)
                                            <span class="badge bg-danger rounded-pill ms-1 small">{{ $unread }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.support.index') }}">
                                        <i class="fas fa-headset me-2 text-primary"></i>Support
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('customer.profile.edit') }}">
                                        <i class="fas fa-user-cog me-2 text-primary"></i>Profile
                                    </a>
                                </li>
                                <li class="border-top">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <!-- Place Order CTA (logged in) -->
                        <a href="{{ route('customer.orders.create') }}" class="btn-nav-register">
                            <i class="fas fa-plus me-1"></i>New Order
                        </a>
                    @else
                        <!-- Not logged in -->
                        <a href="{{ route('login') }}" class="btn-nav-login">Login</a>
                        <a href="{{ route('register') }}" class="btn-nav-register">Get Started</a>
                    @endauth
                </div>

                <!-- Mobile hamburger button -->
                <button class="btn d-lg-none border-0 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">
                    <i class="fas fa-bars fa-lg text-dark"></i>
                </button>

            </div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Nav -->
    <div class="offcanvas offcanvas-end" id="mobileNav" style="max-width: 280px;">
        <div class="offcanvas-header border-bottom">
            <span class="navbar-brand-logo">Iimaan<span>.</span></span>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between">
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('home') }}" class="nav-link-custom py-2 {{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
                <a href="{{ route('services.index') }}" class="nav-link-custom py-2 {{ request()->routeIs('services*') ? 'active' : '' }}">
                    Services
                </a>
                <a href="{{ route('about') }}" class="nav-link-custom py-2 {{ request()->routeIs('about') ? 'active' : '' }}">
                    About Us
                </a>
                <a href="{{ route('reviews.public') }}" class="nav-link-custom py-2 {{ request()->routeIs('reviews*') ? 'active' : '' }}">
                    Reviews
                </a>
                <a href="{{ route('contact.create') }}" class="nav-link-custom py-2 {{ request()->routeIs('contact*') ? 'active' : '' }}">
                    Contact
                </a>
            </div>
            <div class="d-flex flex-column gap-2 pt-3 border-top">
                @auth
                    <div class="mb-3">
                        <div class="fw-600 text-dark small">Welcome back, {{ Auth::user()->name }}</div>
                        <div class="text-muted" style="font-size:0.8rem">{{ Auth::user()->email }}</div>
                    </div>
                    <a class="nav-link-custom py-2" href="{{ route('customer.orders.index') }}">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>My Orders
                    </a>
                    <a class="nav-link-custom py-2" href="{{ route('customer.payments.index') }}">
                        <i class="fas fa-credit-card me-2 text-primary"></i>Payments
                    </a>
                    <a class="nav-link-custom py-2" href="{{ route('customer.notifications.index') }}">
                        <i class="fas fa-bell me-2 text-primary"></i>Notifications
                        @if($unread > 0)
                            <span class="badge bg-danger rounded-pill ms-1 small">{{ $unread }}</span>
                        @endif
                    </a>
                    <a class="nav-link-custom py-2" href="{{ route('customer.support.index') }}">
                        <i class="fas fa-headset me-2 text-primary"></i>Support
                    </a>
                    <a class="nav-link-custom py-2" href="{{ route('customer.profile.edit') }}">
                        <i class="fas fa-user-cog me-2 text-primary"></i>Profile
                    </a>
                    <a href="{{ route('customer.orders.create') }}" class="btn-nav-register text-center my-2">
                        <i class="fas fa-plus me-1"></i>New Order
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-100">
                        @csrf
                        <button type="submit" class="btn-nav-login w-100 text-center text-danger border-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-nav-login text-center">Login</a>
                    <a href="{{ route('register') }}" class="btn-nav-register text-center">Get Started</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-website alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-website alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-website alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-website">
        <div class="container">
            <div class="row g-4">
                <!-- Col 1 — Brand -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-brand">Iimaan<span>.</span></div>
                    <p class="small mb-3">Professional laundry and dry cleaning services in Hargeisa, Somaliland.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white opacity-75 hover-opacity-100"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white opacity-75 hover-opacity-100"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white opacity-75 hover-opacity-100"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>

                <!-- Col 2 — Quick Links -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <a href="{{ route('home') }}" class="footer-link">Home</a>
                    <a href="{{ route('services.index') }}" class="footer-link">Services</a>
                    <a href="{{ route('about') }}" class="footer-link">About Us</a>
                    <a href="{{ route('reviews.public') }}" class="footer-link">Reviews</a>
                    <a href="{{ route('contact.create') }}" class="footer-link">Contact</a>
                </div>

                <!-- Col 3 — Customer Portal -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Customer Portal</h5>
                    @auth
                        <a href="{{ route('customer.orders.index') }}" class="footer-link">My Orders</a>
                        <a href="{{ route('customer.payments.index') }}" class="footer-link">My Payments</a>
                        <a href="{{ route('customer.reviews.index') }}" class="footer-link">My Reviews</a>
                        <a href="{{ route('customer.support.index') }}" class="footer-link">Support</a>
                        <a href="{{ route('customer.profile.edit') }}" class="footer-link">Profile</a>
                    @else
                        <a href="{{ route('login') }}" class="footer-link">Login</a>
                        <a href="{{ route('register') }}" class="footer-link">Register</a>
                        <a href="{{ route('login') }}" class="footer-link">Place Order</a>
                    @endauth
                </div>

                <!-- Col 4 — Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Contact Info</h5>
                    <p class="small mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Hargeisa, Somaliland</p>
                    <p class="small mb-2"><i class="fas fa-phone me-2 text-primary"></i>+252-63-4444444</p>
                    <p class="small mb-2"><i class="fas fa-envelope me-2 text-primary"></i>info@iimaan.com</p>
                    <p class="small mb-0"><i class="fas fa-clock me-2 text-primary"></i>Sun–Thu: 8am–8pm</p>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <span class="small">© 2024 Iimaan Dry Cleaner. All rights reserved.</span>
                <span class="small">Built with <i class="fas fa-heart text-danger"></i> for Hargeisa</span>
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <button class="scroll-top-btn" id="scrollTopBtn" title="Go to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss flash messages after 5 seconds
            const alerts = document.querySelectorAll('.alert-website');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Scroll to top functionality
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    scrollTopBtn.style.display = 'flex';
                } else {
                    scrollTopBtn.style.display = 'none';
                }
            });

            scrollTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
