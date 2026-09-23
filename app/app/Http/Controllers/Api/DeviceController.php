<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timer;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    private function getUser(Request $request)
    {
        return User::find($request->_macropad_user_id);
    }

    public function status(Request $request)
    {
        $user = $this->getUser($request);
        $timer = Timer::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->orderByDesc('is_running')
            ->latest('updated_at')
            ->first();

        $lastTimer = Timer::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();
        $lastDuration = $lastTimer ? $lastTimer->accumulated_seconds : 0;

        if (!$timer) {
            return response()->json([
                'user_id' => $user->id,
                'active' => false,
                'status' => 'IDLE',
                'task_name' => 'Ready',
                'elapsed' => 0,
                'last_duration' => $lastDuration,
                'work_hours_per_day' => (float) config('squad.work_hours_per_day', 24)
            ]);
        }

        $timerables = $timer->timerables;
        $entity = $timerables->first();
        $hasMultiple = $timerables->count() > 1;
        
        $entityName = 'General Timer';
        if ($entity) {
            $entityName = $entity->title ?? $entity->name;
        } elseif ($timer->purpose) {
            $entityName = $timer->purpose;
        }

        return response()->json([
            'user_id' => $user->id,
            'active' => true,
            'status' => $timer->is_running ? 'RUNNING' : 'PAUSED',
            'task_name' => $entityName,
            'has_multiple' => $hasMultiple,
            'elapsed' => $timer->accumulated_seconds + ($timer->is_running ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) : 0),
            'work_hours_per_day' => (float) config('squad.work_hours_per_day', 24)
        ]);
    }

    public function toggleTimer(Request $request)
    {
        $user = $this->getUser($request);
        $timer = Timer::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->orderByDesc('is_running')
            ->latest('updated_at')
            ->first();

        if (!$timer) {
            // Start a generic timer if none exists
            $timer = Timer::create([
                'user_id' => $user->id,
                'purpose' => 'Quick Task',
                'is_running' => true,
                'last_started_at' => floor(microtime(true) * 1000),
                'accumulated_seconds' => 0,
            ]);
            
            broadcast(new \App\Events\TimerSwitched($user->id, $timer->id, [
                'accumulated_seconds' => 0,
                'is_running' => true,
                'last_started_at' => $timer->last_started_at,
            ]));
            
            return response()->json(['status' => 'RUNNING', 'elapsed' => 0]);
        }

        if ($timer->is_running) {
            // Pause it
            $timer->accumulated_seconds += floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000);
            $timer->is_running = false;
            $timer->save();
            broadcast(new \App\Events\TimerUpdated($timer));
            return response()->json(['status' => 'PAUSED', 'elapsed' => $timer->accumulated_seconds]);
        } else {
            // Resume it
            $timer->last_started_at = floor(microtime(true) * 1000);
            $timer->is_running = true;
            $timer->save();
            broadcast(new \App\Events\TimerUpdated($timer));
            return response()->json(['status' => 'RUNNING', 'elapsed' => $timer->accumulated_seconds]);
        }
    }

    public function stopTimer(Request $request)
    {
        $user = $this->getUser($request);
        $timer = Timer::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->orderByDesc('is_running')
            ->latest('updated_at')
            ->first();

        if ($timer) {
            $elapsed = 0;
            if ($timer->is_running) {
                $elapsed = floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000);
                $timer->accumulated_seconds += $elapsed;
            }
            
            $timer->is_running = false;
            $timer->last_started_at = null;
            $timer->completed_at = now();
            $timer->save();

            $openLog = $timer->logs()->whereNull('stopped_at')->latest()->first();
            if ($openLog) {
                $openLog->update([
                    'stopped_at' => floor(microtime(true) * 1000),
                    'duration_seconds' => $elapsed,
                ]);
            }
            
            // Create a fresh global dummy timer so the web UI resets to PAUSED properly
            $newTimer = Timer::create([
                'user_id' => $user->id,
                'purpose' => 'global_focus',
                'accumulated_seconds' => 0,
                'is_running' => false,
            ]);
            
            // Broadcast TimerSwitched so the frontend completely detaches from the completed timer
            // and hooks onto the new one seamlessly.
            broadcast(new \App\Events\TimerSwitched($user->id, $newTimer->id, [
                'accumulated_seconds' => 0,
                'is_running' => false,
                'last_started_at' => null,
                'last_duration' => $timer->accumulated_seconds,
            ]));
            
            // Also broadcast TimerUpdated for the old timer just in case any UI needs to mark it stopped.
            broadcast(new \App\Events\TimerUpdated($timer));
        }

        return response()->json([
            'user_id' => $user->id,
            'active' => false,
            'status' => 'IDLE',
            'task_name' => 'No active timer',
            'elapsed' => 0
        ]);
    }
}
