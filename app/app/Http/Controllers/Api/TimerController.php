<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timer;
use App\Models\TimerLog;
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

        $wasRunning = $timer->is_running;
        $isNowRunning = $validated['is_running'];
        $oldSeconds = $timer->accumulated_seconds;

        $timer->update($validated);

        if ($isNowRunning && !$wasRunning) {
            // Starting the timer.
            // 1. Stop all other running timers
            $otherRunningTimers = Timer::where('is_running', true)
                ->where('id', '!=', $timer->id)
                ->get();
                
            foreach ($otherRunningTimers as $otherTimer) {
                $elapsed = $otherTimer->last_started_at ? floor((floor(microtime(true) * 1000) - $otherTimer->last_started_at) / 1000) : 0;
                $newSeconds = $otherTimer->accumulated_seconds + $elapsed;
                
                $otherTimer->update([
                    'accumulated_seconds' => $newSeconds,
                    'is_running' => false,
                    'last_started_at' => null,
                ]);

                // Close its log
                $openLog = $otherTimer->logs()->whereNull('stopped_at')->latest()->first();
                if ($openLog) {
                    $openLog->update([
                        'stopped_at' => floor(microtime(true) * 1000),
                        'duration_seconds' => $newSeconds - $otherTimer->accumulated_seconds,
                    ]);
                }

                broadcast(new TimerUpdated($otherTimer));
            }

            // 2. Open a new log for THIS timer
            $timer->logs()->create([
                'started_at' => $validated['last_started_at'] ?? floor(microtime(true) * 1000),
            ]);
            
        } elseif (!$isNowRunning && $wasRunning) {
            // Pausing the timer.
            // Close the open log
            $openLog = $timer->logs()->whereNull('stopped_at')->latest()->first();
            if ($openLog) {
                $openLog->update([
                    'stopped_at' => floor(microtime(true) * 1000),
                    'duration_seconds' => $validated['accumulated_seconds'] - $oldSeconds,
                ]);
            }
        }

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
        $oldSeconds = $timer->accumulated_seconds;
        $lastStartedAt = $timer->last_started_at;
        
        $timer->update([
            'is_running' => false,
            'last_started_at' => null,
            'completed_at' => now(),
        ]);

        // Close any open log
        $openLog = $timer->logs()->whereNull('stopped_at')->latest()->first();
        if ($openLog) {
            $elapsed = $lastStartedAt ? floor((floor(microtime(true) * 1000) - $lastStartedAt) / 1000) : 0;
            $openLog->update([
                'stopped_at' => floor(microtime(true) * 1000),
                'duration_seconds' => $elapsed,
            ]);
        }

        broadcast(new TimerUpdated($timer));

        return response()->json(['success' => true]);
    }
}
