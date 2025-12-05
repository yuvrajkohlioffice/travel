<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    private $apiKey = "wb_doJFqo8M7PE_bot"; // your API key

    // -------------------------
    // SEND TEXT MESSAGE
    // -------------------------
    public function sendText(Request $request)
    {
        $response = Http::get("https://wabot.adxventure.com/api/user/send-message", [
            "recipient" => $request->recipient,
            "apikey"    => $this->apiKey,
            "text"      => $request->text,
        ]);

        return $response->json();
    }

    // -------------------------
    // SEND MEDIA MESSAGE (IMAGE / VIDEO / PDF)
    // -------------------------
    public function sendMedia(Request $request)
    {
        $response = Http::get("https://wabot.adxventure.com/api/user/send-media-message", [
            "recipient" => $request->recipient,
            "apikey"    => $this->apiKey,
            "text"      => $request->text,
            "file"      => $request->mediaUrl, // URL of file/image/video
        ]);

        return $response->json();
    }

    // -------------------------
    // SEND MEDIA WITH JSON BODY (Alternative)
    // -------------------------
    public function sendMediaJson(Request $request)
    {
        $payload = [
            "apiKey"    => $this->apiKey,
            "text"      => $request->text,
            "recipient" => $request->recipient,
            "mediaUrl"  => $request->mediaUrl,
            "mediaType" => $request->mediaType, // image / document / video
            "fileName"  => $request->fileName,
        ];

        $response = Http::post("https://wabot.adxventure.com/api/user/send-media-message", $payload);

        return $response->json();
    }
}
