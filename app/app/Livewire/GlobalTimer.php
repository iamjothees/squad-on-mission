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
        $timer = Timer::first();
        
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

    public function render()
    {
        return view('livewire.global-timer');
    }
}
