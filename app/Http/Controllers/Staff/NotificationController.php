<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of staff notifications.
     */
    public function index(Request $request): View
    {
        $query = Notification::where('user_id', Auth::id())->with('order');

        // Apply filters
        $filter = $request->get('filter');
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif (in_array($filter, ['system', 'email', 'sms'])) {
            $query->where('type', $filter);
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        return view('staff.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and redirect.
     */
    public function markRead(Notification $notification): RedirectResponse
    {
        // Ownership Check
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized notification access.');
        }

        $notification->is_read = true;
        $notification->save();

        if ($notification->order_id) {
            return redirect()->route('staff.orders.show', $notification->order_id);
        }

        return redirect()->route('staff.notifications.index');
    }

    /**
     * Mark all notifications for the authenticated staff as read.
     */
    public function markAllRead(): RedirectResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications have been marked as read.');
    }
}
