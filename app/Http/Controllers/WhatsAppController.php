<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppController extends Controller
{
    private function getApiKey()
    {
        return auth()->user()->whatsapp_api_key ?? null;
    }

    // -------------------------
    // SEND TEXT MESSAGE
    // -------------------------
    public function sendText(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'text' => 'required|string',
        ]);

        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return response()->json(['status' => 'error', 'message' => 'WhatsApp API key not configured'], 422);
        }

        try {
            $response = Http::timeout(15)->get('https://wabot.adxventure.com/api/user/send-message', [
                'recipient' => $request->recipient,
                'apikey' => $apiKey,
                'text' => $request->text,
            ]);

            $body = $response->json();

            // Normalize message
            $message = is_array($body['message'] ?? null) ? implode(' ', array_values($body['message'])) : $body['message'] ?? ($body['error'] ?? 'Unknown response');

            return [
                'status' => $body['success'] ?? ($body['status'] ?? 'error'),
                'message' => $message,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp TEXT Exception', ['error' => $e->getMessage(), 'recipient' => $request->recipient]);
            return response()->json(['status' => 'error', 'message' => 'WhatsApp API error occurred: ' . $e->getMessage()], 500);
        }
    }

    // -------------------------
    // SEND MEDIA MESSAGE
    // -------------------------
    public function sendMedia(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'text' => 'nullable|string',
            'mediaUrl' => 'required|url',
        ]);

        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return response()->json(['status' => 'error', 'message' => 'WhatsApp API key not configured'], 422);
        }

        $text = $request->text ?? 'Please check the attached media';

        try {
            $response = Http::timeout(20)->get('https://wabot.adxventure.com/api/user/send-media-message', [
                'recipient' => $request->recipient,
                'apikey' => $apiKey,
                'text' => $text,
                'file' => $request->mediaUrl,
            ]);

            $body = $response->json();

            $message = is_array($body['message'] ?? null) ? implode(' ', array_values($body['message'])) : $body['message'] ?? ($body['error'] ?? 'Unknown response');

            return [
                'status' => $body['success'] ?? ($body['status'] ?? 'error'),
                'message' => $message,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp MEDIA Exception', ['error' => $e->getMessage(), 'recipient' => $request->recipient]);
            return response()->json(['status' => 'error', 'message' => 'WhatsApp API error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function sendMediaJson(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'media_type' => 'required|string|in:template,banner,docs', // frontend must send this
        ]);

        $package = Package::findOrFail($request->package_id);
        $template = MessageTemplate::where('package_id', $package->id)->first();

        // Message text
        $text = $template->whatsapp_text ?? "Please check the package: {$package->package_name}";

        // Determine media based on user selection
        $media = null;

        switch ($request->media_type) {
            case 'template':
                if ($template?->whatsapp_media) {
                    $media = Storage::disk('public')->url($template->whatsapp_media);
                }
                break;

            case 'banner':
                if ($package->package_banner) {
                    $media = Storage::disk('public')->url($package->package_banner);
                }
                break;

            case 'docs':
                if ($package->package_docs) {
                    $docs = is_array($package->package_docs) ? $package->package_docs : [$package->package_docs];
                    $media = Storage::disk('public')->url($docs[0] ?? null);
                }
                break;
        }

        if (!$media) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => "No media found for the selected type: {$request->media_type}",
                ],
                422,
            );
        }

        // WhatsApp API key
        $apiKey = auth()->user()->whatsapp_api_key ?? null;
        if (!$apiKey) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'WhatsApp API key not configured',
                ],
                422,
            );
        }

        // Prepare URL
        $recipient = $request->recipient;
        $textEncoded = rawurlencode($text);
        $fileUrl = $media;

        // Custom filename
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $package->package_name));

        $url = "https://wabot.adxventure.com/api/user/send-media-message?recipient={$recipient}&apikey={$apiKey}&text={$textEncoded}&file={$fileUrl}&filename={$filename}";

        try {
            $response = Http::timeout(20)->get($url);
            $body = $response->json();

            return response()->json([
                'status' => $body['success'] ?? ($body['status'] ?? 'error'),
                'message' => $body['message'] ?? 'Unknown response',
                'media_used' => $fileUrl,
                'text_used' => $text,
                'filename_used' => $filename,
                'URL' => $url,
                'media_type_used' => $request->media_type,
            ]);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'WhatsApp API error: ' . $e->getMessage(),
                    'URL' => $url,
                ],
                500,
            );
        }
    }
}
