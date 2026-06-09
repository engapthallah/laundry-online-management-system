<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use Symfony\Component\HttpFoundation\Response;

class ShareNotificationCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $unreadCount = Notification::where('user_id', Auth::id())
                                       ->where('is_read', false)
                                       ->count();
            View::share('unreadNotificationCount', $unreadCount);

            $recentNotifications = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->with('order')
                ->latest()
                ->take(5)
                ->get();
            View::share('recentNotifications', $recentNotifications);

            // Share pending support count for admins
            $pendingSupportCount = 0;
            if (Auth::user()->isAdmin()) {
                $pendingSupportCount = \App\Models\SupportMessage::where('status', 'pending')->count();
            }
            View::share('pendingSupportCount', $pendingSupportCount);
        } else {
            View::share('pendingSupportCount', 0);
        }

        return $next($request);
    }
}
