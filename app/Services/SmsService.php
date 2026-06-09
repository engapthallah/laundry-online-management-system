<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message.
     *
     * @param string $phone
     * @param string $message
     * @return void
     */
    public static function send(string $phone, string $message): void
    {
        try {
            // Truncate to 160 characters
            $message = substr($message, 0, 160);

            // Development: log the SMS
            if (config('app.env') === 'local') {
                Log::info("SMS TO: {$phone} | {$message}");
                return;
            }

            // Production: placeholder for real SMS API
            Log::info("SMS TO (Production Stub): {$phone} | {$message}");
        } catch (\Exception $e) {
            Log::error("SMS delivery failed to {$phone}: " . $e->getMessage());
        }
    }
}
