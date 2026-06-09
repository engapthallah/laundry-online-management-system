<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LOMS') }} - Customer Portal</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.0 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light min-vh-100 d-flex flex-col flex-column">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3 sticky-top">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-primary" href="{{ route('customer.dashboard') }}">
                <i class="fa-solid fa-soap me-2"></i>LOMS
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#customerNavbar" aria-controls="customerNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="customerNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-2">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('customer.dashboard') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('customer.dashboard') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('customer.orders.create') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('customer.orders.create') }}">
                            New Order
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('customer.orders.index') || request()->routeIs('customer.orders.show') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('customer.orders.index') }}">
                            My Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('customer.support.*') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('customer.support.index') }}">
                            Support
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('customer.reviews.*') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('customer.reviews.index') }}">
                            My Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('reviews.public') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('reviews.public') }}">
                            Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('contact.create') ? 'text-primary active' : 'text-secondary' }}" href="{{ route('contact.create') }}">
                            Contact Us
                        </a>
                    </li>
                </ul>

                <!-- Right Side Actions -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Notification Bell Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="btn btn-link position-relative text-secondary p-0" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell fa-lg"></i>
                            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      style="font-size: 0.65rem; {{ $unreadNotificationCount == 0 ? 'display: none;' : '' }}">
                                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-0" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom bg-light rounded-top-3">
                                <span class="fw-bold text-dark small">Notifications</span>
                                @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                                    <form action="{{ route('customer.notifications.markAllRead') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0 text-primary fw-semibold" style="font-size: 0.75rem;">
                                            Mark All Read
                                        </button>
                                    </form>
                                @endif
                            </li>
                            @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                                @foreach($recentNotifications as $notification)
                                    <li>
                                        <form action="{{ route('customer.notifications.markRead', $notification) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2.5 px-3 border-bottom d-block text-start w-100" style="background-color: {{ $notification->is_read ? '#ffffff' : '#e8f4fd' }}; border: none; white-space: normal;">
                                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                                    <strong class="text-dark small text-truncate d-inline-block" style="max-width: 180px;">{{ \Illuminate\Support\Str::limit($notification->title, 40) }}</strong>
                                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-muted small text-truncate" style="max-width: 280px; font-size: 0.75rem;">
                                                    {{ \Illuminate\Support\Str::limit($notification->message, 60) }}
                                                </div>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            @else
                                <li class="px-3 py-4 text-center text-muted border-bottom">
                                    <i class="fa-solid fa-bell-slash fa-2x mb-2 opacity-50 text-secondary"></i>
                                    <p class="mb-0 small">No unread notifications</p>
                                </li>
                            @endif
                            <li class="rounded-bottom-3 text-center py-2 bg-light">
                                <a href="{{ route('customer.notifications.index') }}" class="text-primary text-decoration-none fw-semibold small">
                                    View All Notifications &rarr;
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-link link-dark text-decoration-none dropdown-toggle fw-semibold d-flex align-items-center gap-2 p-0 border-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3 mt-2" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('customer.profile.edit') }}">
                                    <i class="fa-regular fa-user text-muted"></i>Profile Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-25"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-sign-out-alt"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-grow-1 container py-5">
        <!-- Flash Messages Container -->
        <div class="row" id="flash-message-container">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation fs-5"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                            <div>{{ session('warning') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Page Yield Content -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center">
            <span class="text-muted small">© 2024 LOMS — Laundry Online Management System</span>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-dismiss flash messages after 4 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('#flash-message-container .alert');
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 4000);
            });
        });
    </script>

    <!-- Page Specific Script Yield -->
    @yield('scripts')
</body>
</html>
