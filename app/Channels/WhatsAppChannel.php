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
        // Tries to get it from the "route" method first (On-Demand), then falls back to model attributes
        $recipient = $notifiable->routes[WhatsAppChannel::class] ?? null;
        
        if (!$recipient) {
             $recipient = $notifiable->phone_number ?? $notifiable->primary_phone ?? null;
        }

        if (!$recipient) {
            Log::warning("WhatsApp Channel: No phone number found for notification.");
            return;
        }

        // 3. Get API Key (Strictly from .env as requested)
        $apiKey = env('SYSTEM_WHATSAPP_KEY'); 

        if (!$apiKey) {
            Log::error("WhatsApp Channel: SYSTEM_WHATSAPP_KEY is missing in .env file.");
            return;
        }

        // 4. Get the Message Content
        // We pass $notifiable just in case the notification needs to access the user data
        $message = $notification->toWhatsapp($notifiable);
        
        // 5. Send Request
        try {
            $response = Http::timeout(20)->get('https://wabot.adxventure.com/api/user/send-message', [
                'recipient' => $recipient,
                'apikey'    => $apiKey,
                'text'      => $message,
            ]);

            $body = $response->json();

            // Log Success or Failure
            if (isset($body['success']) && $body['success'] == true) {
                Log::info("WhatsApp sent to $recipient using System Key. ID: " . ($body['data']['id'] ?? 'unknown'));
            } else {
                Log::error("WhatsApp API Error for $recipient: " . json_encode($body));
            }

        } catch (\Exception $e) {
            Log::error("WhatsApp Exception for $recipient: " . $e->getMessage());
        }
    }
}