<?php

namespace App\Livewire;

use App\Models\Timer;
use Livewire\Component;

class GlobalTimer extends Component
{
    public $timerId;
    public $initialState;

    public function getListeners()
    {
        $userId = auth()->id();
        return [
            "echo:users.{$userId},.App\\Events\\TimerSwitched" => 'onTimerSwitched',
        ];
    }

    public function onTimerSwitched($event)
    {
        // When hardware creates a new timer, update Livewire state
        $this->timerId = $event['timerId'];
        $this->initialState = $event['initialState'];
        
        // Dispatch to Alpine.js to restart its window.Timer instance
        $this->dispatch('timer-switched', timerId: $this->timerId, initialState: $this->initialState);
    }

    public function isSystemUser()
    {
        return auth()->check() && strtolower(auth()->user()->name) === 'system';
    }

    public function mount()
    {
        // For demonstration, fetch the very first timer or create a dummy one
        $timer = Timer::where('user_id', auth()->id())->where('is_running', true)->whereNull('completed_at')->latest('updated_at')->first() ?? Timer::where('user_id', auth()->id())->whereNull('completed_at')->latest('updated_at')->first();
        
        if (!$timer) {
            $timer = Timer::create([
                'user_id' => auth()->id(),

                'purpose' => 'global_focus',
                'accumulated_seconds' => 0,
                'is_running' => false,
            ]);
        }

        $this->timerId = $timer->id;
        $lastTimer = \App\Models\Timer::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();
        $lastDuration = $lastTimer ? $lastTimer->accumulated_seconds : 0;

        $this->initialState = [
            'accumulated_seconds' => $timer->accumulated_seconds,
            'is_running' => $timer->is_running,
            'last_started_at' => $timer->last_started_at,
            'last_duration' => $lastDuration,
        ];
    }

    
    #[\Livewire\Attributes\On('start-timer')]
    public function startTimerFor($type, $id)
    {
        // Find entity
        $entity = $type::find($id);
        if (!$entity) return;

        // Find existing timer for this entity
        $timer = $entity->timers()->where('user_id', auth()->id())->whereNull('completed_at')->first();
            
        if (!$timer) {
            $timer = Timer::create([
                'user_id' => auth()->id(),
                'purpose' => 'task_tracking',
                'accumulated_seconds' => 0,
                'is_running' => false,
            ]);
            
            // Attach to entity securely
            try {
                $timer->validateHierarchyAttachments($type, $id);
                if ($type === \App\Models\Task::class) {
                    $timer->tasks()->attach($id);
                } elseif ($type === \App\Models\Project::class) {
                    $timer->projects()->attach($id);
                } elseif ($type === \App\Models\Client::class) {
                    $timer->clients()->attach($id);
                }
            } catch (\Exception $e) {
                $timer->delete();
                session()->flash('error', $e->getMessage());
                return;
            }
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
        if ($this->isSystemUser()) return;
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
        $lastTimer = \App\Models\Timer::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();
        $lastDuration = $lastTimer ? $lastTimer->accumulated_seconds : 0;

        $this->initialState = [
            'accumulated_seconds' => $timer->accumulated_seconds,
            'is_running' => $timer->is_running,
            'last_started_at' => $timer->last_started_at,
            'last_duration' => $lastDuration,
        ];
        
        // Dispatch browser event to re-initialize alpine component
        
        $this->dispatch('timer-switched', timerId: $this->timerId, initialState: $this->initialState);
        broadcast(new \App\Events\TimerSwitched(1, $this->timerId, $this->initialState));
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
            'user_id' => auth()->id(),

            'purpose' => 'global_focus',
            'accumulated_seconds' => 0,
            'is_running' => false,
        ]);
        
        $this->timerId = $newTimer->id;
        $lastTimer = Timer::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();
        $this->initialState = [
            'accumulated_seconds' => 0,
            'is_running' => false,
            'last_started_at' => null,
            'last_duration' => $lastTimer ? $lastTimer->accumulated_seconds : 0,
        ];
        
        
        $this->dispatch('timer-switched', timerId: $this->timerId, initialState: $this->initialState);
        broadcast(new \App\Events\TimerSwitched(1, $this->timerId, $this->initialState));
    }

    
    #[\Livewire\Attributes\On('force-new-timer')]
    public function forceStartNewTimer()
    {
        if ($this->isSystemUser()) return;
        
        if ($this->isSystemUser()) return;
        $now = floor(microtime(true) * 1000);
        
        $otherRunningTimers = Timer::where('user_id', auth()->id())->where('is_running', true)->get();
        foreach ($otherRunningTimers as $otherTimer) {
            $elapsed = $otherTimer->last_started_at ? floor((floor(microtime(true) * 1000) - $otherTimer->last_started_at) / 1000) : 0;
            $newSeconds = $otherTimer->accumulated_seconds + $elapsed;
            $otherTimer->update([
                'accumulated_seconds' => $newSeconds,
                'is_running' => false,
                'last_started_at' => null,
            ]);
            $openLog = $otherTimer->logs()->whereNull('stopped_at')->latest()->first();
            if ($openLog) {
                $openLog->update([
                    'stopped_at' => $now,
                    'duration_seconds' => $elapsed,
                ]);
            }
            broadcast(new \App\Events\TimerUpdated($otherTimer));
        }

        $newTimer = Timer::create([
            'user_id' => auth()->id(),

            'purpose' => 'global_focus',
            'accumulated_seconds' => 0,
            'is_running' => true,
            'last_started_at' => $now,
        ]);
        
        $newTimer->logs()->create([
            'started_at' => $now,
        ]);
        
        $this->timerId = $newTimer->id;
        $lastTimer = Timer::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();
        $this->initialState = [
            'accumulated_seconds' => 0,
            'is_running' => true,
            'last_started_at' => $now,
            'last_duration' => $lastTimer ? $lastTimer->accumulated_seconds : 0,
        ];
        
        
        $this->dispatch('timer-switched', timerId: $this->timerId, initialState: $this->initialState);
        broadcast(new \App\Events\TimerSwitched(1, $this->timerId, $this->initialState));
    }

    public function render()
    {
        return view('livewire.global-timer');
    }
}
