<div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 dark:text-white text-sm">Tasks Overview</h3>
        <x-lucide-check-square class="w-4 h-4 text-green-500" />
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $open }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Open</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $total }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total</div>
        </div>
        <div>
            <div class="text-2xl font-bold {{ $dueSoon > 0 ? 'text-yellow-600 dark:text-yellow-500' : 'text-gray-900 dark:text-white' }}">{{ $dueSoon }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Due Soon (3 Days)</div>
        </div>
        <div>
            <div class="text-2xl font-bold {{ $overdue > 0 ? 'text-red-600 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">{{ $overdue }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Overdue</div>
        </div>
    </div>
</div>
