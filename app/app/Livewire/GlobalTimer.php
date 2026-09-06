<?php

namespace App\Livewire;

use App\Models\Timer;
use Livewire\Component;

class GlobalTimer extends Component
{
    public $timerId;
    public $initialState;

    public function mount()
    {
        // For demonstration, fetch the very first timer or create a dummy one
        $timer = Timer::where('is_running', true)->whereNull('completed_at')->latest('updated_at')->first() ?? Timer::whereNull('completed_at')->latest('updated_at')->first();
        
        if (!$timer) {
            $timer = Timer::create([
                'timerable_type' => 'App\Models\User', // Dummy attachment
                'timerable_id' => 1,
                'purpose' => 'global_focus',
                'accumulated_seconds' => 0,
                'is_running' => false,
            ]);
        }

        $this->timerId = $timer->id;
        $this->initialState = [
            'accumulated_seconds' => $timer->accumulated_seconds,
            'is_running' => $timer->is_running,
            'last_started_at' => $timer->last_started_at,
        ];
    }

    
    #[\Livewire\Attributes\On('start-timer')]
    public function startTimerFor($type, $id)
    {
        // Find or create timer for entity
        $timer = Timer::where('timerable_type', $type)
            ->where('timerable_id', $id)
            ->whereNull('completed_at')
            ->first();
            
        if (!$timer) {
            $timer = Timer::create([
                'timerable_type' => $type,
                'timerable_id' => $id,
                'purpose' => 'task_tracking',
                'accumulated_seconds' => 0,
                'is_running' => false,
            ]);
        }

        // If THIS timer is already running, do nothing!
        if ($timer->is_running) {
            return;
        }

        // Stop all other timers

        $otherRunningTimers = Timer::where('is_running', true)
            ->where('id', '!=', $timer->id)
            ->get();
            
        foreach ($otherRunningTimers as $otherTimer) {
            $elapsed = $otherTimer->last_started_at ? floor((floor(microtime(true) * 1000) - $otherTimer->last_started_at) / 1000) : 0;
            $newSeconds = $otherTimer->accumulated_seconds + $elapsed;
            
            $oldSeconds = $otherTimer->accumulated_seconds;
            $otherTimer->update([
                'accumulated_seconds' => $newSeconds,
                'is_running' => false,
                'last_started_at' => null,
            ]);

            $openLog = $otherTimer->logs()->whereNull('stopped_at')->latest()->first();
            if ($openLog) {
                $openLog->update([
                    'stopped_at' => floor(microtime(true) * 1000),
                    'duration_seconds' => $newSeconds - $oldSeconds,
                ]);
            }
            broadcast(new \App\Events\TimerUpdated($otherTimer));
        }

        // Start this timer
        $now = floor(microtime(true) * 1000);
        $timer->update([
            'is_running' => true,
            'last_started_at' => $now,
        ]);
        
        $timer->logs()->create([
            'started_at' => $now,
        ]);
        
        broadcast(new \App\Events\TimerUpdated($timer));

        $this->timerId = $timer->id;
        $this->initialState = [
            'accumulated_seconds' => $timer->accumulated_seconds,
            'is_running' => $timer->is_running,
            'last_started_at' => $timer->last_started_at,
        ];
        
        // Dispatch browser event to re-initialize alpine component
        $this->dispatch('timer-switched', timerId: $this->timerId, initialState: $this->initialState);
    }

    
    public function stopTimer()
    {
        $timer = Timer::find($this->timerId);
        if ($timer) {
            $lastStartedAt = $timer->last_started_at;
            $elapsed = $lastStartedAt ? floor((floor(microtime(true) * 1000) - $lastStartedAt) / 1000) : 0;
            
            $timer->update([
                'accumulated_seconds' => $timer->accumulated_seconds + $elapsed,
                'is_running' => false,
                'last_started_at' => null,
                'completed_at' => now(),
            ]);

            $openLog = $timer->logs()->whereNull('stopped_at')->latest()->first();
            if ($openLog) {
                $openLog->update([
                    'stopped_at' => floor(microtime(true) * 1000),
                    'duration_seconds' => $elapsed,
                ]);
            }
            broadcast(new \App\Events\TimerUpdated($timer));
        }

        // Restart with a fresh global dummy timer so the UI resets (PAUSED)
        $newTimer = Timer::create([
            'timerable_type' => 'App\\Models\\User',
            'timerable_id' => 1,
            'purpose' => 'global_focus',
            'accumulated_seconds' => 0,
            'is_running' => false,
        ]);
        
        $this->timerId = $newTimer->id;
        $this->initialState = [
            'accumulated_seconds' => 0,
            'is_running' => false,
            'last_started_at' => null,
        ];
        
        $this->dispatch('timer-switched', timerId: $this->timerId, initialState: $this->initialState);
    }

    public function render()
    {
        return view('livewire.global-timer');
    }
}
