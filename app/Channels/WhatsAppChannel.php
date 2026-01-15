<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
{
    // 1. Check if the notification has the required method
    if (!method_exists($notification, 'toWhatsapp')) {
        Log::warning("WhatsApp Channel: 'toWhatsapp' method missing in notification class " . get_class($notification));
        return;
    }

    // 2. Get Recipient
    $recipient = null;

    if (method_exists($notifiable, 'routeNotificationFor')) {
        $recipient = $notifiable->routeNotificationFor('whatsapp');
        if (!$recipient) {
            $recipient = $notifiable->routeNotificationFor(self::class);
        }
    }

    if (!$recipient) {
        $recipient = $notifiable->phone_number ?? $notifiable->primary_phone ?? null;
    }

    if (!$recipient) {
        $id = method_exists($notifiable, 'getKey') ? $notifiable->getKey() : 'Anonymous';
        Log::warning("WhatsApp Channel: No recipient phone number found for ID: " . $id);
        return;
    }

    // 3. Configuration
    $apiKey = 'wb_MkxeSihvX44_bot'; 
    $baseUrl = 'https://wabot.adxventure.com/api/user/send-media-message';

    if (!$apiKey) {
        Log::error("WhatsApp Channel: API Key is missing.");
        return;
    }

    // 4. Get Content
    $data = $notification->toWhatsapp($notifiable);
    $text = is_array($data) ? ($data['message'] ?? '') : $data;
    $mediaUrl = is_array($data) ? ($data['url'] ?? null) : null;

    // 5. Prepare Query Parameters
    $queryParams = [
        'apikey' => $apiKey,
        'recipient' => $recipient,
        'text' => $text,
    ];

    if ($mediaUrl) {
        $queryParams['url'] = $mediaUrl;
    }

    // --- DEBUGGING STEP START ---
    // Manually build the query string to see exactly what the full URL looks like
    $fullUrlForLog = $baseUrl . '?' . http_build_query($queryParams);
    
    Log::info("WhatsApp Request DEBUG:", [
        'full_url' => $fullUrlForLog,  // <--- This is the URL you asked for
        'recipient' => $recipient,
        'text_length' => strlen($text)
    ]);
    // --- DEBUGGING STEP END ---

    // 6. Send Request
    try {
        // We still use the array method for the actual request to let Laravel handle encoding safely
        $response = Http::timeout(20)->get($baseUrl, $queryParams);
        $body = $response->json();

        // 7. Check response and Log
        if ($response->successful() && isset($body['message'])) {
            Log::info("WhatsApp sent to $recipient.", [
                'message_text' => $text,
                'api_response' => $body
            ]);
        } else {
            Log::error("WhatsApp API Error for $recipient.", [
                'full_url_attempted' => $fullUrlForLog, // Log it here too for easy debugging on failure
                'sent_text' => $text,
                'response' => $body,
                'status_code' => $response->status()
            ]);
        }

    } catch (\Exception $e) {
        Log::error("WhatsApp Exception for $recipient: " . $e->getMessage());
    }
}
}