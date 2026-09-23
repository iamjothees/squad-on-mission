<div>
    @if($isRunning)
        <button wire:click="toggle" class="p-1.5 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 rounded transition-colors" title="Running... Click to focus">
            <x-lucide-activity class="w-4 h-4 animate-pulse" />
        </button>
    @else
        <button wire:click="toggle" class="p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition-colors" title="Start Timer">
            <x-lucide-play class="w-4 h-4 fill-current" />
        </button>
    @endif
</div>
