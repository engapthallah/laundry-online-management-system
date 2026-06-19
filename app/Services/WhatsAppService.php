<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Cache/History of sent message hashes within this request lifecycle to avoid duplicates.
     *
     * @var array<string, bool>
     */
    private static array $sentMessages = [];

    /**
     * Send WhatsApp message via CallMeBot API
     * Target: Business phone (owner gets notified
     * and contacts customer directly)
     *
     * @param string $message  The message to send
     * @param string|null $customerPhone
     *   If provided, include customer phone in message
     * @return bool True if sent successfully
     */
    public static function send(
        string $message,
        ?string $customerPhone = null
    ): bool
    {
        // Check if WhatsApp is enabled
        if (!config('services.whatsapp.enabled')) {
            Log::info('WhatsApp disabled. Message: '
                      . $message);
            return false;
        }

        // Add customer phone to message if provided
        if ($customerPhone) {
            $message .= "\n\n📱 *Customer Phone:* "
                      . $customerPhone;
        }

        // Request-level deduplication check
        $dedupKey = md5($message);
        if (isset(self::$sentMessages[$dedupKey])) {
            Log::info('WhatsApp duplicate send prevented in this request context. Message hash: ' . $dedupKey);
            return true;
        }
        self::$sentMessages[$dedupKey] = true;

        $provider = config('services.whatsapp.provider',
                           'callmebot');

        try {
            if ($provider === 'callmebot') {
                return self::sendViaCallMeBot($message);
            }

            if ($provider === 'twilio') {
                return self::sendViaTwilio(
                    $message,
                    $customerPhone
                );
            }

            Log::warning('Unknown WhatsApp provider: '
                         . $provider);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: '
                       . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via CallMeBot (free, to business phone)
     */
    private static function sendViaCallMeBot(
        string $message
    ): bool
    {
        $phone  = config('services.whatsapp.business_phone');
        $apiKey = config('services.whatsapp.api_key');

        if (!$phone || !$apiKey) {
            Log::warning('CallMeBot: Missing phone '
                         . 'or API key in config');
            return false;
        }

        $encodedMessage = urlencode($message);

        $url = "https://api.callmebot.com/whatsapp.php"
             . "?phone={$phone}"
             . "&text={$encodedMessage}"
             . "&apikey={$apiKey}";

        $response = Http::timeout(15)->get($url);

        if ($response->successful()) {
            Log::info('WhatsApp (CallMeBot) sent '
                      . 'successfully to: ' . $phone);
            return true;
        }

        Log::warning('WhatsApp (CallMeBot) failed. '
                     . 'Status: ' . $response->status()
                     . ' Body: ' . $response->body());
        return false;
    }

    /**
     * Send via Twilio WhatsApp API (direct to customer)
     * Optional — requires Twilio account
     */
    private static function sendViaTwilio(
        string $message,
        ?string $toPhone = null
    ): bool
    {
        $accountSid = config('services.twilio.sid');
        $authToken  = config('services.twilio.token');
        $fromNumber = config('services.twilio.whatsapp_from');
        $toNumber   = $toPhone
                   ?? config('services.whatsapp.business_phone');

        if (!$accountSid || !$authToken
            || !$fromNumber || !$toNumber) {
            Log::warning('Twilio: Missing configuration');
            return false;
        }

        // Format numbers robustly for WhatsApp sandbox or live channels
        $cleanFrom = str_replace('whatsapp:', '', $fromNumber);
        $from = 'whatsapp:+' . ltrim($cleanFrom, '+');

        $cleanTo = str_replace('whatsapp:', '', $toNumber);
        $to = 'whatsapp:+' . ltrim($cleanTo, '+');

        $url  = "https://api.twilio.com/2010-04-01"
              . "/Accounts/{$accountSid}/Messages.json";

        $response = Http::withBasicAuth(
            $accountSid,
            $authToken
        )->asForm()->post($url, [
            'From' => $from,
            'To'   => $to,
            'Body' => $message,
        ]);

        if ($response->successful()) {
            Log::info('WhatsApp (Twilio) sent to: '
                      . $to);
            return true;
        }

        Log::warning('WhatsApp (Twilio) failed. Status: ' . $response->status() . ' Response: '
                     . json_encode($response->json()));
        return false;
    }

    /**
     * Test WhatsApp connection
     * Call this from a test route to verify setup
     */
    public static function sendTestMessage(): bool
    {
        $message = "🧺 *Iimaan Dry Cleaner*\n\n"
                 . "✅ WhatsApp integration is working!\n"
                 . "LOMS notifications are active.\n"
                 . "Time: " . now()->format('h:i A, M d Y');

        return self::send($message);
    }
}
