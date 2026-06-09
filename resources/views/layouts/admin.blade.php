<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LOMS') }} - Admin Portal</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.0 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm py-2">
        <div class="container-fluid px-4">
            <!-- Mobile Toggle Button for Sidebar Drawer -->
            <button class="btn btn-primary d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="fa-solid fa-bars fs-5"></i>
            </button>
            
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-soap me-2"></i>LOMS Admin
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto"></div>
            </div>

            <!-- Profile and Notifications area -->
            <div class="d-flex align-items-center gap-3">
                <!-- Notification Bell Dropdown -->
                <div class="dropdown me-2">
                    <a href="#" class="btn btn-link position-relative text-white p-0" id="adminNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg text-white"></i>
                        @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size: 0.65rem; {{ $unreadNotificationCount == 0 ? 'display: none;' : '' }}">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-0 text-start" aria-labelledby="adminNotificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom bg-light rounded-top-3">
                            <span class="fw-bold text-dark small">Notifications</span>
                            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                                <form action="{{ route('admin.notifications.markAllRead') }}" method="POST" class="m-0">
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
                                    <form action="{{ route('admin.notifications.markRead', $notification) }}" method="POST" class="m-0">
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
                            <a href="{{ route('admin.notifications.index') }}" class="text-primary text-decoration-none fw-semibold small">
                                View All Notifications &rarr;
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-white fw-medium d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    <span class="badge bg-danger text-uppercase px-2 py-1 fs-8 fw-semibold">Admin</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Responsive Drawer on Mobile, Fixed Column on Desktop) -->
            <div class="col-md-3 col-lg-2 bg-dark text-white px-0 position-fixed top-0 bottom-0 start-0 z-1 collapse d-md-block min-vh-100 pt-5 mt-4" id="desktopSidebar">
                <!-- Inner Container for Links -->
                <div class="p-3">
                    <div class="mb-3 px-3 text-muted text-uppercase fw-semibold small tracking-wider">Control Panel</div>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : 'text-white-50 hover-bg-secondary' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.dashboard') }}">
                                <i class="fa-solid fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.analytics*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.analytics.index') }}">
                                <i class="fas fa-chart-bar"></i>Analytics
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.users.index') }}">
                                <i class="fa-solid fa-users"></i>User Management
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.services.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.services.index') }}">
                                <i class="fa-solid fa-concierge-bell"></i>Services
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.orders.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.orders.index') }}">
                                <i class="fa-solid fa-shopping-bag"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.delivery.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.delivery.index') }}">
                                <i class="fa-solid fa-truck"></i>Delivery
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('admin.support*') ? 'active bg-primary text-white' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.support.index') }}">
                                <i class="fa-solid fa-envelope"></i>Support Messages
                                @if(isset($pendingSupportCount) && $pendingSupportCount > 0)
                                    <span class="badge bg-warning text-dark ms-auto rounded-pill">
                                        {{ $pendingSupportCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.reviews.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.reviews.index') }}">
                                <i class="fa-solid fa-star"></i>Reviews
                            </a>
                        </li>
                    </ul>

                    <hr class="text-secondary my-4">

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="px-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Offcanvas Sidebar Menu for Mobile Devices -->
            <div class="offcanvas offcanvas-start bg-dark text-white d-md-none" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                <div class="offcanvas-header border-bottom border-secondary">
                    <h5 class="offcanvas-title fw-bold text-primary" id="sidebarMenuLabel">
                        <i class="fa-solid fa-soap me-2"></i>LOMS Admin
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-3">
                    <div class="mb-3 px-3 text-muted text-uppercase fw-semibold small tracking-wider">Control Panel</div>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.dashboard') }}">
                                <i class="fa-solid fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.analytics*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.analytics.index') }}">
                                <i class="fas fa-chart-bar"></i>Analytics
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.users.index') }}">
                                <i class="fa-solid fa-users"></i>User Management
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.services.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.services.index') }}">
                                <i class="fa-solid fa-concierge-bell"></i>Services
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.orders.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.orders.index') }}">
                                <i class="fa-solid fa-shopping-bag"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.delivery.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.delivery.index') }}">
                                <i class="fa-solid fa-truck"></i>Delivery
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('admin.support*') ? 'active bg-primary text-white' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.support.index') }}">
                                <i class="fa-solid fa-envelope"></i>Support Messages
                                @if(isset($pendingSupportCount) && $pendingSupportCount > 0)
                                    <span class="badge bg-warning text-dark ms-auto rounded-pill">
                                        {{ $pendingSupportCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white {{ request()->routeIs('admin.reviews.*') ? 'active bg-primary' : 'text-white-50' }} px-3 py-2.5 d-flex align-items-center gap-3" href="{{ route('admin.reviews.index') }}">
                                <i class="fa-solid fa-star"></i>Reviews
                            </a>
                        </li>
                    </ul>

                    <hr class="text-secondary my-4">

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="px-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content Area -->
            <!-- Offset by 3 cols on desktop sidebar (col-md-3) to prevent overlapping -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-2 offset-md-3 offset-lg-2">
                <!-- Flash Messages Container -->
                <div class="row" id="flash-message-container">
                    <div class="col-12">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle-check fs-5"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation fs-5"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                                    <div>{{ session('warning') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Page Content Yield -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to auto-dismiss flash messages after 4 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Find all alert elements inside the flash message container
            const alerts = document.querySelectorAll('#flash-message-container .alert');
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    // Use Bootstrap's native Alert component to close it
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
