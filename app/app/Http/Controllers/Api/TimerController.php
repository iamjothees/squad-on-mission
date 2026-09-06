<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timer;
use App\Events\TimerUpdated;
use Illuminate\Http\Request;

class TimerController extends Controller
{
    public function show(Timer $timer)
    {
        return response()->json([
            'id' => $timer->id,
            'accumulated_seconds' => $timer->accumulated_seconds,
            'is_running' => $timer->is_running,
            'last_started_at' => $timer->last_started_at,
        ]);
    }

    public function update(Request $request, Timer $timer)
    {
        $validated = $request->validate([
            'accumulated_seconds' => 'required|integer',
            'is_running' => 'required|boolean',
            'last_started_at' => 'nullable|integer',
        ]);

        $timer->update($validated);

        // Broadcast the event
        broadcast(new TimerUpdated($timer));

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $timer->id,
                'accumulated_seconds' => $timer->accumulated_seconds,
                'is_running' => $timer->is_running,
                'last_started_at' => $timer->last_started_at,
            ]
        ]);
    }
    
    public function stop(Timer $timer)
    {
        $timer->update([
            'accumulated_seconds' => 0,
            'is_running' => false,
            'last_started_at' => null,
        ]);

        broadcast(new TimerUpdated($timer));

        return response()->json(['success' => true]);
    }
}
