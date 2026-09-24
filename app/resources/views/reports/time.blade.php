<x-layouts.app title="Time Analytics">
    <div class="flex flex-col gap-5">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Time Analytics</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deep dive into exactly where your hours are being invested.</p>
            </div>
        </div>

        <livewire:reports.time-report />
    </div>
</x-layouts.app>
