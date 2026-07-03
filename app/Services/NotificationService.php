<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SupportMessage;
use App\Models\User;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\PaymentRefundedMail;
use App\Mail\SupportRepliedMail;
use App\Mail\DeliveryCompletedMail;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Core send method that creates and saves a Notification record.
     * Always safe: wraps operations in try/catch, logging but never throwing.
     */
    public static function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'system',
        ?int $orderId = null
    ): ?Notification {
        try {
            return Notification::create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create notification for user ID {$userId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Triggered on Order placed.
     */
    public static function orderPlaced(Order $order): void
    {
        try {
            $customer = $order->customer;
            if (!$customer) {
                return;
            }

            $orderNumber = $order->order_number;
            $totalPrice = number_format($order->total_price, 2);

            $customerTitle = "Order Confirmed — {$orderNumber}";
            $customerMessage = "Your order {$orderNumber} has been placed successfully. Total: \${$totalPrice}. We will begin processing shortly.";

            // 1. Customer: System Notification
            self::send($customer->id, $customerTitle, $customerMessage, 'system', $order->id);

            // 2. Customer: Email Notification
            self::send($customer->id, $customerTitle, $customerMessage, 'email', $order->id);
            try {
                Mail::to($customer->email)->send(new OrderConfirmedMail($order));
            } catch (\Exception $e) {
                Log::error("Failed sending OrderConfirmedMail to {$customer->email}: " . $e->getMessage());
            }

            // 3. Customer: SMS Notification
            $smsMessage = "LOMS: Order {$orderNumber} confirmed! Total: \${$totalPrice}. We will start processing your laundry shortly. — Iimaan Dry Cleaner";
            try {
                if ($customer->phone) {
                    SmsService::send($customer->phone, $smsMessage);
                }
            } catch (\Exception $e) {
                Log::error("Failed sending SMS to customer {$customer->id}: " . $e->getMessage());
            }

            // 4. Admin (first admin user): System Notification
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $adminTitle = "New Order Received — {$orderNumber}";
                $adminMessage = "Customer {$customer->name} placed order {$orderNumber} worth \${$totalPrice}.";
                self::send($admin->id, $adminTitle, $adminMessage, 'system', $order->id);
            }
        } catch (\Exception $e) {
            Log::error("Error processing orderPlaced notifications for Order #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Triggered on Laundry Order status update.
     */
    public static function orderStatusUpdated(Order $order, string $newStatus): void
    {
        try {
            $customer = $order->customer;
            if (!$customer) {
                return;
            }

            $orderNumber = $order->order_number;
            $title = "Order Update — {$orderNumber}";

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

            $message = $statusMessages[$newStatus] ?? "Your order status has been updated to {$newStatus}.";

            if ($newStatus === 'cancelled' && $order->payment_status === 'rejected') {
                $message = "Your payment for Order #{$orderNumber} could not be verified and the order has been cancelled.";
            }

            // If the status is delivered, delegate to deliveryCompleted to avoid duplication
            if ($newStatus === 'delivered') {
                self::deliveryCompleted($order);
                return;
            }

            // 1. Customer: System Notification
            self::send($customer->id, $title, $message, 'system', $order->id);

            // Channel dispatch logic based on status
            // Email dispatch for confirmed and cancelled status updates
            if (in_array($newStatus, ['confirmed', 'cancelled'])) {
                self::send($customer->id, $title, $message, 'email', $order->id);
                try {
                    Mail::to($customer->email)->send(new OrderStatusUpdatedMail($order, $newStatus));
                } catch (\Exception $e) {
                    Log::error("Failed sending OrderStatusUpdatedMail to {$customer->email} for status '{$newStatus}': " . $e->getMessage());
                }
            }

            // Customer Email notification (Ready For Delivery)
            if ($newStatus === 'ready_for_delivery') {
                self::send($customer->id, $title, $message, 'email', $order->id);
                try {
                    Mail::to($customer->email)->send(new \App\Mail\OrderReadyForDeliveryMail($order));
                    Log::info("Email notification sent successfully to Customer ID {$customer->id} ({$customer->email}) for Order #{$order->id} [ready_for_delivery].");
                } catch (\Exception $e) {
                    Log::error("Failed to send ready_for_delivery email to Customer ID {$customer->id} ({$customer->email}) for Order #{$order->id}: " . $e->getMessage());
                }
            }

            // SMS dispatch for ready_for_delivery, out_for_delivery
            if (in_array($newStatus, ['ready_for_delivery', 'out_for_delivery'])) {
                $smsTemplates = [
                    'ready_for_delivery' => "LOMS: Order {$orderNumber} is ready and will be picked up by our delivery team soon. — Iimaan Dry Cleaner",
                    'out_for_delivery'   => "LOMS: Order {$orderNumber} is on its way! Please be available at your delivery address. — Iimaan Dry Cleaner",
                ];
                $smsMessage = $smsTemplates[$newStatus] ?? $message;

                try {
                    if ($customer->phone) {
                        SmsService::send($customer->phone, $smsMessage);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed sending SMS update to customer {$customer->id} for status '{$newStatus}': " . $e->getMessage());
                }
            }

            // WhatsApp notification to business phone
            if ($newStatus === 'ready_for_delivery') {
                try {
                    $waMessage = \App\Services\WhatsAppTemplates::readyForDelivery($order);
                    \App\Services\WhatsAppService::send(
                        $waMessage,
                        $order->customer->phone ?? null
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'WhatsApp ready_for_delivery failed: '
                        . $e->getMessage()
                    );
                }
            }

            // Customer WhatsApp notification (CallMeBot)
            if ($newStatus === 'ready_for_delivery') {
                try {
                    \App\Jobs\SendWhatsAppNotificationJob::dispatch($customer, $order, 'ready_for_delivery');
                } catch (\Exception $e) {
                    Log::error("Failed to dispatch WhatsApp job for ready_for_delivery on Order #{$order->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error processing orderStatusUpdated notifications for Order #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Triggered on Payment Confirmed.
     */
    public static function paymentConfirmed(Payment $payment): void
    {
        try {
            $order = $payment->order;
            if (!$order) {
                return;
            }

            $customer = $order->customer;
            if (!$customer) {
                return;
            }

            $orderNumber = $order->order_number;
            $title = "Payment Confirmed — {$orderNumber}";
            $amount = number_format($payment->amount, 2);
            $method = strtoupper($payment->payment_method);
            $ref = $payment->transaction_reference;

            $message = "Your payment of \${$amount} via {$method} has been confirmed. Transaction: {$ref}";

            // 1. Customer: System Notification
            self::send($customer->id, $title, $message, 'system', $order->id);

            // 2. Customer: Email Notification
            self::send($customer->id, $title, $message, 'email', $order->id);
            try {
                Mail::to($customer->email)->send(new PaymentConfirmedMail($payment));
            } catch (\Exception $e) {
                Log::error("Failed sending PaymentConfirmedMail to {$customer->email}: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error("Error processing paymentConfirmed notifications for Payment #{$payment->id}: " . $e->getMessage());
        }
    }

    /**
     * Triggered on Payment Refunded.
     */
    public static function paymentRefunded(Payment $payment): void
    {
        try {
            $order = $payment->order;
            if (!$order) {
                return;
            }

            $customer = $order->customer;
            if (!$customer) {
                return;
            }

            $orderNumber = $order->order_number;
            $title = "Payment Refunded — {$orderNumber}";
            $amount = number_format($payment->amount, 2);

            $customerMessage = "Your payment of \${$amount} for order {$orderNumber} has been refunded.";

            // 1. Customer: System Notification
            self::send($customer->id, $title, $customerMessage, 'system', $order->id);

            // 2. Customer: Email Notification
            self::send($customer->id, $title, $customerMessage, 'email', $order->id);
            try {
                Mail::to($customer->email)->send(new PaymentRefundedMail($payment));
            } catch (\Exception $e) {
                Log::error("Failed sending PaymentRefundedMail to {$customer->email}: " . $e->getMessage());
            }

            // 3. Admin: System Notification
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $adminTitle = "Refund Processed — {$orderNumber}";
                $adminMessage = "Refund of \${$amount} processed for order {$orderNumber}.";
                self::send($admin->id, $adminTitle, $adminMessage, 'system', $order->id);
            }
        } catch (\Exception $e) {
            Log::error("Error processing paymentRefunded notifications for Payment #{$payment->id}: " . $e->getMessage());
        }
    }

    /**
     * Triggered on Payment Failed.
     */
    public static function paymentFailed(Payment $payment): void
    {
        try {
            $order = $payment->order;
            if (!$order) {
                return;
            }

            $customer = $order->customer;
            if (!$customer) {
                return;
            }

            $orderNumber = $order->order_number;
            $title = "Payment Issue — {$orderNumber}";
            $message = "Your payment for order {$orderNumber} could not be processed. Please retry your payment.";

            // Customer: System only
            self::send($customer->id, $title, $message, 'system', $order->id);
        } catch (\Exception $e) {
            Log::error("Error processing paymentFailed notifications for Payment #{$payment->id}: " . $e->getMessage());
        }
    }

    /**
     * Triggered when support message is replied to.
     */
    public static function supportMessageReplied(SupportMessage $message): void
    {
        try {
            $customer = $message->user;
            if (!$customer) {
                return;
            }

            $title = "Support Reply — {$message->subject}";
            $notificationMessage = "Our team has replied to your support message. Log in to view the response.";

            // 1. Customer: System Notification
            self::send($customer->id, $title, $notificationMessage, 'system');

            // 2. Customer: Email Notification
            self::send($customer->id, $title, $notificationMessage, 'email');
            try {
                Mail::to($customer->email)->send(new SupportRepliedMail($message));
            } catch (\Exception $e) {
                Log::error("Failed sending SupportRepliedMail to {$customer->email}: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error("Error processing supportMessageReplied notifications for message #{$message->id}: " . $e->getMessage());
        }
    }

    /**
     * Triggered on Delivery Completed.
     */
    public static function deliveryCompleted(Order $order): void
    {
        try {
            $customer = $order->customer;
            if (!$customer) {
                return;
            }

            $orderNumber = $order->order_number;

            $customerTitle = "Order Delivered — {$orderNumber}";
            $customerMessage = "Your order has been delivered successfully. Enjoy! Please rate your experience.";
            $smsMessage = "LOMS: Order {$orderNumber} delivered! Thank you for choosing us. Please rate your experience. — Iimaan Dry Cleaner";

            // 1. Customer: System Notification
            self::send($customer->id, $customerTitle, $customerMessage, 'system', $order->id);

            // 2. Customer: Email Notification
            self::send($customer->id, $customerTitle, $customerMessage, 'email', $order->id);
            try {
                Mail::to($customer->email)->send(new \App\Mail\OrderDeliveredMail($order));
                Log::info("Email notification sent successfully to Customer ID {$customer->id} ({$customer->email}) for Order #{$order->id} [delivered].");
            } catch (\Exception $e) {
                Log::error("Failed to send delivered email to Customer ID {$customer->id} ({$customer->email}) for Order #{$order->id}: " . $e->getMessage());
            }

            // 3. Customer: SMS Notification
            try {
                if ($customer->phone) {
                    SmsService::send($customer->phone, $smsMessage);
                }
            } catch (\Exception $e) {
                Log::error("Failed sending SMS to customer {$customer->id} on delivery: " . $e->getMessage());
            }

            // WhatsApp notification to business phone
            try {
                $waMessage = \App\Services\WhatsAppTemplates::orderDelivered($order);
                \App\Services\WhatsAppService::send(
                    $waMessage,
                    $order->customer->phone ?? null
                );
            } catch (\Exception $e) {
                Log::error(
                    'WhatsApp delivered failed: '
                    . $e->getMessage()
                );
            }

            // Customer WhatsApp notification (CallMeBot)
            try {
                \App\Jobs\SendWhatsAppNotificationJob::dispatch($customer, $order, 'delivered');
            } catch (\Exception $e) {
                Log::error("Failed to dispatch WhatsApp job for delivered on Order #{$order->id}: " . $e->getMessage());
            }

            // 4. Admin: System Notification
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $adminTitle = "Delivery Complete — {$orderNumber}";
                $adminMessage = "Order {$orderNumber} was delivered to {$customer->name}.";
                self::send($admin->id, $adminTitle, $adminMessage, 'system', $order->id);
            }
        } catch (\Exception $e) {
            Log::error("Error processing deliveryCompleted notifications for Order #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Send a WhatsApp notification to the customer via CallMeBot free API.
     * Always safe: wraps everything in try/catch, logging outcomes, and never throwing exceptions.
     *
     * @param  \App\Models\User  $customer
     * @param  \App\Models\Order  $order
     * @param  string  $eventType
     * @return bool
     */
    public static function sendWhatsAppNotification(User $customer, Order $order, string $eventType): bool
    {
        try {
            $apiKey = config('services.callmebot.apikey');
            if (empty($apiKey)) {
                Log::warning("CallMeBot API key is not configured. Cannot send WhatsApp notification for Order #{$order->id}.");
                return false;
            }

            $recipientPhone = config('services.callmebot.phone');
            if (empty($recipientPhone)) {
                Log::warning("CallMeBot recipient phone is not configured. Cannot send WhatsApp notification for Order #{$order->id}.");
                return false;
            }

            // Strip the '+' sign from the phone number as required by CallMeBot
            $cleanPhone = ltrim($recipientPhone, '+');

            $customerFullName = $customer->name;
            $orderId = $order->id;

            // WhatsApp alerts templates for the business owner
            if ($eventType === 'ready_for_delivery') {
                $message = "🧺 Order Ready for Delivery!\n"
                         . "Customer: {$customerFullName}\n"
                         . "Order ID: #{$orderId}\n"
                         . "The laundry is clean and ready. Please coordinate delivery to the customer.";
            } elseif ($eventType === 'delivered') {
                $message = "✅ Order Delivered!\n"
                         . "Customer: {$customerFullName}\n"
                         . "Order ID: #{$orderId}\n"
                         . "The order has been successfully delivered to the customer.";
            } else {
                Log::warning("Invalid eventType '{$eventType}' specified for CallMeBot WhatsApp notification on Order #{$orderId}.");
                return false;
            }

            // Make the HTTP request using Laravel's Http client with query parameters
            $response = Http::timeout(15)->get('https://api.callmebot.com/whatsapp.php', [
                'phone'  => $cleanPhone,
                'text'   => $message,
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp notification via CallMeBot sent successfully to owner (Phone: {$cleanPhone}) for Order #{$orderId} [Type: {$eventType}].");
                return true;
            }

            Log::error("CallMeBot API returned status {$response->status()} with body: {$response->body()} for Order #{$orderId}.");
            return false;

        } catch (\Exception $e) {
            Log::error("Exception occurred while sending CallMeBot WhatsApp notification for Order #{$order->id}: " . $e->getMessage());
            return false;
        }
    }
}
