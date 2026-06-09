@extends('layouts.admin')

@section('content')
<div class="container py-2">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6">
            <h3 class="fw-bold text-dark mb-1">Notifications Audit</h3>
            <p class="text-secondary mb-0">
                {{ $unreadNotificationCount ?? 0 }} unread notifications (all system audit trail)
            </p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                <form method="POST" action="{{ route('admin.notifications.markAllRead') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary fw-semibold rounded-3 shadow-sm px-4">
                        <i class="fa-solid fa-check-double me-1"></i> Mark All My Notifications Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs border-bottom mb-4">
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ request('filter') === null ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.notifications.index') }}">
                All Notifications
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ request('filter') === 'unread' ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}">
                Unread
                @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                    <span class="badge bg-danger ms-1">{{ $unreadNotificationCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ request('filter') === 'system' ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.notifications.index', ['filter' => 'system']) }}">
                System
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ request('filter') === 'email' ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.notifications.index', ['filter' => 'email']) }}">
                Email
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ request('filter') === 'sms' ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.notifications.index', ['filter' => 'sms']) }}">
                SMS
            </a>
        </li>
    </ul>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3">
            @if($notifications->isEmpty())
                <div class="text-center py-5 my-3">
                    <div class="mb-3 text-secondary">
                        <i class="fa-solid fa-bell-slash fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-dark fw-bold mb-1">No notifications yet</h5>
                    <p class="text-secondary mb-0">You're all caught up!</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        @php
                            $icon = 'fa-bell text-primary';
                            if ($notification->type === 'email') {
                                $icon = 'fa-envelope text-success';
                            } elseif ($notification->type === 'sms') {
                                $icon = 'fa-sms text-warning';
                            }
                            // Only clickable to mark read if it belongs to this admin
                            $isMine = ($notification->user_id === Auth::id());
                        @endphp
                        <div class="list-group-item list-group-item-action p-3 mb-2 border rounded shadow-sm clickable-item position-relative"
                             style="background-color: {{ $notification->is_read ? '#ffffff' : '#e8f4fd' }}; border-left: {{ $notification->is_read ? '1px solid #dee2e6' : '4px solid #0d6efd' }} !important; cursor: {{ $isMine ? 'pointer' : 'default' }}; transition: all 0.2s;"
                             onclick="if({{ $isMine ? 'true' : 'false' }}) { event.target.tagName !== 'A' && event.target.tagName !== 'BUTTON' && event.target.closest('form') === null && document.getElementById('mark-read-form-{{ $notification->id }}').submit(); }">
                             
                            @if($isMine)
                                <!-- Form to submit on click -->
                                <form id="mark-read-form-{{ $notification->id }}" action="{{ route('admin.notifications.markRead', $notification) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endif

                            <div class="d-flex">
                                <div class="me-3 fs-4 d-flex align-items-center">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                                <div class="w-100">
                                    <div class="d-flex w-100 justify-content-between align-items-baseline mb-1">
                                        <h5 class="mb-0 fw-bold fs-6 text-dark">{{ $notification->title }}</h5>
                                        <span class="text-muted small" title="{{ $notification->created_at->format('M d, Y h:i A') }}">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-secondary text-wrap" style="font-size: 0.9rem; line-height: 1.4;">
                                        {{ $notification->message }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                        <div>
                                            <!-- Target User Info -->
                                            @if($notification->user)
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 fs-8 me-2">
                                                    <i class="fa-solid fa-user me-1"></i>{{ $notification->user->name }} ({{ ucfirst($notification->user->role) }})
                                                </span>
                                            @endif

                                            <!-- Associated Order -->
                                            @if($notification->order_id && $notification->order)
                                                <a href="{{ route('admin.orders.show', $notification->order_id) }}" class="badge bg-primary-subtle text-primary text-decoration-none border border-primary-subtle px-2.5 py-1.5 fs-8">
                                                    <i class="fa-solid fa-hashtag me-1"></i>{{ $notification->order->order_number }}
                                                </a>
                                            @endif
                                        </div>
                                        <div>
                                            @if($isMine && !$notification->is_read)
                                                <form action="{{ route('admin.notifications.markRead', $notification) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary py-1 px-3 fw-semibold rounded-pill" style="font-size: 0.75rem;">
                                                        <i class="fa-solid fa-check me-1"></i>Mark as Read
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
