<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppTemplates
{
    /**
     * Message 1: Order ready for delivery
     * Sent when staff marks order as ready_for_delivery
     */
    public static function readyForDelivery(
        Order $order
    ): string
    {
        $customerName = explode(' ',
            $order->customer->name)[0];
        $orderNumber  = $order->order_number;
        $totalPrice   = '$' . number_format(
            $order->total_price, 2);
        $address      = $order->delivery_address;

        return "🧺 *Iimaan Dry Cleaner*\n\n"
             . "✅ *Order Ready for Delivery!*\n\n"
             . "Dear {$customerName},\n"
             . "Your laundry is clean and ready "
             . "for delivery!\n\n"
             . "📦 *Order:* #{$orderNumber}\n"
             . "💰 *Total:* {$totalPrice}\n"
             . "📍 *Deliver to:* {$address}\n\n"
             . "Our delivery agent will contact "
             . "you shortly to arrange delivery.\n\n"
             . "📞 Need help? Contact us anytime.\n"
             . "Thank you for choosing Iimaan! 🙏";
    }

    /**
     * Message 2: Order delivered successfully
     * Sent when delivery agent marks as delivered
     */
    public static function orderDelivered(
        Order $order
    ): string
    {
        $customerName = explode(' ',
            $order->customer->name)[0];
        $orderNumber  = $order->order_number;
        $totalPrice   = '$' . number_format(
            $order->total_price, 2);
        $deliveredAt  = now()->format('h:i A, M d Y');
        $reviewUrl    = config('app.url')
                      . '/reviews';

        return "🧺 *Iimaan Dry Cleaner*\n\n"
             . "🎉 *Order Delivered Successfully!*\n\n"
             . "Dear {$customerName},\n"
             . "Your fresh laundry has been "
             . "delivered!\n\n"
             . "📦 *Order:* #{$orderNumber}\n"
             . "💰 *Amount:* {$totalPrice}\n"
             . "🕐 *Delivered:* {$deliveredAt}\n\n"
             . "⭐ *Rate your experience:*\n"
             . "{$reviewUrl}\n\n"
             . "Thank you for choosing Iimaan "
             . "Dry Cleaner!\n"
             . "See you next time 👋";
    }
}
