<?php

namespace App\Livewire\Dashboard;

use App\Models\TimerLog;
use Livewire\Component;
use Carbon\Carbon;

class TimeStats extends Component
{
    public function render()
    {
        $todayMs = now()->startOfDay()->timestamp * 1000;
        $weekMs = now()->startOfWeek()->timestamp * 1000;

        $todayLogs = TimerLog::where('started_at', '>=', $todayMs)->get();
        $todaySeconds = $todayLogs->sum(function($log) {
            if ($log->duration_seconds !== null) return $log->duration_seconds;
            return floor((floor(microtime(true) * 1000) - $log->started_at) / 1000);
        });

        $weekLogs = TimerLog::where('started_at', '>=', $weekMs)->get();
        $weekSeconds = $weekLogs->sum(function($log) {
            if ($log->duration_seconds !== null) return $log->duration_seconds;
            return floor((floor(microtime(true) * 1000) - $log->started_at) / 1000);
        });

        return view('livewire.dashboard.time-stats', compact('todaySeconds', 'weekSeconds'));
    }
}
