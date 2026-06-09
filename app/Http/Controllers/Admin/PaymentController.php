<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of all payments in the administration panel.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        // Calculate stats
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('paid_at', today())
            ->sum('amount');
        $pendingCount = Payment::where('status', 'pending')->count();
        $failedCount = Payment::where('status', 'failed')->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'pending_count' => $pendingCount,
            'failed_count' => $failedCount,
        ];

        // Build query
        $query = Payment::with(['order.customer', 'user']);

        // Search: order number, customer name, transaction reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_reference', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        // Filter by method
        if ($request->filled('method') && $request->method !== 'all') {
            $query->where('payment_method', $request->method);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display the specified payment detail panel.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function show(Payment $payment): View
    {
        $payment->load(['order.orderItems.service', 'order.customer', 'user']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Mark a pending payment as completed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markComplete(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Only pending payments can be marked as completed.');
        }

        $request->validate([
            'transaction_reference' => 'nullable|string|max:100',
        ]);

        $order = $payment->order;
        $ref = $request->transaction_reference ?? "CASH-{$order->order_number}-" . now()->timestamp;

        DB::transaction(function () use ($payment, $order, $ref) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'transaction_reference' => $ref,
            ]);

            $order->update([
                'payment_status' => 'paid',
            ]);

            // Create notification for customer
            \App\Services\NotificationService::paymentConfirmed($payment);
        });

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment marked as completed successfully.');
    }

    /**
     * Process a refund for a completed payment.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refund(Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'completed') {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Only completed payments can be refunded.');
        }

        if ($payment->order->status === 'delivered') {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Cannot refund payments for delivered orders.');
        }

        $order = $payment->order;

        DB::transaction(function () use ($payment, $order) {
            $payment->update([
                'status' => 'refunded',
            ]);

            $order->update([
                'payment_status' => 'refunded',
            ]);

            // Create notifications for customer and admin
            \App\Services\NotificationService::paymentRefunded($payment);
        });

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment refunded successfully.');
    }

    /**
     * Mark a pending payment as failed.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markFailed(Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Only pending payments can be marked as failed.');
        }

        $order = $payment->order;

        DB::transaction(function () use ($payment, $order) {
            $payment->update([
                'status' => 'failed',
            ]);

            // Create customer notification
            \App\Services\NotificationService::paymentFailed($payment);
        });

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment marked as failed successfully.');
    }

    /**
     * Export the filtered payments list as a CSV stream.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="loms-payments-' . now()->format('Y-m-d') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        return response()->stream(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Payment ID',
                'Order Number',
                'Customer Name',
                'Payment Method',
                'Amount',
                'Status',
                'Transaction Reference',
                'Paid At',
                'Created At'
            ]);

            // Apply same filters for query building
            $query = Payment::with(['order.customer']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_reference', 'like', "%{$search}%")
                      ->orWhereHas('order', function ($q) use ($search) {
                          $q->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                      });
                });
            }

            if ($request->filled('method') && $request->method !== 'all') {
                $query->where('payment_method', $request->method);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('paid_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('paid_at', '<=', $request->date_to);
            }

            $query->latest()->chunk(100, function ($payments) use ($handle) {
                foreach ($payments as $payment) {
                    fputcsv($handle, [
                        $payment->id,
                        $payment->order->order_number ?? '—',
                        $payment->order->customer->name ?? '—',
                        strtoupper($payment->payment_method),
                        $payment->amount,
                        $payment->status,
                        $payment->transaction_reference ?? '—',
                        $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '—',
                        $payment->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Generate and display a printable receipt for the payment.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function receipt(Payment $payment): View
    {
        $payment->load(['order.orderItems.service', 'order.customer', 'user']);
        return view('payments.receipt', compact('payment'));
    }
}
