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

        // 2. Get Recipient (Follows Laravel standard + your fallbacks)
        $recipient = null;

        if (method_exists($notifiable, 'routeNotificationFor')) {
            // A. Check for 'whatsapp' string (Standard for User/Lead models)
            $recipient = $notifiable->routeNotificationFor('whatsapp');
            
            // B. If that failed, check for the Class Name (For Anonymous/Command notifications)
            if (!$recipient) {
                $recipient = $notifiable->routeNotificationFor(self::class);
            }
        }

        if (!$recipient) {
            $recipient = $notifiable->phone_number ?? $notifiable->primary_phone ?? null;
        }

        if (!$recipient) {
            // Use try/catch for getKey() because AnonymousNotifiable doesn't have it
            $id = method_exists($notifiable, 'getKey') ? $notifiable->getKey() : 'Anonymous';
            Log::warning("WhatsApp Channel: No recipient phone number found for ID: " . $id);
            return;
        }

        // 3. Configuration
        $apiKey = 'wb_4mBjE3IfwFs_bot'; 
        $baseUrl = 'https://wabot.adxventure.com/api/user/send-media-message';

        if (!$apiKey) {
            Log::error("WhatsApp Channel: API Key is missing.");
            return;
        }

        // 4. Get Content from Notification
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

        // 6. Send Request
        try {
            $response = Http::timeout(20)->get($baseUrl, $queryParams);
            $body = $response->json();

            // 7. Check response and Log (UPDATED)
            if ($response->successful() && isset($body['message'])) {
                Log::info("WhatsApp sent to $recipient.", [
                    'message_text' => $text, // <--- Added this
                    'api_response' => $body
                ]);
            } else {
                Log::error("WhatsApp API Error for $recipient.", [
                    'sent_text' => $text,    // <--- Added this to error log too
                    'response' => $body
                ]);
            }

        } catch (\Exception $e) {
            Log::error("WhatsApp Exception for $recipient: " . $e->getMessage());
        }
    }
}