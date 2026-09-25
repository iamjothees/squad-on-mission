<x-layouts.app title="Focus Analytics">
    <div class="flex flex-col gap-5">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Focus Analytics</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">See where your time is being spent across projects, tasks, and clients.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reports.time') }}" wire:navigate class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors shadow-sm">
                    View Time Report
                </a>
            </div>
        </div>

        <livewire:reports.entity-report />
    </div>
</x-layouts.app>
