<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Middleware\CheckMacropadToken;

Route::middleware([CheckMacropadToken::class])->prefix('device')->group(function () {
    Route::get('/status', [DeviceController::class, 'status']);
    Route::post('/timer/toggle', [DeviceController::class, 'toggleTimer']);
    Route::post('/timer/stop', [DeviceController::class, 'stopTimer']);
    Route::post('/timer/reset', [DeviceController::class, 'resetTimer']);
});
