<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LOMS') }} - Dashboard</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Top Navbar -->
    <header class="navbar navbar-dark sticky-top bg-primary flex-md-nowrap p-0 shadow-sm">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5 fw-bold" href="#">
            <i class="fa-solid fa-soap me-2"></i>LOMS
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed end-0 top-0 mt-2 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="w-100"></div>
        
        <div class="navbar-nav flex-row pe-3">
            <div class="nav-item text-nowrap d-flex align-items-center">
                <span class="text-white me-3 d-none d-sm-inline">
                    <i class="fa-regular fa-user me-2"></i>{{ Auth::user()->name }}
                    <span class="badge bg-white text-primary ms-1 text-uppercase">{{ Auth::user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm fw-bold">
                        <i class="fa-solid fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark text-white collapse min-vh-100 pt-3">
                <div class="position-sticky sidebar-sticky">
                    <div class="px-3 mb-4">
                        <h6 class="text-muted text-uppercase fw-bold small">Navigation</h6>
                    </div>
                    <ul class="nav flex-column px-2">
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white {{ request()->routeIs('admin.analytics.index') ? 'bg-primary rounded fw-semibold' : 'text-white-50' }} px-3 py-2 d-flex align-items-center" href="{{ route('admin.analytics.index') }}">
                                    <i class="fa-solid fa-chart-line me-2"></i>Admin Analytics
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-gears me-2"></i>Services
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-cart-shopping me-2"></i>Orders
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-credit-card me-2"></i>Payments
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-users me-2"></i>Users
                                </a>
                            </li>
                        @elseif(Auth::user()->isStaff())
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white {{ request()->routeIs('staff.dashboard') ? 'bg-primary rounded fw-semibold' : 'text-white-50' }} px-3 py-2 d-flex align-items-center" href="{{ route('staff.dashboard') }}">
                                    <i class="fa-solid fa-chart-line me-2"></i>Staff Dashboard
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-folder-open me-2"></i>Manage Orders
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-bell me-2"></i>Notifications
                                </a>
                            </li>
                        @elseif(Auth::user()->isDelivery())
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white {{ request()->routeIs('delivery.dashboard') ? 'bg-primary rounded fw-semibold' : 'text-white-50' }} px-3 py-2 d-flex align-items-center" href="{{ route('delivery.dashboard') }}">
                                    <i class="fa-solid fa-chart-line me-2"></i>Delivery Dashboard
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-truck me-2"></i>Assigned Tasks
                                </a>
                            </li>
                        @elseif(Auth::user()->isCustomer())
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-basket-shopping me-2"></i>New Order
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link text-white text-white-50 px-3 py-2 d-flex align-items-center" href="#">
                                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Order History
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Content Yield -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
