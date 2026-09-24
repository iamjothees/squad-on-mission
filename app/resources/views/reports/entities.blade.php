<x-layouts.app title="Focus Analytics">
    <div class="flex flex-col gap-5">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Focus Analytics</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">See which clients, projects, and tasks consume the most time.</p>
            </div>
        </div>

        <livewire:reports.entity-report />
    </div>
</x-layouts.app>
