<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// For the physical device Macropad
Route::middleware(function (Request $request, $next) {
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $user = \App\Models\User::where('macropad_token', $token)->first();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Bind the user to the request so controllers can use it
    $request->merge(['_macropad_user_id' => $user->id]);
    
    return $next($request);
})->prefix('device')->group(function () {
    Route::get('/status', [\App\Http\Controllers\Api\DeviceController::class, 'status']);
    Route::post('/timer/toggle', [\App\Http\Controllers\Api\DeviceController::class, 'toggleTimer']);
    Route::post('/timer/stop', [\App\Http\Controllers\Api\DeviceController::class, 'stopTimer']);
});
