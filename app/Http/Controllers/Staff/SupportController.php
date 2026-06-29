<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SupportController extends Controller
{
    /**
     * Display a listing of support messages with optional status and search filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = SupportMessage::with('user');

        // Apply filters
        if ($request->filled('status') && in_array($request->status, ['pending', 'resolved', 'ignored'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('staff.support.index', compact('messages'));
    }

    /**
     * Display the specified support message.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $message = SupportMessage::with('user')->findOrFail($id);
        return view('staff.support.show', compact('message'));
    }

    /**
     * Reply to the support message and mark it as resolved.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, int $id)
    {
        $request->validate([
            'reply_content' => 'required|string|max:3000',
        ]);

        $message = SupportMessage::findOrFail($id);

        if ($message->status !== 'pending') {
            return redirect()->route('staff.support.show', $message->id)
                ->with('error', 'This message has already been replied to or processed.');
        }

        DB::transaction(function () use ($request, $message) {
            $message->update([
                'admin_reply' => $request->reply_content,
                'status'      => 'resolved',
                'replied_at'  => Carbon::now(),
            ]);

            if ($message->user_id) {
                Notification::create([
                    'user_id' => $message->user_id,
                    'title'   => "Support Reply — " . $message->subject,
                    'message' => "Our support team has replied to your message. Log in to read the response.",
                    'type'    => 'system',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        });

        // Try to send email notification to customer
        try {
            if ($message->email) {
                Mail::to($message->email)->send(new \App\Mail\SupportRepliedMail($message));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send support reply email notification: " . $e->getMessage());
        }

        return redirect()->route('staff.support.index')
            ->with('success', 'Reply sent successfully. Ticket has been resolved.');
    }
}
