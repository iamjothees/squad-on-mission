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
            'elapsed' => $timer->accumulated_seconds + ($timer->is_running ? (now()->timestamp - $timer->last_started_at) : 0)
        ]);
    }

    public function toggleTimer(Request $request)
    {
        $user = $this->getUser($request);
        $timer = Timer::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->first();

        if (!$timer) {
            // Start a generic timer if none exists
            $timer = Timer::create([
                'user_id' => $user->id,
                'purpose' => 'Quick Task',
                'is_running' => true,
                'last_started_at' => now(),
                'accumulated_seconds' => 0,
            ]);
            
            return response()->json(['status' => 'RUNNING', 'elapsed' => 0]);
        }

        if ($timer->is_running) {
            // Pause it
            $timer->accumulated_seconds += (now()->timestamp - $timer->last_started_at);
            $timer->is_running = false;
            $timer->save();
            return response()->json(['status' => 'PAUSED', 'elapsed' => $timer->accumulated_seconds]);
        } else {
            // Resume it
            $timer->last_started_at = now()->timestamp;
            $timer->is_running = true;
            $timer->save();
            return response()->json(['status' => 'RUNNING', 'elapsed' => $timer->accumulated_seconds]);
        }
    }

    public function stopTimer(Request $request)
    {
        $user = $this->getUser($request);
        $timer = Timer::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->first();

        if ($timer) {
            if ($timer->is_running) {
                $timer->accumulated_seconds += (now()->timestamp - $timer->last_started_at);
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
