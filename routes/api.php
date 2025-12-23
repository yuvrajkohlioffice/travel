<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\FollowupReasonController;

/*
|--------------------------------------------------------------------------
| Public APIs
|--------------------------------------------------------------------------
*/

Route::post('/send-text', [WhatsAppController::class, 'sendText']);
Route::post('/send-media', [WhatsAppController::class, 'sendMedia']);
Route::post('/send-media-json', [WhatsAppController::class, 'sendMediaJson']);

/*
|--------------------------------------------------------------------------
| Authenticated APIs
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/followup-reasons', [FollowupReasonController::class, 'indexApi']);

});
