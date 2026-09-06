<div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 dark:text-white text-sm">Projects Overview</h3>
        <x-lucide-folder class="w-4 h-4 text-indigo-500" />
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $active }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Active</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $total }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completed }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Completed</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $onHold }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">On Hold</div>
        </div>
    </div>
</div>
