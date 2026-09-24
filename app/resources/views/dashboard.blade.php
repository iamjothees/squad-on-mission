<x-layouts.app title="Dashboard">
    <div class="flex flex-col gap-6 w-full max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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
        
        <livewire:report-dashboard lazy />
    </div>
</x-layouts.app>
