@component('mail::message')
# Hello {{ $order->customer->name }},

Your order status has been updated.

@php
    $statusMessages = [
        'confirmed'          => "Your order has been confirmed and will be processed soon.",
        'washing'            => "Your clothes are now being washed.",
        'drying'             => "Your clothes are being dried.",
        'ironing'            => "Your clothes are being ironed.",
        'folding'            => "Your clothes are being folded and prepared for delivery.",
        'ready_for_delivery' => "Your order is ready! Our delivery team will collect it shortly.",
        'out_for_delivery'   => "Your order is on its way! Please be available at your delivery address.",
        'delivered'          => "Your order has been delivered. Thank you for choosing LOMS! Please leave a review.",
        'cancelled'          => "Your order has been cancelled. Contact support if this was an error.",
    ];
    $messageText = $statusMessages[$status] ?? "Your order status has been updated.";
    $statusLabel = ucwords(str_replace('_', ' ', $status));
@endphp

@component('mail::panel')
**Order Number:** {{ $order->order_number }}<br>
**New Status:** **{{ $statusLabel }}**<br>
**Updated At:** {{ now()->format('M d, Y h:i A') }}
@endcomponent

{{ $messageText }}

@component('mail::button', ['url' => route('customer.orders.show', $order->id), 'color' => 'green'])
View Order
@endcomponent

Thanks,<br>
**Iimaan Dry Cleaner — LOMS**
@endcomponent
