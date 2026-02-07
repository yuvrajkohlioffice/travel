<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    private function getApiKey()
    {
        return auth()->user()->whatsapp_api_key ?? null;
    }
    // -------------------------
    // 🧪 QUICK TEST CONNECTION
    // -------------------------
    public function testConnection(Request $request)
    {
        // 1. Get Recipient from URL (e.g., ?recipient=919876543210)
        $recipient = $request->query('recipient');

        if (!$recipient) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Missing recipient! Add ?recipient=PHONE_NUMBER to the URL.',
                ],
                400,
            );
        }

        // 2. Get API Key
        $apiKey = $this->getApiKey();

        if (!$apiKey) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User has no WhatsApp API Key configured.',
                ],
                422,
            );
        }

        // 3. Send Test Message
        $text = "✅ *Connection Successful!*\n\nHello from Laravel. If you are reading this, your WhatsApp API integration is working perfectly.\n\n📅 Time: " . now()->toDateTimeString();

        try {
            $response = Http::timeout(10)->get('https://wabot.adxventure.com/api/user/send-message', [
                'recipient' => $recipient,
                'apikey' => $apiKey,
                'text' => $text,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request sent to API',
                'api_response' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Connection Failed: ' . $e->getMessage(),
                ],
                500,
            );
        }
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
            'media_type' => 'required|string|in:template,banner,docs',
        ]);

        // --- 1. CLEANUP (Garbage Collection) ---
        // Deletes temp files older than 1 hour to keep server clean
        // You can also move this to a Cron Job later if you prefer
        $files = Storage::disk('public')->files('temp_whatsapp');
        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            if (Carbon::createFromTimestamp($lastModified)->lt(Carbon::now()->subHour())) {
                Storage::disk('public')->delete($file);
            }
        }

        // --- 2. GET DATA ---
        $package = Package::findOrFail($request->package_id);
        $template = MessageTemplate::where('package_id', $package->id)->first();

        $storagePath = null;
        $text = $template->whatsapp_text ?? "Details for: {$package->package_name}";

        switch ($request->media_type) {
            case 'template':
                $storagePath = $template?->getRawOriginal('whatsapp_media');
                break;
            case 'banner':
                $storagePath = $package->getRawOriginal('package_banner');
                break;
            case 'docs':
                $docs = $package->package_docs;
                $storagePath = is_array($docs) ? $docs[0] ?? null : $docs;
                break;
        }

        if (!$storagePath || !Storage::disk('public')->exists($storagePath)) {
            return response()->json(['status' => 'error', 'message' => 'Original file not found.'], 422);
        }

        // --- 3. CREATE TEMP FILE WITH FRIENDLY NAME ---

        // Get extension (pdf, jpg, etc)
        $extension = pathinfo($storagePath, PATHINFO_EXTENSION) ?: 'pdf';

        // Create clean name: "Chopta-Tungnath-Package.pdf"
        $cleanName = Str::slug($package->package_name) . '.' . $extension;

        // Define temp path
        $tempPath = "temp_whatsapp/{$cleanName}";

        // Copy original to temp (overwrite if exists to ensure latest version)
        // We use copy() so we don't move/delete the original
        Storage::disk('public')->put($tempPath, Storage::disk('public')->get($storagePath));

        // --- 4. SEND API REQUEST ---

        // Generate URL for the RENAMED file
        $fileUrl = Storage::disk('public')->url($tempPath);
        $apiKey = auth()->user()->whatsapp_api_key;

        if (!$apiKey) {
            return response()->json(['status' => 'error', 'message' => 'API Key missing'], 422);
        }

        $apiUrl = 'https://wabot.adxventure.com/api/user/send-media-message';

        try {
            $response = Http::timeout(30)->get($apiUrl, [
                'recipient' => $request->recipient,
                'apikey' => $apiKey,
                'text' => $text,
                'file' => $fileUrl, // Now sending .../temp_whatsapp/Chopta-Tungnath.pdf
                'filename' => $cleanName,
            ]);

            return response()->json([
                'status' => 'success',
                'sent_as' => $cleanName,
                'temp_url_used' => $fileUrl,
                'api_response' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
