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
        $recipient = $notifiable->routes[WhatsAppChannel::class] ?? null;
        
        if (!$recipient) {
             $recipient = $notifiable->phone_number ?? $notifiable->primary_phone ?? null;
        }

        if (!$recipient) {
            Log::warning("WhatsApp Channel: No phone number found for notification.");
            return;
        }

        // 3. Get API Key
        $apiKey = env('SYSTEM_WHATSAPP_KEY'); 

        if (!$apiKey) {
            Log::error("WhatsApp Channel: SYSTEM_WHATSAPP_KEY is missing in .env file.");
            return;
        }

        // 4. Get the Message Content
        // Expecting an array: ['message' => 'Your Caption', 'url' => 'http://path.to/image.jpg']
        $data = $notification->toWhatsapp($notifiable);
        
        // Ensure data is formatted correctly
        $text = is_array($data) ? ($data['message'] ?? '') : $data;
        $mediaUrl = is_array($data) ? ($data['url'] ?? null) : null;

        if (!$mediaUrl) {
            Log::warning("WhatsApp Channel: Media URL missing for recipient $recipient");
            // Optional: fallback to text-only endpoint if needed, or return.
        }

        // 5. Send Request
        try {
            // Using the send-media-message endpoint
            $response = Http::timeout(20)->get('https://wabot.adxventure.com/api/user/send-media-message', [
                'recipient' => $recipient,
                'apikey'    => $apiKey,
                'text'      => $text,       // The caption
                
            ]);

            $body = $response->json();

            // 6. Log Success or Failure based on the specific response provided
            // Expected: {"message":"Text message sent successfully."}
            if (isset($body['message']) && $body['message'] === 'Text message sent successfully.') {
                Log::info("WhatsApp Media sent to $recipient. Response: " . $body['message']);
            } else {
                Log::error("WhatsApp API Error for $recipient: " . json_encode($body));
            }

        } catch (\Exception $e) {
            Log::error("WhatsApp Exception for $recipient: " . $e->getMessage());
        }
    }
}