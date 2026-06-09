@component('mail::message')
# Hello {{ $payment->order->customer->name }},

Your payment has been refunded.

@component('mail::panel')
**Order Number:** {{ $payment->order->order_number }}<br>
**Refunded Amount:** ${{ number_format($payment->amount, 2) }}<br>
**Original Payment Method:** {{ strtoupper($payment->payment_method) }}
@endcomponent

Please allow 3-5 business days for the funds to reflect back in your account.

@component('mail::button', ['url' => route('customer.support.create'), 'color' => 'green'])
Contact Support
@endcomponent

Thanks,<br>
**Iimaan Dry Cleaner — LOMS**
@endcomponent
