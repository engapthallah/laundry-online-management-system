<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportController extends Controller
{
    /**
     * Display a listing of support messages with optional status and search filters.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing filters and search queries.
     * @return \Illuminate\View\View The admin support messages index page.
     */
    public function index(Request $request): View
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

        $messages = $query->latest()->paginate(20)->withQueryString();

        // Calculate summary counts for cards
        $totalCount = SupportMessage::count();
        $pendingCount = SupportMessage::where('status', 'pending')->count();
        $resolvedCount = SupportMessage::where('status', 'resolved')->count();
        $ignoredCount = SupportMessage::where('status', 'ignored')->count();

        return view('admin.support.index', compact(
            'messages',
            'totalCount',
            'pendingCount',
            'resolvedCount',
            'ignoredCount'
        ));
    }

    /**
     * Display the specified support message detail page.
     *
     * @param \App\Models\SupportMessage $support The support message model instance.
     * @return \Illuminate\View\View The support message thread and manage page.
     */
    public function show(SupportMessage $support): View
    {
        $support->load('user');
        return view('admin.support.show', compact('support'));
    }

    /**
     * Reply to the support message and mark it as resolved.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request with the reply message.
     * @param \App\Models\SupportMessage $support The support message model instance.
     * @return \Illuminate\Http\RedirectResponse Redirect response to the ticket page.
     */
    public function reply(Request $request, SupportMessage $support): RedirectResponse
    {
        if ($support->status !== 'pending') {
            return redirect()->route('admin.support.show', $support)
                ->with('error', 'This message has already been replied to or processed.');
        }

        $request->validate([
            'admin_reply' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        DB::transaction(function () use ($request, $support) {
            $support->update([
                'admin_reply' => $request->admin_reply,
                'status'      => 'resolved',
                'replied_at'  => now(),
            ]);

            if ($support->user_id) {
                Notification::create([
                    'user_id' => $support->user_id,
                    'title'   => "Support Reply — " . $support->subject,
                    'message' => "Our support team has replied to your message. Log in to read the response.",
                    'type'    => 'system',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        });

        // Try to send email notification to customer
        try {
            if ($support->email) {
                Mail::to($support->email)->send(new \App\Mail\SupportRepliedMail($support));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send support reply email notification: " . $e->getMessage());
        }

        return redirect()->route('admin.support.show', $support)
            ->with('success', 'Reply sent successfully. Message marked as resolved.');
    }

    /**
     * Update the status of the specified support message.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @param \App\Models\SupportMessage $support The support message model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects back or to support show view.
     */
    public function updateStatus(Request $request, SupportMessage $support): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,resolved,ignored'],
        ]);

        $newStatus = $request->status;

        if ($newStatus === 'pending') {
            $support->update([
                'status'      => 'pending',
                'admin_reply' => null,
                'replied_at'  => null,
            ]);
            $message = 'Message reopened and reset to pending.';
        } else {
            $support->update([
                'status' => $newStatus,
            ]);
            $message = 'Message status updated to ' . $newStatus . '.';
        }

        return redirect()->route('admin.support.show', $support)->with('success', $message);
    }

    /**
     * Export support messages as a memory-efficient CSV download.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing current filters.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse Streamed CSV file download.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = SupportMessage::query();

        // Apply filters same as index
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

        $filename = 'loms-support-messages-' . date('Ymd-His') . '.csv';

        return Response::streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Header line
            fputcsv($handle, [
                'ID',
                'Customer Name',
                'Email',
                'Subject',
                'Message',
                'Status',
                'Submitted At',
                'Replied At',
                'Has Reply'
            ]);

            // Query chunking
            $query->chunk(100, function ($messages) use ($handle) {
                foreach ($messages as $message) {
                    fputcsv($handle, [
                        $message->id,
                        $message->name,
                        $message->email,
                        $message->subject,
                        Str::limit($message->message, 100),
                        ucfirst($message->status),
                        $message->created_at ? $message->created_at->format('M d, Y h:i A') : '—',
                        $message->replied_at ? $message->replied_at->format('M d, Y h:i A') : '—',
                        $message->admin_reply ? 'Yes' : 'No'
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv',
            'Cache-Control'       => 'no-cache, must-revalidate',
            'Expires'             => '0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
