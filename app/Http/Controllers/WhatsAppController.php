<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    /**
     * Get API key from authenticated user
     */
    private function getApiKey()
    {
        $user = auth()->user();

        if (!$user || !$user->whatsapp_api_key) {
            return null;
        }

        return $user->whatsapp_api_key;
    }

    // -------------------------
    // SEND TEXT MESSAGE
    // -------------------------
    public function sendText(Request $request)
    {
        $request->validate([
            'recipient' => 'required',
            'text' => 'required',
        ]);

        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            Log::error("WhatsApp API Missing (TEXT)", [
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp API key not configured'
            ], 422);
        }

        Log::info("WhatsApp TEXT Request", [
            'recipient' => $request->recipient,
            'text' => $request->text
        ]);

        $response = Http::timeout(15)->get(
            'https://wabot.adxventure.com/api/user/send-message',
            [
                'recipient' => $request->recipient,
                'apikey' => $apiKey,
                'text' => $request->text,
            ]
        );

        Log::info("WhatsApp TEXT Response", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return $response->json();
    }

    // -------------------------
    // SEND MEDIA MESSAGE (PDF / IMAGE / VIDEO)
    // -------------------------
    public function sendMedia(Request $request)
    {
        $request->validate([
            'recipient' => 'required',
            'text' => 'required|string',
            'mediaUrl' => 'required|url',
        ]);

        $apiKey = $this->getApiKey();

        if (!$apiKey) {
            Log::error("WhatsApp API Missing (MEDIA)", [
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp API key not configured'
            ], 422);
        }

        try {
            Log::info("WhatsApp MEDIA Request", [
                'url' => 'https://wabot.adxventure.com/api/user/send-media-message',
                'payload' => [
                    'recipient' => $request->recipient,
                    'text' => $request->text,
                    'file' => $request->mediaUrl,
                    'apikey' => $apiKey,
                ]
            ]);

            $response = Http::timeout(20)->get(
                'https://wabot.adxventure.com/api/user/send-media-message',
                [
                    'recipient' => $request->recipient,
                    'apikey' => $apiKey,
                    'text' => $request->text,
                    'file' => $request->mediaUrl,
                ]
            );

            Log::info("WhatsApp MEDIA Response", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'WhatsApp API request failed',
                    'response' => $response->body()
                ], 500);
            }

            return $response->json();

        } catch (\Throwable $e) {
            Log::error("WhatsApp MEDIA Exception", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp API error occurred',
                'exception' => $e->getMessage()
            ], 500);
        }
    }

    // -------------------------
    // LEGACY JSON STYLE METHOD
    // -------------------------
public function sendMediaJson(Request $request)
{
    $request->validate([
        'recipient' => 'required|string',
        'text'      => 'required|string',
        'file'      => 'required|url', // must be public
        'apikey'    => 'nullable|string',
    ]);

    $apiKey = $request->apikey ?? $this->getApiKey();

    if (!$apiKey) {
        Log::error("WhatsApp API key missing", ['user_id' => auth()->id() ?? null]);
        return response()->json(['error' => 'WhatsApp API key not configured'], 422);
    }

    // Build the full URL
    $url = 'https://wabot.adxventure.com/api/user/send-media-message?' . http_build_query([
        'recipient' => $request->recipient,
        'apikey'    => $apiKey,
        'text'      => $request->text,
        'file'      => $request->file,
    ]);

    // Log the exact URL
    Log::info("WhatsApp MEDIA Request URL", ['url' => $url]);

    try {
        $response = Http::timeout(20)->get($url);

        Log::info("WhatsApp MEDIA Response", [
            'status' => $response->status(),
            'body'   => $response->body(),
            'url'    => $url,
        ]);

        return $response->json();
    } catch (\Throwable $e) {
        Log::error("WhatsApp MEDIA Exception", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'url'   => $url,
        ]);

        return response()->json([
            'status'    => 'error',
            'message'   => 'WhatsApp API error occurred',
            'exception' => $e->getMessage(),
            'url'       => $url,
        ], 500);
    }
}


}
