<div class="space-y-6">
 <!-- Filters -->
 <div class="bg-surface border border-border rounded-lg shadow-sm p-4">
 <h3 class="text-sm font-semibold text-fg flex items-center gap-2 mb-4">
 <x-lucide-sliders class="w-4 h-4 text-emerald-500" /> Global Filters
 </h3>
 <div class="flex gap-4">
 <div class="w-64">
 <label class="block text-xs text-fg-muted mb-1">User</label>
 <select wire:model.live="userId" class="w-full bg-bg border border-border text-fg text-sm rounded px-2 py-1.5 focus:outline-none focus:border-accent transition-colors">
 <option value="all">All Users</option>
 @foreach(\App\Models\User::all() as $user)
 <option value="{{ $user->id }}">{{ $user->name }}</option>
 @endforeach
 </select>
 </div>
 </div>
 </div>

 <!-- Custom Comparison Tool -->
 <div class="bg-surface border border-border rounded-lg shadow-sm p-4">
 <h3 class="text-sm font-semibold text-fg flex items-center gap-2 mb-4">
 <x-lucide-git-compare class="w-4 h-4 text-pink-500" /> Custom Comparison
 </h3>
 <div class="flex flex-col md:flex-row items-end gap-4">
 <div class="w-full md:w-64">
 <label class="block text-xs text-fg-muted mb-1">Base Period</label>
 <select wire:model.live="basePeriod" class="w-full bg-bg border border-border text-fg text-sm rounded px-2 py-1.5 focus:outline-none focus:border-accent transition-colors">
 <option value="today">Today</option>
 <option value="yesterday">Yesterday</option>
 <option value="this_week">This Week</option>
 <option value="last_week">Last Week</option>
 <option value="2_weeks_ago">2 Weeks Ago</option>
 <option value="3_weeks_ago">3 Weeks Ago</option>
 <option value="this_month">This Month</option>
 <option value="last_month">Last Month</option>
 </select>
 </div>
 <div class="hidden md:flex items-center justify-center pb-2 px-2 text-gray-400">
 <span class="text-xs font-bold uppercase tracking-wider">VS</span>
 </div>
 <div class="w-full md:w-64">
 <label class="block text-xs text-fg-muted mb-1">Comparison Period</label>
 <select wire:model.live="comparePeriod" class="w-full bg-bg border border-border text-fg text-sm rounded px-2 py-1.5 focus:outline-none focus:border-accent transition-colors">
 <option value="today">Today</option>
 <option value="yesterday">Yesterday</option>
 <option value="this_week">This Week</option>
 <option value="last_week">Last Week</option>
 <option value="2_weeks_ago">2 Weeks Ago</option>
 <option value="3_weeks_ago">3 Weeks Ago</option>
 <option value="this_month">This Month</option>
 <option value="last_month">Last Month</option>
 </select>
 </div>
 </div>

 <div class="mt-6 p-4 rounded bg-surface border border-border flex items-center justify-between">
 <div>
 <p class="text-xs text-fg-muted uppercase tracking-wider font-semibold mb-1">Delta Analysis</p>
 <div class="flex items-baseline gap-2">
 <span class="text-2xl font-bold text-fg">{{ number_format($customBaseSeconds / 3600, 1) }}h</span>
 <span class="text-sm text-gray-400">vs {{ number_format($customCompareSeconds / 3600, 1) }}h</span>
 </div>
 </div>
 
 @php
 $delta = $customBaseSeconds - $customCompareSeconds;
 $percent = $customCompareSeconds > 0 ? ($delta / $customCompareSeconds) * 100 : ($customBaseSeconds > 0 ? 100 : 0);
 $isPositive = $delta >= 0;
 @endphp
 <div class="text-right">
 <span class="inline-flex items-center gap-1 font-bold text-lg {{ $isPositive ? 'text-green-500' : 'text-red-500' }}">
 @if($isPositive) <x-lucide-trending-up class="w-5 h-5" /> @else <x-lucide-trending-down class="w-5 h-5" /> @endif
 {{ $isPositive ? '+' : '' }}{{ round($percent) }}%
 </span>
 <p class="text-xs text-fg-muted mt-0.5">{{ $isPositive ? '+' : '' }}{{ number_format($delta / 3600, 1) }} hours difference</p>
 </div>
 </div>
 
 <div class="mt-4">
 <div class="w-full bg-surface rounded-full h-2 overflow-hidden flex">
 @php
 $max = max($customBaseSeconds, $customCompareSeconds, 1);
 $baseW = ($customBaseSeconds / $max) * 100;
 $compW = ($customCompareSeconds / $max) * 100;
 @endphp
 <div class="bg-accent/100 h-full transition-all duration-1000" style="width: {{ $baseW }}%"></div>
 </div>
 <div class="flex justify-between text-[10px] text-fg-muted mt-1 uppercase">
 <span>0h</span>
 <span>{{ number_format($max / 3600, 1) }}h (Peak)</span>
 </div>
 </div>
 </div>

 <!-- Standard Pulse -->
 <div class="bg-surface border border-border rounded-lg shadow-sm p-4">
 <h3 class="text-sm font-semibold text-fg flex items-center gap-2 mb-4">
 <x-lucide-clock class="w-4 h-4 text-accent" /> Standard Pulse
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <!-- Daily -->
 <div class="p-4 rounded border border-border bg-bg">
 <div class="text-xs text-fg-muted uppercase tracking-wider font-semibold mb-2">Today vs Yesterday</div>
 <div class="flex items-end justify-between mb-2">
 <span class="text-xl font-bold text-fg">{{ number_format($todaySeconds / 3600, 1) }}h</span>
 <span class="text-xs text-gray-400">prev: {{ number_format($yesterdaySeconds / 3600, 1) }}h</span>
 </div>
 <div class="w-full bg-surface rounded-full h-1.5 overflow-hidden">
 <div class="bg-blue-500 h-full transition-all" style="width: {{ max(10, min(100, $yesterdaySeconds > 0 ? ($todaySeconds/$yesterdaySeconds)*100 : 100)) }}%"></div>
 </div>
 </div>
 
 <!-- Weekly -->
 <div class="p-4 rounded border border-border bg-bg">
 <div class="text-xs text-fg-muted uppercase tracking-wider font-semibold mb-2">This Week vs Last</div>
 <div class="flex items-end justify-between mb-2">
 <span class="text-xl font-bold text-fg">{{ number_format($thisWeekSeconds / 3600, 1) }}h</span>
 <span class="text-xs text-gray-400">prev: {{ number_format($lastWeekSeconds / 3600, 1) }}h</span>
 </div>
 <div class="w-full bg-surface rounded-full h-1.5 overflow-hidden">
 <div class="bg-purple-500 h-full transition-all" style="width: {{ max(10, min(100, $lastWeekSeconds > 0 ? ($thisWeekSeconds/$lastWeekSeconds)*100 : 100)) }}%"></div>
 </div>
 </div>

 <!-- Monthly -->
 <div class="p-4 rounded border border-border bg-bg">
 <div class="text-xs text-fg-muted uppercase tracking-wider font-semibold mb-2">This Month vs Last</div>
 <div class="flex items-end justify-between mb-2">
 <span class="text-xl font-bold text-fg">{{ number_format($thisMonthSeconds / 3600, 1) }}h</span>
 <span class="text-xs text-gray-400">prev: {{ number_format($lastMonthSeconds / 3600, 1) }}h</span>
 </div>
 <div class="w-full bg-surface rounded-full h-1.5 overflow-hidden">
 <div class="bg-emerald-500 h-full transition-all" style="width: {{ max(10, min(100, $lastMonthSeconds > 0 ? ($thisMonthSeconds/$lastMonthSeconds)*100 : 100)) }}%"></div>
 </div>
 </div>
 </div>
 </div>
</div>
