<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Timer;

class TimerPlayButton extends Component
{
    public $type;
    public $id;
    public $isRunning = false;

    public function mount($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
        $this->checkStatus();
    }

    public function checkStatus()
    {
        
        $entity = $this->type::find($this->id);
        if ($entity) {
            $timer = $entity->timers()
                ->where('user_id', auth()->id())
                ->whereNull('completed_at')
                ->where('is_running', true)
                ->first();
            $this->isRunning = $timer !== null;
        } else {
            $this->isRunning = false;
        }
    }

    #[On('timer-switched')]
    public function onTimerSwitched()
    {
        $this->checkStatus();
    }
    
    public function getListeners()
    {
        $userId = auth()->id();
        return [
            "echo:users.{$userId},.App\\Events\\TimerSwitched" => 'checkStatus',
            "echo:users.{$userId},.TimerUpdated" => 'checkStatus',
        ];
    }

    public function toggle()
    {
        if ($this->isRunning) {
            // Already running. If they click it, maybe they want to focus on it?
            // GlobalTimer doesn't expose a 'focus' event, but dispatching 'start-timer' 
            // when it's already running will just return early in GlobalTimer.
            // We can just dispatch a generic notification or do nothing.
            // Dispatching 'start-timer' does nothing if already running.
            $this->dispatch('start-timer', type: $this->type, id: $this->id);
        } else {
            // Start it
            $this->dispatch('start-timer', type: $this->type, id: $this->id);
            // Optimistically update
            $this->isRunning = true;
        }
    }

    public function render()
    {
        return view('livewire.timer-play-button');
    }
}
