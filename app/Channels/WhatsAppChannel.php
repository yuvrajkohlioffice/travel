<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        // 1. Check if the notification has the WhatsApp method
        if (!method_exists($notification, 'toWhatsapp')) {
            return;
        }

        // 2. Get the message and recipient
        $message = $notification->toWhatsapp($notifiable);
        
        // Try to find phone number in multiple places
        $recipient = $notifiable->routes['whatsapp'] 
                  ?? $notifiable->primary_phone 
                  ?? $notifiable->phone 
                  ?? null;

        if (!$recipient) {
            Log::warning("WhatsApp Channel: No phone number found for notifiable ID " . $notifiable->id);
            return;
        }

        // 3. Determine API Key
        // Since this runs in background, we can't use auth()->user().
        // We try to get the API key from the invoice's creator (User) if available.
        $apiKey = null;

        if (isset($notifiable->user) && $notifiable->user->whatsapp_api_key) {
             // If the invoice belongs to a user, use their key
            $apiKey = $notifiable->user->whatsapp_api_key;
        } elseif (isset($notifiable->lead) && $notifiable->lead->user && $notifiable->lead->user->whatsapp_api_key) {
             // If attached to a lead -> user
             $apiKey = $notifiable->lead->user->whatsapp_api_key;
        } else {
            // FALLBACK: Use a system-wide key from .env
            $apiKey = env('SYSTEM_WHATSAPP_KEY'); 
        }

        if (!$apiKey) {
            Log::error("WhatsApp Channel: No API Key found for recipient $recipient");
            return;
        }

        // 4. Send the Request (Replicating your Controller logic)
        try {
            // Using the same endpoint as your sendText method
            $response = Http::timeout(15)->get('https://wabot.adxventure.com/api/user/send-message', [
                'recipient' => $recipient,
                'apikey'    => $apiKey,
                'text'      => $message,
            ]);

            $body = $response->json();

            // 5. Log Result
            if (isset($body['success']) && $body['success'] == true) {
                 Log::info("WhatsApp sent successfully to $recipient");
            } else {
                 Log::error("WhatsApp API Error: " . json_encode($body));
            }

        } catch (\Throwable $e) {
            Log::error("WhatsApp Exception: " . $e->getMessage());
        }
    }
}