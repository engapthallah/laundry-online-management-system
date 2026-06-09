<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::whereHas('order', function ($q) {
            $q->where('customer_id', Auth::id());
        });

        // Compute summary counts and sums based on customer's total (unfiltered) payments:
        $allCustomerPayments = (clone $query)->get();

        $stats = [
            'total_count' => $allCustomerPayments->count(),
            'completed_count' => $allCustomerPayments->where('status', 'completed')->count(),
            'completed_amount' => $allCustomerPayments->where('status', 'completed')->sum('amount'),
            'pending_count' => $allCustomerPayments->where('status', 'pending')->count(),
            'failed_refunded_count' => $allCustomerPayments->whereIn('status', ['failed', 'refunded'])->count(),
        ];

        $totalSpent = $stats['completed_amount'];

        // Apply filters:
        if ($request->filled('method') && $request->method !== 'all') {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $payments = $query->with('order')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.payments.index', compact('payments', 'stats', 'totalSpent'));
    }

    /**
     * Display the specified payment and adaptive instructions.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function show(Payment $payment): View
    {
        $payment->load('order.orderItems.service');
        $this->authorizePayment($payment);
        $order = $payment->order;

        return view('customer.payments.show', compact('payment', 'order'));
    }

    /**
     * Confirm a pending mobile payment.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return redirect()->route('customer.payments.show', $payment)
                ->with('error', 'This payment has already been processed or is not pending.');
        }

        if (!in_array($payment->payment_method, ['zaad', 'edahab'])) {
            return redirect()->route('customer.payments.show', $payment)
                ->with('error', 'Cash payments do not require self-confirmation.');
        }

        $order = $payment->order;
        $timestamp = now()->timestamp;
        $method = strtoupper($payment->payment_method);
        $ref = "{$method}-{$order->order_number}-{$timestamp}";

        DB::transaction(function () use ($payment, $order, $ref, $method) {
            // 1. Update payment status to completed
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'transaction_reference' => $ref,
            ]);

            // 2. Update order payment status
            $order->update([
                'payment_status' => 'paid',
            ]);

            // 3. Create customer notification
            \App\Services\NotificationService::paymentConfirmed($payment);
        });

        return redirect()->route('customer.payments.show', $payment)
            ->with('success', 'Payment confirmed successfully! Your order is now being processed.');
    }

    /**
     * Retry a failed payment.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function retry(Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'failed') {
            return redirect()->route('customer.payments.show', $payment)
                ->with('error', 'Only failed payments can be retried.');
        }

        $payment->update([
            'status' => 'pending'
        ]);

        return redirect()->route('customer.payments.show', $payment)
            ->with('success', 'Payment reset. Please complete your payment using the instructions below.');
    }

    /**
     * Generate and display a printable receipt for the payment.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function receipt(Payment $payment): View
    {
        $this->authorizePayment($payment);
        $payment->load('order.orderItems.service', 'order.customer');
        return view('payments.receipt', compact('payment'));
    }

    /**
     * Authorize that the payment belongs to the currently authenticated customer.
     *
     * @param \App\Models\Payment $payment
     * @return void
     */
    private function authorizePayment(Payment $payment): void
    {
        if ($payment->order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment.');
        }
    }
}
