<div class="space-y-6">
    <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Analysis Period</h3>
            <p class="text-xs text-gray-500 mt-0.5">Select the timeframe to aggregate entity focus.</p>
        </div>
        <select wire:model.live="period" class="w-full md:w-64 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-200 text-sm rounded px-3 py-2 focus:outline-none focus:border-indigo-500 transition-colors">
            <option value="today">Today</option>
            <option value="this_week">This Week</option>
            <option value="last_week">Last Week</option>
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Projects -->
        <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 flex flex-col">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2 mb-4">
                <x-lucide-folder class="w-4 h-4 text-emerald-500" /> Top Projects
            </h3>
            <div class="space-y-4 flex-grow">
                @forelse($topProjects as $item)
                    @php 
                        $max = $topProjects[0]['seconds'] ?: 1; 
                        $width = max(2, ($item['seconds'] / $max) * 100);
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate pr-2">{{ $item['name'] }}</span>
                            <span class="text-gray-500 whitespace-nowrap">{{ number_format($item['seconds'] / 3600, 1) }}h</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden flex">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $width }}%"></div>
                        </div>
                        <div class="text-[10px] mt-1 text-right {{ $item['delta_percent'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $item['delta_percent'] >= 0 ? '+' : '' }}{{ $item['delta_percent'] }}% vs prev
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-gray-500">No projects recorded in this period.</div>
                @endforelse
            </div>
        </div>

        <!-- Top Tasks -->
        <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 flex flex-col">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2 mb-4">
                <x-lucide-check-square class="w-4 h-4 text-indigo-500" /> Top Tasks
            </h3>
            <div class="space-y-4 flex-grow">
                @forelse($topTasks as $item)
                    @php 
                        $max = $topTasks[0]['seconds'] ?: 1; 
                        $width = max(2, ($item['seconds'] / $max) * 100);
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate pr-2">{{ $item['name'] }}</span>
                            <span class="text-gray-500 whitespace-nowrap">{{ number_format($item['seconds'] / 3600, 1) }}h</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden flex">
                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: {{ $width }}%"></div>
                        </div>
                        <div class="text-[10px] mt-1 text-right {{ $item['delta_percent'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $item['delta_percent'] >= 0 ? '+' : '' }}{{ $item['delta_percent'] }}% vs prev
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-gray-500">No tasks recorded in this period.</div>
                @endforelse
            </div>
        </div>

        <!-- Top Clients -->
        <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 flex flex-col">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2 mb-4">
                <x-lucide-users class="w-4 h-4 text-purple-500" /> Top Clients
            </h3>
            <div class="space-y-4 flex-grow">
                @forelse($topClients as $item)
                    @php 
                        $max = $topClients[0]['seconds'] ?: 1; 
                        $width = max(2, ($item['seconds'] / $max) * 100);
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate pr-2">{{ $item['name'] }}</span>
                            <span class="text-gray-500 whitespace-nowrap">{{ number_format($item['seconds'] / 3600, 1) }}h</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden flex">
                            <div class="bg-purple-500 h-full rounded-full transition-all duration-1000" style="width: {{ $width }}%"></div>
                        </div>
                        <div class="text-[10px] mt-1 text-right {{ $item['delta_percent'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $item['delta_percent'] >= 0 ? '+' : '' }}{{ $item['delta_percent'] }}% vs prev
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-gray-500">No clients recorded in this period.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
