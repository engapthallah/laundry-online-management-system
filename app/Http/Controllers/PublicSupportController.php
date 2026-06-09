<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PublicSupportController extends Controller
{
    /**
     * Show the public contact and support form.
     *
     * @return \Illuminate\View\View The contact form view.
     */
    public function create(): View
    {
        $user = Auth::user();
        $prefilledName = $user ? $user->name : '';
        $prefilledEmail = $user ? $user->email : '';

        return view('public.contact', compact('prefilledName', 'prefilledEmail'));
    }

    /**
     * Store a guest or customer's support submission with honeypot spam protection.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the contact page with status.
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot spam protection: if the hidden website field is filled, silently ignore
        if ($request->filled('website')) {
            \Illuminate\Support\Facades\Log::warning('Honeypot trigger detected public contact submission.');
            return redirect()->route('contact.create');
        }

        $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        $support = SupportMessage::create([
            'user_id' => Auth::id() ?? null,
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        // Notify admin of public form submission
        try {
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => "New Contact Form Submission — " . $support->subject,
                    'message' => $support->name . " (" . $support->email . ") sent a contact form message: \"" . $support->subject . "\"",
                    'type'    => 'system',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify admin on public contact submission: " . $e->getMessage());
        }

        return redirect()->route('contact.create')
            ->with('success', 'Thank you! Your message has been received. We will get back to you within 24 business hours.');
    }
}
