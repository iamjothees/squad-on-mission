<x-layouts.app title="Dashboard">
    <div class="flex flex-col gap-6 w-full max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-xl">Mission Control</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Here is a summary of your freelance career progress.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('clients.create') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-sm">
                    <x-lucide-users class="w-4 h-4" />
                    New Client
                </a>
                <a href="{{ route('projects.create') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-sm">
                    <x-lucide-folder class="w-4 h-4" />
                    New Project
                </a>
                <a href="{{ route('tasks.create') }}" wire:navigate class="inline-flex items-center gap-1.5 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-4 py-1.5 rounded-md text-sm font-semibold hover:bg-gray-700 dark:hover:bg-white transition-colors shadow-sm">
                    <x-lucide-check-square class="w-4 h-4" />
                    New Task
                </a>
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
