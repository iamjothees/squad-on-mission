<div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm flex flex-col justify-between">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 dark:text-white text-sm">Time Tracked</h3>
        <x-lucide-clock class="w-4 h-4 text-blue-500" />
    </div>
    
    <div class="space-y-4">
        <div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white font-mono">{{ \App\Support\TimeHelper::formatDuration($todaySeconds) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Today</div>
        </div>
        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
            <div class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ \App\Support\TimeHelper::formatDuration($weekSeconds) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">This Week</div>
        </div>
    </div>
</div>
