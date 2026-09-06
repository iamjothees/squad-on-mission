<x-layouts.app title="Dashboard">
    <div class="flex flex-col gap-6 w-full max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-xl">Mission Control</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Here is a summary of your freelance career progress.</p>
            </div>
        </div>

        <!-- Highlighted Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <livewire:dashboard.project-stats lazy />
            <livewire:dashboard.task-stats lazy />
            <livewire:dashboard.time-stats lazy />
        </div>
        
        <!-- Maybe some placeholders for future content below -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 border-dashed rounded-lg h-64 flex items-center justify-center text-sm font-medium text-gray-400 dark:text-gray-600">
                Recent Projects Activity will appear here
            </div>
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 border-dashed rounded-lg h-64 flex items-center justify-center text-sm font-medium text-gray-400 dark:text-gray-600">
                Recent Tasks Activity will appear here
            </div>
        </div>
    </div>
</x-layouts.app>
