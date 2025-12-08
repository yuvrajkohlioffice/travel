<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

Route::post('/send-text', [WhatsAppController::class, 'sendText']);
Route::post('/send-media', [WhatsAppController::class, 'sendMedia']);
Route::post('/send-media-json', [WhatsAppController::class, 'sendMediaJson']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
