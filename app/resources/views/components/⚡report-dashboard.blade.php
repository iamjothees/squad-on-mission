<?php

use Livewire\Component;
use App\Models\TimerLog;
use App\Models\Timer;
use App\Models\Task;
use App\Models\Project;
use App\Models\Client;
use Carbon\Carbon;

new class extends Component
{
    public $timeData = [];
    public $topEntities = [];
    
    public function mount() {
        $this->calculateReports();
    }
    
    public function calculateReports() {
        $userId = auth()->id();
        $now = Carbon::now();
        
        $periods = [
            'today' => [$now->copy()->startOfDay()->timestamp * 1000, $now->copy()->endOfDay()->timestamp * 1000],
            'yesterday' => [$now->copy()->subDay()->startOfDay()->timestamp * 1000, $now->copy()->subDay()->endOfDay()->timestamp * 1000],
            'this_week' => [$now->copy()->startOfWeek()->timestamp * 1000, $now->copy()->endOfWeek()->timestamp * 1000],
            'last_week' => [$now->copy()->subWeek()->startOfWeek()->timestamp * 1000, $now->copy()->subWeek()->endOfWeek()->timestamp * 1000],
            'this_month' => [$now->copy()->startOfMonth()->timestamp * 1000, $now->copy()->endOfMonth()->timestamp * 1000],
            'last_month' => [$now->copy()->subMonth()->startOfMonth()->timestamp * 1000, $now->copy()->subMonth()->endOfMonth()->timestamp * 1000],
        ];
        
        $this->timeData = [];
        foreach ($periods as $key => $range) {
            $sum = TimerLog::whereHas('timer', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->whereBetween('started_at', $range)->sum('duration_seconds');
            $this->timeData[$key] = $sum;
        }
        
        // Entity calculations (All Time for simplicity, or This Month)
        $monthRange = $periods['this_month'];
        $logsThisMonth = TimerLog::with('timer.tasks', 'timer.projects', 'timer.clients')
            ->whereHas('timer', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('started_at', $monthRange)
            ->get();
            
        $tasks = [];
        $projects = [];
        $clients = [];
        
        foreach ($logsThisMonth as $log) {
            $dur = $log->duration_seconds;
            if (!$dur) continue;
            
            if ($log->timer) {
                foreach ($log->timer->tasks as $t) {
                    $tasks[$t->name] = ($tasks[$t->name] ?? 0) + $dur;
                }
                foreach ($log->timer->projects as $p) {
                    $projects[$p->name] = ($projects[$p->name] ?? 0) + $dur;
                }
                foreach ($log->timer->clients as $c) {
                    $clients[$c->name] = ($clients[$c->name] ?? 0) + $dur;
                }
            }
        }
        
        arsort($tasks);
        arsort($projects);
        arsort($clients);
        
        $this->topEntities = [
            'tasks' => array_slice($tasks, 0, 5),
            'projects' => array_slice($projects, 0, 5),
            'clients' => array_slice($clients, 0, 5),
        ];
    }
    
    public function formatTime($seconds) {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        if ($h > 0) return "{$h}h {$m}m";
        return "{$m}m";
    }
    
    public function getChangePercentage($current, $previous) {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        $diff = $current - $previous;
        return round(($diff / $previous) * 100);
    }
};
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
    <!-- Time Worked Comparison -->
    <div class="bg-[#1C1C1E] border border-gray-800 rounded p-4">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 text-indigo-400"></i> Time Invested
        </h3>
        
        <div class="space-y-4">
            <!-- Today vs Yesterday -->
            @php 
                $todayP = $this->getChangePercentage($timeData['today'], $timeData['yesterday']);
                $weekP = $this->getChangePercentage($timeData['this_week'], $timeData['last_week']);
                $monthP = $this->getChangePercentage($timeData['this_month'], $timeData['last_month']);
            @endphp
            
            <div class="flex justify-between items-end border-b border-gray-800 pb-3">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Today</div>
                    <div class="text-xl font-bold text-gray-200 tabular-nums">{{ $this->formatTime($timeData['today']) }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-gray-500 mb-1">Yesterday: {{ $this->formatTime($timeData['yesterday']) }}</div>
                    <div class="text-xs font-medium {{ $todayP >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $todayP > 0 ? '+' : '' }}{{ $todayP }}%
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-end border-b border-gray-800 pb-3">
                <div>
                    <div class="text-xs text-gray-500 mb-1">This Week</div>
                    <div class="text-xl font-bold text-gray-200 tabular-nums">{{ $this->formatTime($timeData['this_week']) }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-gray-500 mb-1">Last Week: {{ $this->formatTime($timeData['last_week']) }}</div>
                    <div class="text-xs font-medium {{ $weekP >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $weekP > 0 ? '+' : '' }}{{ $weekP }}%
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-end">
                <div>
                    <div class="text-xs text-gray-500 mb-1">This Month</div>
                    <div class="text-xl font-bold text-gray-200 tabular-nums">{{ $this->formatTime($timeData['this_month']) }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-gray-500 mb-1">Last Month: {{ $this->formatTime($timeData['last_month']) }}</div>
                    <div class="text-xs font-medium {{ $monthP >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $monthP > 0 ? '+' : '' }}{{ $monthP }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Entities (This Month) -->
    <div class="bg-[#1C1C1E] border border-gray-800 rounded p-4 flex flex-col gap-4">
        <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
            <i data-lucide="bar-chart-2" class="w-4 h-4 text-emerald-400"></i> Top Focus (This Month)
        </h3>
        
        <div class="grid grid-cols-2 gap-4">
            <!-- Projects -->
            <div>
                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Projects</div>
                <div class="space-y-2">
                    @forelse($topEntities['projects'] as $name => $secs)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-300 truncate max-w-[100px]">{{ $name }}</span>
                            <span class="text-emerald-400 font-medium tabular-nums">{{ $this->formatTime($secs) }}</span>
                        </div>
                    @empty
                        <div class="text-xs text-gray-600">No data</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Tasks -->
            <div>
                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Tasks</div>
                <div class="space-y-2">
                    @forelse($topEntities['tasks'] as $name => $secs)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-300 truncate max-w-[100px]">{{ $name }}</span>
                            <span class="text-emerald-400 font-medium tabular-nums">{{ $this->formatTime($secs) }}</span>
                        </div>
                    @empty
                        <div class="text-xs text-gray-600">No data</div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-3 mt-auto">
            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Top Clients</div>
            <div class="flex gap-4">
                @forelse($topEntities['clients'] as $name => $secs)
                    <div class="bg-[#2C2C2E] rounded px-2 py-1 text-[10px]">
                        <span class="text-gray-300">{{ $name }}</span>
                        <span class="text-indigo-400 ml-1 font-bold">{{ $this->formatTime($secs) }}</span>
                    </div>
                @empty
                    <div class="text-xs text-gray-600">No data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>