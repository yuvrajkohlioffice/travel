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
        // 2. Get Recipient
        $recipient = null;

        // Check for custom routing first
        if (method_exists($notifiable, 'routeNotificationFor')) {
            $recipient = $notifiable->routeNotificationFor('whatsapp');
            if (!$recipient) {
                $recipient = $notifiable->routeNotificationFor(self::class);
            }
        }

        // Fallback: Build recipient from model attributes
        if (!$recipient) {
            $phoneNumber = $notifiable->phone_number ?? $notifiable->primary_phone ?? null;
            
            // FIX: If phone_code is empty in DB, default to '91'
            $phoneCode = !empty($notifiable->phone_code) ? $notifiable->phone_code : '91';

            if ($phoneNumber) {
                // 1. Combine Code + Number
                $fullNumber = $phoneCode . $phoneNumber;
                
                // 2. Sanitize (Remove spaces, +, etc.)
                $recipient = preg_replace('/[^0-9]/', '', $fullNumber);

                if (strlen($recipient) === 10) {
                    $recipient = '91' . $recipient;
                }
            }
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
            'recipient' => \Illuminate\Support\Str::startsWith($recipient, '91') ? $recipient : '91' . $recipient,
            'text' => $text,
        ];

        if ($mediaUrl) {
            $queryParams['url'] = $mediaUrl;
        }

        // --- DEBUGGING STEP START ---
        $fullUrlForLog = $baseUrl . '?' . http_build_query($queryParams);
        
        Log::info("WhatsApp Request DEBUG:", [
            'full_url' => $fullUrlForLog,
            'recipient' => $recipient,
            'text_length' => strlen($text)
        ]);
        // --- DEBUGGING STEP END ---

        // 6. Send Request
        try {
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
                    'full_url_attempted' => $fullUrlForLog,
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