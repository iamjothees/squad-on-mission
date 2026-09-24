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
    
    public $prevTasks = [];
    public $prevProjects = [];
    public $prevClients = [];
    
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
        
        $prevRange = match($this->reportPeriod) {
            'today' => [$now->copy()->subDay()->startOfDay()->timestamp * 1000, $now->copy()->subDay()->endOfDay()->timestamp * 1000],
            'this_week' => [$now->copy()->subWeek()->startOfWeek()->timestamp * 1000, $now->copy()->subWeek()->endOfWeek()->timestamp * 1000],
            'this_month' => [$now->copy()->subMonth()->startOfMonth()->timestamp * 1000, $now->copy()->subMonth()->endOfMonth()->timestamp * 1000],
        };
        
        // Fetch Current
        $this->tasks = [];
        $this->projects = [];
        $this->clients = [];
        $this->fetchDataInto($userId, $range, $this->tasks, $this->projects, $this->clients);
        
        // Fetch Previous
        $this->prevTasks = [];
        $this->prevProjects = [];
        $this->prevClients = [];
        $this->fetchDataInto($userId, $prevRange, $this->prevTasks, $this->prevProjects, $this->prevClients);
        
        arsort($this->tasks);
        arsort($this->projects);
        arsort($this->clients);
    }
    
    private function fetchDataInto($userId, $range, &$tasksData, &$projectsData, &$clientsData) {
        $logs = TimerLog::with('timer.tasks', 'timer.projects', 'timer.clients')
            ->whereHas('timer', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('started_at', $range)
            ->get();
            
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
    
    public function getChangePercentage($current, $previous) {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        $diff = $current - $previous;
        return round(($diff / $previous) * 100);
    }
};
?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-[#1C1C1E] border border-gray-800 rounded p-4 gap-4">
        <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
            <i data-lucide="filter" class="w-4 h-4 text-emerald-400"></i> Period Selection
        </h3>
        <select wire:model.live="reportPeriod" class="bg-[#2C2C2E] border border-gray-700 text-gray-200 text-sm rounded px-3 py-1.5 focus:outline-none focus:border-indigo-500">
            <option value="today">Today (vs Yesterday)</option>
            <option value="this_week">This Week (vs Last Week)</option>
            <option value="this_month">This Month (vs Last Month)</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Projects -->
        <div class="bg-[#1C1C1E] border border-gray-800 rounded p-6">
            <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="folder" class="w-4 h-4 text-indigo-400"></i> Top Projects
            </h4>
            
            <div class="space-y-6">
                @php $maxProject = !empty($projects) ? max($projects) : 1; @endphp
                @forelse(array_slice($projects, 0, 5) as $name => $secs)
                    @php 
                        $prevSecs = $prevProjects[$name] ?? 0;
                        $delta = $this->getChangePercentage($secs, $prevSecs);
                    @endphp
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-gray-200 text-sm font-medium truncate pr-4">{{ $name }}</span>
                            <div class="text-right flex flex-col items-end">
                                <span class="text-gray-300 font-bold tabular-nums">{{ $this->formatTime($secs) }}</span>
                                <span class="text-[10px] {{ $delta >= 0 ? 'text-green-500' : 'text-red-500' }}">{{ $delta > 0 ? '+' : '' }}{{ $delta }}% vs prev</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($secs, $maxProject) }}%"></div>
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
            
            <div class="space-y-6">
                @php $maxTask = !empty($tasks) ? max($tasks) : 1; @endphp
                @forelse(array_slice($tasks, 0, 5) as $name => $secs)
                    @php 
                        $prevSecs = $prevTasks[$name] ?? 0;
                        $delta = $this->getChangePercentage($secs, $prevSecs);
                    @endphp
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-gray-200 text-sm font-medium truncate pr-4">{{ $name }}</span>
                            <div class="text-right flex flex-col items-end">
                                <span class="text-gray-300 font-bold tabular-nums">{{ $this->formatTime($secs) }}</span>
                                <span class="text-[10px] {{ $delta >= 0 ? 'text-green-500' : 'text-red-500' }}">{{ $delta > 0 ? '+' : '' }}{{ $delta }}% vs prev</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($secs, $maxTask) }}%"></div>
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
                @forelse(array_slice($clients, 0, 9) as $name => $secs)
                    @php 
                        $prevSecs = $prevClients[$name] ?? 0;
                        $delta = $this->getChangePercentage($secs, $prevSecs);
                    @endphp
                    <div class="bg-[#2C2C2E] border border-gray-700 rounded p-3 relative overflow-hidden group">
                        <div class="flex justify-between items-start mb-3 relative z-10">
                            <span class="text-gray-200 text-xs font-medium truncate pr-2">{{ $name }}</span>
                            <div class="flex flex-col items-end">
                                <span class="text-purple-400 text-xs font-bold tabular-nums">{{ $this->formatTime($secs) }}</span>
                                <span class="text-[9px] {{ $delta >= 0 ? 'text-green-500' : 'text-red-500' }} mt-0.5">{{ $delta > 0 ? '+' : '' }}{{ $delta }}%</span>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-purple-500/50 transition-all duration-500" style="width: {{ $this->getWidthPercentage($secs, $maxClient) }}%"></div>
                    </div>
                @empty
                    <div class="text-xs text-gray-600 text-center py-4 col-span-full">No client data for this period.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>