@component('mail::message')
# Hello {{ $order->customer->name }},

Your order has been delivered successfully!

@php
    $agentFirstName = 'our delivery team';
    if ($order->deliveryAssignment && $order->deliveryAssignment->deliveryAgent) {
        $agentName = $order->deliveryAssignment->deliveryAgent->name;
        $agentFirstName = explode(' ', trim($agentName))[0];
    }
@endphp

@component('mail::panel')
**Order Number:** {{ $order->order_number }}<br>
**Delivered At:** {{ $order->delivery_time ? \Carbon\Carbon::parse($order->delivery_time)->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}<br>
**Delivery Agent:** {{ $agentFirstName }}
@endcomponent

@component('mail::button', ['url' => route('customer.reviews.create', ['order' => $order->id]), 'color' => 'green'])
Leave a Review
@endcomponent

Thank you for choosing Iimaan Dry Cleaner!

Thanks,<br>
**Iimaan Dry Cleaner — LOMS**
@endcomponent
