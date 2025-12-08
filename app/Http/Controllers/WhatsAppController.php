<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    private $apiKey = 'wb_doJFqo8M7PE_bot'; // your API key

    // -------------------------
    // SEND TEXT MESSAGE
    // -------------------------
    public function sendText(Request $request)
    {
        $response = Http::get('https://wabot.adxventure.com/api/user/send-message', [
            'recipient' => $request->recipient,
            'apikey' => $this->apiKey,
            'text' => $request->text,
        ]);

        return $response->json();
    }

    // -------------------------
    // SEND MEDIA MESSAGE (IMAGE / VIDEO / PDF)
    // -------------------------
    public function sendMedia(Request $request)
    {
        $response = Http::get('https://wabot.adxventure.com/api/user/send-media-message', [
            'recipient' => $request->recipient,
            'apikey' => $this->apiKey,
            'text' => $request->text,
            'file' => $request->mediaUrl, // URL of file/image/video
        ]);

        return $response->json();
    }

    // -------------------------
    // SEND MEDIA WITH JSON BODY (Alternative)
    public function sendMediaJson(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'recipient' => 'required|string',
            'apikey' => 'required|string',
            'text' => 'required|string',
            'file' => 'required|url',
        ]);

        // Build URL with query parameters
        $url = 'https://wabot.adxventure.com/api/user/send-media-message';

        // Prepare query parameters
        $queryParams = [
            'recipient' => $validated['recipient'],
            'apikey' => $this->apiKey, // or $validated['apikey'] if you want to use from request
            'text' => $validated['text'],
            'file' => $validated['file'],
        ];

        // Send GET request with query parameters
        $response = Http::get($url, $queryParams);

        return $response->json();
    }
}
