<x-layouts.app title="Leads">
    <div class="flex flex-col gap-5">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Leads</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage and track your potential clients and incoming opportunities.</p>
            </div>
        </div>

        <livewire:lead-manager />
    </div>
</x-layouts.app>
