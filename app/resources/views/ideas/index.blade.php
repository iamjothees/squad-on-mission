<x-layouts.app title="Ideas">
    <div class="flex flex-col gap-5">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Ideas</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dump and organize your crazy ideas, brainstorms, and someday-projects.</p>
            </div>
        </div>

        <livewire:idea-board />
    </div>
</x-layouts.app>
