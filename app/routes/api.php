<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// For the physical device Macropad
Route::middleware(function (Request $request, $next) {
    $token = $request->bearerToken();
    if (!$token || $token !== env('DEVICE_API_TOKEN', 'secret-macropad-token')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    return $next($request);
})->prefix('device')->group(function () {
    Route::get('/status', [\App\Http\Controllers\Api\DeviceController::class, 'status']);
    Route::post('/timer/toggle', [\App\Http\Controllers\Api\DeviceController::class, 'toggleTimer']);
    Route::post('/timer/stop', [\App\Http\Controllers\Api\DeviceController::class, 'stopTimer']);
});
