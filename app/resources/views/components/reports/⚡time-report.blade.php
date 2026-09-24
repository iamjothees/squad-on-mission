<?php

use Livewire\Component;
use App\Models\TimerLog;
use App\Models\User;
use Carbon\Carbon;

new class extends Component
{
    public $timeData = [];
    
    // Custom Comparison Properties
    public $basePeriod = 'this_week';
    public $comparePeriod = 'last_week';
    public $selectedUserId = 'all';
    
    public $users = [];
    
    public function mount() {
        $this->users = User::all();
        // Default to current user
        $this->selectedUserId = auth()->id();
        $this->calculateReports();
    }
    
    public function updated() {
        $this->calculateReports();
    }
    
    public function getRangeForPeriod($period, $now) {
        return match($period) {
            'today' => [$now->copy()->startOfDay()->timestamp * 1000, $now->copy()->endOfDay()->timestamp * 1000],
            'yesterday' => [$now->copy()->subDay()->startOfDay()->timestamp * 1000, $now->copy()->subDay()->endOfDay()->timestamp * 1000],
            'this_week' => [$now->copy()->startOfWeek()->timestamp * 1000, $now->copy()->endOfWeek()->timestamp * 1000],
            'last_week' => [$now->copy()->subWeek()->startOfWeek()->timestamp * 1000, $now->copy()->subWeek()->endOfWeek()->timestamp * 1000],
            '2_weeks_ago' => [$now->copy()->subWeeks(2)->startOfWeek()->timestamp * 1000, $now->copy()->subWeeks(2)->endOfWeek()->timestamp * 1000],
            '3_weeks_ago' => [$now->copy()->subWeeks(3)->startOfWeek()->timestamp * 1000, $now->copy()->subWeeks(3)->endOfWeek()->timestamp * 1000],
            'this_month' => [$now->copy()->startOfMonth()->timestamp * 1000, $now->copy()->endOfMonth()->timestamp * 1000],
            'last_month' => [$now->copy()->subMonth()->startOfMonth()->timestamp * 1000, $now->copy()->subMonth()->endOfMonth()->timestamp * 1000],
            default => [$now->copy()->startOfWeek()->timestamp * 1000, $now->copy()->endOfWeek()->timestamp * 1000],
        };
    }
    
    public function getLabelForPeriod($period) {
        return ucwords(str_replace('_', ' ', $period));
    }
    
    public function calculateReports() {
        $now = Carbon::now();
        
        $periods = [
            'today' => $this->getRangeForPeriod('today', $now),
            'yesterday' => $this->getRangeForPeriod('yesterday', $now),
            'this_week' => $this->getRangeForPeriod('this_week', $now),
            'last_week' => $this->getRangeForPeriod('last_week', $now),
            'this_month' => $this->getRangeForPeriod('this_month', $now),
            'last_month' => $this->getRangeForPeriod('last_month', $now),
            
            // Custom
            'custom_base' => $this->getRangeForPeriod($this->basePeriod, $now),
            'custom_compare' => $this->getRangeForPeriod($this->comparePeriod, $now),
        ];
        
        $this->timeData = [];
        $userId = $this->selectedUserId;
        
        foreach ($periods as $key => $range) {
            $query = TimerLog::whereHas('timer', function($q) use ($userId) {
                if ($userId !== 'all') {
                    $q->where('user_id', $userId);
                }
            })->whereBetween('started_at', $range);
            
            $this->timeData[$key] = $query->sum('duration_seconds');
        }
    }
    
    public function formatTime($seconds) {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        return "{$h}h {$m}m";
    }
    
    public function getChangePercentage($current, $previous) {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        $diff = $current - $previous;
        return round(($diff / $previous) * 100);
    }
    
    public function getWidthPercentage($seconds, $maxSeconds) {
        if ($maxSeconds == 0) return 0;
        return min(100, round(($seconds / $maxSeconds) * 100));
    }
};
?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 gap-4">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <x-lucide-sliders class="w-4 h-4 text-emerald-500" /> Global Filters
        </h3>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <label class="text-xs text-gray-500">User:</label>
            <select wire:model.live="selectedUserId" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-800 dark:text-gray-200 text-sm rounded px-3 py-1.5 focus:outline-none focus:border-indigo-500">
                <option value="all">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Custom Comparison Tool -->
    <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <x-lucide-git-compare class="w-4 h-4 text-pink-500" /> Custom Comparison
            </h3>
            
            <div class="flex items-center gap-2">
                <select wire:model.live="basePeriod" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-800 dark:text-gray-200 text-xs rounded px-2 py-1 focus:outline-none focus:border-indigo-500">
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="2_weeks_ago">2 Weeks Ago</option>
                    <option value="3_weeks_ago">3 Weeks Ago</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                </select>
                <span class="text-gray-500 text-xs">vs</span>
                <select wire:model.live="comparePeriod" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-800 dark:text-gray-200 text-xs rounded px-2 py-1 focus:outline-none focus:border-indigo-500">
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="2_weeks_ago">2 Weeks Ago</option>
                    <option value="3_weeks_ago">3 Weeks Ago</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                </select>
            </div>
        </div>
        
        @php 
            $customMax = max($timeData['custom_base'], $timeData['custom_compare'], 1);
            $customP = $this->getChangePercentage($timeData['custom_base'], $timeData['custom_compare']);
        @endphp
        
        <div class="space-y-4 max-w-3xl mx-auto">
            <div class="flex flex-col gap-1">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $this->getLabelForPeriod($basePeriod) }}</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200 tabular-nums">{{ $this->formatTime($timeData['custom_base']) }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-pink-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['custom_base'], $customMax) }}%"></div>
                </div>
            </div>
            
            <div class="flex flex-col gap-1">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ $this->getLabelForPeriod($comparePeriod) }}</span>
                    <span class="text-gray-600 dark:text-gray-400 tabular-nums">{{ $this->formatTime($timeData['custom_compare']) }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gray-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['custom_compare'], $customMax) }}%"></div>
                </div>
            </div>
            
            <div class="pt-2 text-center">
                <span class="text-sm font-medium px-3 py-1.5 rounded {{ $customP >= 0 ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                    Delta: {{ $customP > 0 ? '+' : '' }}{{ $customP }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Overviews -->
    <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
            <x-lucide-clock class="w-4 h-4 text-indigo-500" /> Standard Pulse
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Daily -->
            @php 
                $todayMax = max($timeData['today'], $timeData['yesterday'], 1);
                $todayP = $this->getChangePercentage($timeData['today'], $timeData['yesterday']);
            @endphp
            <div class="space-y-4">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-200 dark:border-gray-800 pb-2">Daily</h4>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-800 dark:text-gray-800 dark:text-gray-200">Today</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 tabular-nums">{{ $this->formatTime($timeData['today']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['today'], $todayMax) }}%"></div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Yesterday</span>
                        <span class="text-gray-600 dark:text-gray-400 tabular-nums">{{ $this->formatTime($timeData['yesterday']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gray-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['yesterday'], $todayMax) }}%"></div>
                    </div>
                </div>
                
                <div class="pt-2">
                    <span class="text-xs font-medium px-2 py-1 rounded {{ $todayP >= 0 ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        {{ $todayP > 0 ? '+' : '' }}{{ $todayP }}% vs Yesterday
                    </span>
                </div>
            </div>

            <!-- Weekly -->
            @php 
                $weekMax = max($timeData['this_week'], $timeData['last_week'], 1);
                $weekP = $this->getChangePercentage($timeData['this_week'], $timeData['last_week']);
            @endphp
            <div class="space-y-4">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-200 dark:border-gray-800 pb-2">Weekly</h4>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-800 dark:text-gray-800 dark:text-gray-200">This Week</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 tabular-nums">{{ $this->formatTime($timeData['this_week']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['this_week'], $weekMax) }}%"></div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Last Week</span>
                        <span class="text-gray-600 dark:text-gray-400 tabular-nums">{{ $this->formatTime($timeData['last_week']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gray-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['last_week'], $weekMax) }}%"></div>
                    </div>
                </div>
                
                <div class="pt-2">
                    <span class="text-xs font-medium px-2 py-1 rounded {{ $weekP >= 0 ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        {{ $weekP > 0 ? '+' : '' }}{{ $weekP }}% vs Last Week
                    </span>
                </div>
            </div>

            <!-- Monthly -->
            @php 
                $monthMax = max($timeData['this_month'], $timeData['last_month'], 1);
                $monthP = $this->getChangePercentage($timeData['this_month'], $timeData['last_month']);
            @endphp
            <div class="space-y-4">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-200 dark:border-gray-800 pb-2">Monthly</h4>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-800 dark:text-gray-800 dark:text-gray-200">This Month</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 tabular-nums">{{ $this->formatTime($timeData['this_month']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['this_month'], $monthMax) }}%"></div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Last Month</span>
                        <span class="text-gray-600 dark:text-gray-400 tabular-nums">{{ $this->formatTime($timeData['last_month']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gray-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $this->getWidthPercentage($timeData['last_month'], $monthMax) }}%"></div>
                    </div>
                </div>
                
                <div class="pt-2">
                    <span class="text-xs font-medium px-2 py-1 rounded {{ $monthP >= 0 ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        {{ $monthP > 0 ? '+' : '' }}{{ $monthP }}% vs Last Month
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>