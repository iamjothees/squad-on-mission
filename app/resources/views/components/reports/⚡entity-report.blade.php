<?php

use Livewire\Component;
use App\Models\TimerLog;
use Carbon\Carbon;

new class extends Component
{
    public $reportPeriod = 'this_week'; // 'today', 'this_week', 'this_month'
    public $tasks = [];
    public $projects = [];
    public $clients = [];
    
    public function mount() {
        $this->calculateEntities();
    }
    
    public function updatedReportPeriod() {
        $this->calculateEntities();
    }
    
    public function calculateEntities() {
        $userId = auth()->id();
        $now = Carbon::now();
        
        $range = match($this->reportPeriod) {
            'today' => [$now->copy()->startOfDay()->timestamp * 1000, $now->copy()->endOfDay()->timestamp * 1000],
            'this_week' => [$now->copy()->startOfWeek()->timestamp * 1000, $now->copy()->endOfWeek()->timestamp * 1000],
            'this_month' => [$now->copy()->startOfMonth()->timestamp * 1000, $now->copy()->endOfMonth()->timestamp * 1000],
        };
        
        $logs = TimerLog::with('timer.tasks', 'timer.projects', 'timer.clients')
            ->whereHas('timer', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('started_at', $range)
            ->get();
            
        $tasksData = [];
        $projectsData = [];
        $clientsData = [];
        
        foreach ($logs as $log) {
            $dur = $log->duration_seconds;
            if (!$dur) continue;
            
            if ($log->timer) {
                foreach ($log->timer->tasks as $t) {
                    $tasksData[$t->name] = ($tasksData[$t->name] ?? 0) + $dur;
                }
                foreach ($log->timer->projects as $p) {
                    $projectsData[$p->name] = ($projectsData[$p->name] ?? 0) + $dur;
                }
                foreach ($log->timer->clients as $c) {
                    $clientsData[$c->name] = ($clientsData[$c->name] ?? 0) + $dur;
                }
            }
        }
        
        arsort($tasksData);
        arsort($projectsData);
        arsort($clientsData);
        
        $this->tasks = $tasksData;
        $this->projects = $projectsData;
        $this->clients = $clientsData;
    }
    
    public function formatTime($seconds) {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        return "{$h}h {$m}m";
    }
    
    public function getWidthPercentage($seconds, $maxSeconds) {
        if ($maxSeconds == 0) return 0;
        return min(100, round(($seconds / $maxSeconds) * 100));
    }
};
?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="flex justify-between items-center bg-[#1C1C1E] border border-gray-800 rounded p-4">
        <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
            <i data-lucide="filter" class="w-4 h-4 text-emerald-400"></i> Period Selection
        </h3>
        <select wire:model.live="reportPeriod" class="bg-[#2C2C2E] border border-gray-700 text-gray-200 text-sm rounded px-3 py-1.5 focus:outline-none focus:border-indigo-500">
            <option value="today">Today</option>
            <option value="this_week">This Week</option>
            <option value="this_month">This Month</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Projects -->
        <div class="bg-[#1C1C1E] border border-gray-800 rounded p-6">
            <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="folder" class="w-4 h-4 text-indigo-400"></i> Top Projects
            </h4>
            
            <div class="space-y-4">
                @php $maxProject = !empty($projects) ? max($projects) : 1; @endphp
                @forelse($projects as $name => $secs)
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-200 font-medium truncate pr-4">{{ $name }}</span>
                            <span class="text-gray-400 tabular-nums">{{ $this->formatTime($secs) }}</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($secs, $maxProject) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-600 text-center py-4">No project data for this period.</div>
                @endforelse
            </div>
        </div>

        <!-- Tasks -->
        <div class="bg-[#1C1C1E] border border-gray-800 rounded p-6">
            <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="check-square" class="w-4 h-4 text-emerald-400"></i> Top Tasks
            </h4>
            
            <div class="space-y-4">
                @php $maxTask = !empty($tasks) ? max($tasks) : 1; @endphp
                @forelse(array_slice($tasks, 0, 10) as $name => $secs)
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-200 font-medium truncate pr-4">{{ $name }}</span>
                            <span class="text-gray-400 tabular-nums">{{ $this->formatTime($secs) }}</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($secs, $maxTask) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-600 text-center py-4">No task data for this period.</div>
                @endforelse
            </div>
        </div>
        
        <!-- Clients -->
        <div class="bg-[#1C1C1E] border border-gray-800 rounded p-6 md:col-span-2">
            <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-purple-400"></i> Top Clients
            </h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php $maxClient = !empty($clients) ? max($clients) : 1; @endphp
                @forelse($clients as $name => $secs)
                    <div class="bg-[#2C2C2E] border border-gray-700 rounded p-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-200 text-xs font-medium truncate">{{ $name }}</span>
                            <span class="text-purple-400 text-xs font-bold tabular-nums">{{ $this->formatTime($secs) }}</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1 overflow-hidden">
                            <div class="bg-purple-500 h-1 rounded-full" style="width: {{ $this->getWidthPercentage($secs, $maxClient) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-600 text-center py-4 col-span-full">No client data for this period.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>