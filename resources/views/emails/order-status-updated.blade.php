@component('mail::message')
# Hello {{ $order->customer->name }},

Your order status has been updated.

@php
    $statusMessages = [
        'pending_pickup'          => "Your order is waiting for pickup by our delivery agent.",
        'picked_up_from_customer' => "Your laundry has been collected by our agent.",
        'delivered_to_laundry'    => "Laundry arrived at our shop.",
        'processing'              => "We are cleaning your laundry.",
        'ready_for_delivery'      => "Your laundry is ready! Our delivery agent will collect it shortly.",
        'picked_up_from_laundry'  => "Your laundry has been collected from the shop.",
        'on_the_way'              => "Almost there! Our delivery agent is on the way to you.",
        'delivered'               => "Your order has been delivered. Thank you for choosing LOMS! Please leave a review.",
        'cancelled'               => "Your order has been cancelled. Contact support if this was an error.",
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
