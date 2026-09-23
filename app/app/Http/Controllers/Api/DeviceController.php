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

        if (!$timer) {
            return response()->json([
                'active' => false,
                'status' => 'IDLE',
                'task_name' => 'No active timer',
                'elapsed' => 0
            ]);
        }

        return response()->json([
            'active' => true,
            'status' => $timer->is_running ? 'RUNNING' : 'PAUSED',
            'task_name' => $timer->purpose ?? 'General Timer',
            'elapsed' => $timer->accumulated_seconds + ($timer->is_running ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) : 0)
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
            if ($timer->is_running) {
                $timer->accumulated_seconds += floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000);
            }
            $timer->is_running = false;
            $timer->completed_at = now();
            $timer->save();
        }

        return response()->json([
            'active' => false,
            'status' => 'IDLE',
            'task_name' => 'No active timer',
            'elapsed' => 0
        ]);
    }
}
