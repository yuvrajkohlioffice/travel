<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        // 1. Check if the notification has the WhatsApp message method
        if (!method_exists($notification, 'toWhatsapp')) {
            return;
        }

        // 2. Get Recipient Phone
        // Tries to get it from the "route" method first, then falls back to model attributes
        $recipient = $notifiable->routes[WhatsAppChannel::class] ?? null;
        
        if (!$recipient) {
             $recipient = $notifiable->phone_number ?? $notifiable->primary_phone ?? null;
        }

        if (!$recipient) {
            Log::warning("WhatsApp Channel: No phone number found for notification.");
            return;
        }

        // 3. Determine API Key
        // Priority: 1. The Agent/User passed in the notification -> 2. System Fallback
        $apiKey = env('SYSTEM_WHATSAPP_KEY'); 

        if (isset($notification->senderUser) && $notification->senderUser->whatsapp_api_key) {
            $apiKey = $notification->senderUser->whatsapp_api_key;
        }

        if (!$apiKey) {
            Log::error("WhatsApp Channel: No API Key available for recipient $recipient");
            return;
        }

        // 4. Get the Message Content
        $message = $notification->toWhatsapp($notifiable);
        
        // 5. Send Request (Using your confirmed working API Endpoint)
        try {
            $response = Http::timeout(15)->get('https://wabot.adxventure.com/api/user/send-message', [
                'recipient' => $recipient,
                'apikey'    => $apiKey,
                'text'      => $message,
            ]);

            $body = $response->json();

            // Log Success or Failure
            if (isset($body['success']) && $body['success'] == true) {
                // Success logic
                Log::info("WhatsApp sent to $recipient. ID: " . ($body['data']['id'] ?? 'unknown'));
            } else {
                // API returned an error
                Log::error("WhatsApp API Error for $recipient: " . json_encode($body));
            }

        } catch (\Exception $e) {
            Log::error("WhatsApp Exception for $recipient: " . $e->getMessage());
        }
    }
}