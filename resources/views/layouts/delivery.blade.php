<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LOMS') }} - Delivery Panel</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.0 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, #d35400 0%, #e67e22 100%);
            --sidebar-hover: rgba(255, 255, 255, 0.15);
            --sidebar-active-bg: #ffffff;
            --sidebar-active-text: #d35400;
            --sidebar-active-border: #e67e22;
            --text-light: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        /* Desktop Sidebar styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 280px;
            background: var(--sidebar-bg);
            color: var(--text-light);
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-brand {
            padding: 24px;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background-color: rgba(0, 0, 0, 0.05);
        }

        .sidebar-user {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.6);
            padding: 16px 24px 8px;
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
            border-left-color: rgba(255, 255, 255, 0.25);
        }

        .sidebar-link.active {
            background-color: var(--sidebar-active-bg);
            color: var(--sidebar-active-text) !important;
            border-left-color: var(--sidebar-active-border);
            font-weight: 600;
        }

        /* Main Content wrapper */
        #page-content-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        /* Avatar circle styles */
        .avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.35);
        }

        .avatar-circle-dark {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #d35400;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Mobile specific adjustments */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                display: none;
            }
            #page-content-wrapper {
                margin-left: 0;
            }
        }

        /* Custom badge sizes */
        .badge-count {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 20px;
        }

        /* Offcanvas customizations */
        .offcanvas-sidebar {
            background: var(--sidebar-bg);
            color: var(--text-light);
            width: 280px !important;
        }
        .offcanvas-sidebar .btn-close-white {
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <!-- Desktop Sidebar (LG Screens and up) -->
    <div id="sidebar-wrapper" class="d-none d-lg-block">
        <div class="sidebar-brand d-flex align-items-center gap-2">
            <i class="fa-solid fa-truck fs-4 text-white"></i>
            <span>LOMS Delivery Panel</span>
        </div>
        
        <div class="sidebar-user d-flex align-items-center gap-3">
            <div class="avatar-circle">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size: 0.95rem;">{{ Auth::user()->name }}</div>
                <div class="text-white-50 text-truncate" style="font-size: 0.8rem;">Delivery Agent</div>
            </div>
        </div>

        <div class="sidebar-heading">Transit Operations</div>
        
        <nav class="nav flex-column">
            <a href="{{ route('delivery.dashboard') }}" class="sidebar-link {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('delivery.orders.index') }}" class="sidebar-link {{ request()->routeIs('delivery.orders.index') && !request()->has('status') ? 'active' : '' }}">
                <i class="fa-solid fa-shipping-fast"></i>
                <span>My Deliveries</span>
            </a>
            
            <a href="{{ route('delivery.orders.index', ['status' => 'active']) }}" class="sidebar-link {{ request()->routeIs('delivery.orders.index') && request()->get('status') === 'active' ? 'active' : '' }}">
                <i class="fa-solid fa-map-marker-alt"></i>
                <span class="me-auto">Active Deliveries</span>
                <span class="badge bg-white text-dark badge-count fw-bold">
                    {{ \App\Models\Order::where('delivery_agent_id', Auth::id())
                        ->where(function($q) {
                            $q->where('payment_method', 'cash')
                              ->orWhere(function($q2) {
                                  $q2->whereIn('payment_method', ['zaad', 'edahab'])
                                     ->where('payment_status', 'verified');
                                  });
                            })
                        ->whereIn('status', ['pending_pickup','picked_up_from_customer',
                                             'ready_for_delivery','picked_up_from_laundry','on_the_way'])
                        ->count() }}
                </span>
            </a>

            <a href="{{ route('delivery.orders.index', ['status' => 'delivered']) }}" class="sidebar-link {{ request()->routeIs('delivery.orders.index') && request()->get('status') === 'delivered' ? 'active' : '' }}">
                <i class="fa-solid fa-check-double"></i>
                <span>Completed</span>
            </a>

            <div class="sidebar-heading">Settings & Alerts</div>

            <a href="{{ route('delivery.notifications.index') }}" class="sidebar-link {{ request()->routeIs('delivery.notifications.index') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i>
                <span class="me-auto">Notifications</span>
                @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                    <span class="badge bg-danger badge-count text-white" style="{{ $unreadNotificationCount == 0 ? 'display: none;' : '' }}">
                        {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('delivery.profile.edit') }}" class="sidebar-link {{ request()->routeIs('delivery.profile.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-user-cog"></i>
                <span>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" id="desktop-logout-form" class="d-none">
                @csrf
            </form>
            <a href="#" class="sidebar-link text-white-50 mt-4" onclick="event.preventDefault(); document.getElementById('desktop-logout-form').submit();">
                <i class="fa-solid fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Mobile Navigation Sidebar (Offcanvas) -->
    <div class="offcanvas offcanvas-start offcanvas-sidebar d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header border-bottom border-white-10">
            <h5 class="offcanvas-title d-flex align-items-center gap-2 fw-bold" id="mobileSidebarLabel">
                <i class="fa-solid fa-truck text-white"></i>
                <span>LOMS Delivery Panel</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="sidebar-user d-flex align-items-center gap-3">
                <div class="avatar-circle">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-white-50" style="font-size: 0.8rem;">Delivery Agent</div>
                </div>
            </div>

            <div class="sidebar-heading">Transit Operations</div>
            
            <nav class="nav flex-column">
                <a href="{{ route('delivery.dashboard') }}" class="sidebar-link {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar')).hide();">
                    <i class="fa-solid fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('delivery.orders.index') }}" class="sidebar-link {{ request()->routeIs('delivery.orders.index') && !request()->has('status') ? 'active' : '' }}" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar')).hide();">
                    <i class="fa-solid fa-shipping-fast"></i>
                    <span>My Deliveries</span>
                </a>
                
                <a href="{{ route('delivery.orders.index', ['status' => 'active']) }}" class="sidebar-link {{ request()->routeIs('delivery.orders.index') && request()->get('status') === 'active' ? 'active' : '' }}" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar')).hide();">
                    <i class="fa-solid fa-map-marker-alt"></i>
                    <span class="me-auto">Active Deliveries</span>
                    <span class="badge bg-white text-dark badge-count fw-bold">
                        {{ \App\Models\Order::where('delivery_agent_id', Auth::id())
                            ->where(function($q) {
                                $q->where('payment_method', 'cash')
                                  ->orWhere(function($q2) {
                                      $q2->whereIn('payment_method', ['zaad', 'edahab'])
                                         ->where('payment_status', 'verified');
                                  });
                            })
                            ->whereIn('status', ['pending_pickup','picked_up_from_customer',
                                                 'ready_for_delivery','picked_up_from_laundry','on_the_way'])
                            ->count() }}
                    </span>
                </a>

                <a href="{{ route('delivery.orders.index', ['status' => 'delivered']) }}" class="sidebar-link {{ request()->routeIs('delivery.orders.index') && request()->get('status') === 'delivered' ? 'active' : '' }}" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar')).hide();">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Completed</span>
                </a>

                <div class="sidebar-heading">Settings & Alerts</div>

                <a href="{{ route('delivery.notifications.index') }}" class="sidebar-link {{ request()->routeIs('delivery.notifications.index') ? 'active' : '' }}" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar')).hide();">
                    <i class="fa-solid fa-bell"></i>
                    <span class="me-auto">Notifications</span>
                    @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        <span class="badge bg-danger badge-count text-white" style="{{ $unreadNotificationCount == 0 ? 'display: none;' : '' }}">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('delivery.profile.edit') }}" class="sidebar-link {{ request()->routeIs('delivery.profile.edit') ? 'active' : '' }}" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar')).hide();">
                    <i class="fa-solid fa-user-cog"></i>
                    <span>Profile</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" id="mobile-logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" class="sidebar-link text-white-50 mt-4" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                    <i class="fa-solid fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div id="page-content-wrapper">
        
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <!-- Mobile Sidebar Toggle -->
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h4 class="mb-0 fw-bold text-dark d-none d-sm-block">Delivery Panel</h4>
                <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-semibold d-none d-md-inline-block">
                    <i class="fa-solid fa-truck-ramp-box me-1"></i> Today's Deliveries: 
                    {{ Auth::user()->assignedDeliveryOrders()->where('status', 'delivered')->whereDate('delivery_time', \Carbon\Carbon::today())->count() }}
                </span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Live Clock and Date -->
                <div class="text-end d-none d-md-block me-2">
                    <div class="fw-semibold text-secondary" style="font-size: 0.9rem;" id="live-date">
                        {{ now()->format('l, M d, Y') }}
                    </div>
                    <div class="text-muted small" style="font-size: 0.8rem;" id="live-time">
                        {{ now()->format('h:i:s A') }}
                    </div>
                </div>

                <!-- Notification Bell Dropdown -->
                <div class="dropdown me-2">
                    <a href="#" class="btn btn-link position-relative text-secondary p-0" id="deliveryNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg"></i>
                        @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size: 0.65rem; {{ $unreadNotificationCount == 0 ? 'display: none;' : '' }}">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-0 text-start" aria-labelledby="deliveryNotificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom bg-light rounded-top-3">
                            <span class="fw-bold text-dark small">Notifications</span>
                            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                                <form action="{{ route('delivery.notifications.markAllRead') }}" method="POST" class="m-0">
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
                                    <form action="{{ route('delivery.notifications.markRead', $notification) }}" method="POST" class="m-0">
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
                            <a href="{{ route('delivery.notifications.index') }}" class="text-primary text-decoration-none fw-semibold small">
                                View All Notifications &rarr;
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Small Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link link-dark text-decoration-none dropdown-toggle fw-semibold d-flex align-items-center gap-2 p-0 border-0" type="button" id="topbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-circle-dark">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3 mt-2" aria-labelledby="topbarDropdown">
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('delivery.profile.edit') }}">
                                <i class="fa-solid fa-user-cog text-muted"></i>Profile Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider opacity-25"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger d-flex align-items-center gap-2" href="#" onclick="event.preventDefault(); document.getElementById('desktop-logout-form').submit();">
                                <i class="fa-solid fa-sign-out-alt"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Yield Body -->
        <main class="flex-grow-1 p-4">
            
            <!-- Flash Messages -->
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

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 mt-auto text-center">
            <div class="container-fluid">
                <span class="text-muted small">LOMS &copy; 2024 &mdash; Delivery Portal</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Layout Actions: Clock & Alert auto-dismiss -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('#flash-message-container .alert');
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 4000);
            });

            // Live Time updates
            const timeEl = document.getElementById('live-time');
            const dateEl = document.getElementById('live-date');
            
            if (timeEl && dateEl) {
                setInterval(function () {
                    const now = new Date();
                    
                    // Format time
                    let hours = now.getHours();
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    timeEl.textContent = `${hours}:${minutes}:${seconds} ${ampm}`;

                    // Format date
                    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
                    dateEl.textContent = now.toLocaleDateString('en-US', options);
                }, 1000);
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
