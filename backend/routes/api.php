<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ChatController;

Route::prefix('v1')->group(function () {
    Route::get('services', [ServiceController::class, 'index'])
        ->middleware('throttle:120,1');
    Route::get('services/{service}', [ServiceController::class, 'show'])
        ->middleware('throttle:120,1');

    Route::get('chat/history/{sessionId}', [ChatController::class, 'history'])
        ->middleware('throttle:120,1');
    Route::post('chat/message', [ChatController::class, 'message'])
        ->middleware('throttle:30,1');
});