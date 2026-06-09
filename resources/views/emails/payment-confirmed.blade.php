@component('mail::message')
# Hello {{ $payment->order->customer->name }},

Your payment has been confirmed!

@component('mail::panel')
**Order Number:** {{ $payment->order->order_number }}<br>
**Amount Paid:** ${{ number_format($payment->amount, 2) }}<br>
**Payment Method:** {{ strtoupper($payment->payment_method) }}<br>
**Transaction Reference:** {{ $payment->transaction_reference }}<br>
**Date Paid:** {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}
@endcomponent

Thank you for your payment.

@component('mail::button', ['url' => route('customer.payments.receipt', $payment->id), 'color' => 'green'])
View Receipt
@endcomponent

Thanks,<br>
**Iimaan Dry Cleaner — LOMS**
@endcomponent
