<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupportController extends Controller
{
    /**
     * Display a listing of the customer's support messages.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing filter variables.
     * @return \Illuminate\View\View The support messages index page.
     */
    public function index(Request $request): View
    {
        $query = SupportMessage::where('user_id', Auth::id());

        if ($request->filled('status') && in_array($request->status, ['pending', 'resolved', 'ignored'])) {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(10)->withQueryString();

        return view('customer.support.index', compact('messages'));
    }

    /**
     * Show the form for creating a new support message.
     *
     * @return \Illuminate\View\View The support ticket creation form.
     */
    public function create(): View
    {
        $user = Auth::user();
        return view('customer.support.create', compact('user'));
    }

    /**
     * Store a newly created support message in storage and notify admin.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing message parameters.
     * @return \Illuminate\Http\RedirectResponse Redirect response to support dashboard with success feedback.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        $user = Auth::user();

        $support = SupportMessage::create([
            'user_id' => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        try {
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => "New Support Message — " . $support->subject,
                    'message' => $user->name . " sent a new support message: \"" . $support->subject . "\"",
                    'type'    => 'system',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify admin on support message: " . $e->getMessage());
        }

        return redirect()->route('customer.support.index')
            ->with('success', 'Your message has been sent. We will reply within 24 business hours.');
    }

    /**
     * Display the specified support message and its thread.
     *
     * @param \App\Models\SupportMessage $support The support message instance via route model binding.
     * @return \Illuminate\View\View The support message thread detail page.
     */
    public function show(SupportMessage $support): View
    {
        if ($support->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customer.support.show', compact('support'));
    }
}
