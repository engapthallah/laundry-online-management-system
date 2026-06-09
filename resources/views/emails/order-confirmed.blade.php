@component('mail::message')
# Hello {{ $order->customer->name }},

Your order has been confirmed!

@component('mail::panel')
**Order Number:** {{ $order->order_number }}<br>
**Total Amount:** ${{ number_format($order->total_price, 2) }}<br>
**Pickup Date:** {{ \Carbon\Carbon::parse($order->pickup_time)->format('M d, Y h:i A') }}<br>
**Payment Method:** {{ strtoupper($order->payment_method) }}
@endcomponent

@component('mail::table')
| Service | Qty | Price |
| :--- | :---: | :---: |
@foreach($order->orderItems as $item)
| {{ $item->service->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('customer.orders.show', $order->id), 'color' => 'green'])
Track Your Order
@endcomponent

Your clothes are in good hands.

Thanks,<br>
**Iimaan Dry Cleaner — LOMS**
@endcomponent
