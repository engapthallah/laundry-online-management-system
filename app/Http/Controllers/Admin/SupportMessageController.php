<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupportMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = SupportMessage::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        return view('admin.support.index', compact('messages'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SupportMessage $support): View
    {
        return view('admin.support.show', compact('support'));
    }

    /**
     * Reply to the support message.
     */
    public function reply(Request $request, SupportMessage $support): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string'],
        ]);

        $support->admin_reply = $validated['admin_reply'];
        $support->status = 'resolved';
        $support->replied_at = now();
        $support->save();

        // Send notification to customer
        \App\Services\NotificationService::supportMessageReplied($support);

        return redirect()->route('admin.support.show', $support->id)->with('success', 'Reply submitted and ticket marked as resolved.');
    }

    /**
     * Update the status of the specified support message.
     */
    public function updateStatus(Request $request, SupportMessage $support): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,resolved,ignored'],
        ]);

        $support->status = $validated['status'];
        if ($validated['status'] === 'pending') {
            $support->admin_reply = null;
            $support->replied_at = null;
        }
        $support->save();

        return back()->with('success', "Ticket status updated to " . ucfirst($validated['status']) . ".");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupportMessage $support): RedirectResponse
    {
        $support->delete();

        return redirect()->route('admin.support.index')->with('success', 'Support message deleted successfully.');
    }
}
