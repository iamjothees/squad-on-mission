<?php

use Livewire\Component;
use App\Models\TimerLog;
use Carbon\Carbon;

new class extends Component
{
    public $timeData = [];
    
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
    <div class="bg-[#1C1C1E] border border-gray-800 rounded p-6">
        <h3 class="text-sm font-semibold text-gray-300 mb-6 flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 text-indigo-400"></i> Time Invested Overview
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Daily -->
            @php 
                $todayMax = max($timeData['today'], $timeData['yesterday'], 1);
                $todayP = $this->getChangePercentage($timeData['today'], $timeData['yesterday']);
            @endphp
            <div class="space-y-4">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-2">Daily</h4>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-300">Today</span>
                        <span class="font-bold text-gray-200 tabular-nums">{{ $this->formatTime($timeData['today']) }}</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($timeData['today'], $todayMax) }}%"></div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Yesterday</span>
                        <span class="text-gray-400 tabular-nums">{{ $this->formatTime($timeData['yesterday']) }}</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gray-600 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($timeData['yesterday'], $todayMax) }}%"></div>
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
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-2">Weekly</h4>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-300">This Week</span>
                        <span class="font-bold text-gray-200 tabular-nums">{{ $this->formatTime($timeData['this_week']) }}</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($timeData['this_week'], $weekMax) }}%"></div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Last Week</span>
                        <span class="text-gray-400 tabular-nums">{{ $this->formatTime($timeData['last_week']) }}</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gray-600 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($timeData['last_week'], $weekMax) }}%"></div>
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
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-800 pb-2">Monthly</h4>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-300">This Month</span>
                        <span class="font-bold text-gray-200 tabular-nums">{{ $this->formatTime($timeData['this_month']) }}</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($timeData['this_month'], $monthMax) }}%"></div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Last Month</span>
                        <span class="text-gray-400 tabular-nums">{{ $this->formatTime($timeData['last_month']) }}</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gray-600 h-1.5 rounded-full" style="width: {{ $this->getWidthPercentage($timeData['last_month'], $monthMax) }}%"></div>
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
    
    <!-- Deep Dive Table -->
    <div class="bg-[#1C1C1E] border border-gray-800 rounded overflow-x-auto">
        <div class="p-4 border-b border-gray-800">
            <h3 class="text-sm font-semibold text-gray-300">Raw Data</h3>
        </div>
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-[#2C2C2E] text-gray-400 border-b border-gray-800 text-xs uppercase tracking-wider">
                    <th class="px-4 py-3 font-medium">Period</th>
                    <th class="px-4 py-3 font-medium text-right">Current Duration</th>
                    <th class="px-4 py-3 font-medium text-right">Previous Duration</th>
                    <th class="px-4 py-3 font-medium text-right">Delta (%)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm">
                <tr class="hover:bg-[#252527] transition-colors">
                    <td class="px-4 py-3 text-gray-200 font-medium">Day (Today vs Yesterday)</td>
                    <td class="px-4 py-3 text-gray-300 text-right tabular-nums">{{ $this->formatTime($timeData['today']) }}</td>
                    <td class="px-4 py-3 text-gray-500 text-right tabular-nums">{{ $this->formatTime($timeData['yesterday']) }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="{{ $todayP >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $todayP > 0 ? '+' : '' }}{{ $todayP }}%</span>
                    </td>
                </tr>
                <tr class="hover:bg-[#252527] transition-colors">
                    <td class="px-4 py-3 text-gray-200 font-medium">Week (This vs Last)</td>
                    <td class="px-4 py-3 text-gray-300 text-right tabular-nums">{{ $this->formatTime($timeData['this_week']) }}</td>
                    <td class="px-4 py-3 text-gray-500 text-right tabular-nums">{{ $this->formatTime($timeData['last_week']) }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="{{ $weekP >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $weekP > 0 ? '+' : '' }}{{ $weekP }}%</span>
                    </td>
                </tr>
                <tr class="hover:bg-[#252527] transition-colors">
                    <td class="px-4 py-3 text-gray-200 font-medium">Month (This vs Last)</td>
                    <td class="px-4 py-3 text-gray-300 text-right tabular-nums">{{ $this->formatTime($timeData['this_month']) }}</td>
                    <td class="px-4 py-3 text-gray-500 text-right tabular-nums">{{ $this->formatTime($timeData['last_month']) }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="{{ $monthP >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $monthP > 0 ? '+' : '' }}{{ $monthP }}%</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>