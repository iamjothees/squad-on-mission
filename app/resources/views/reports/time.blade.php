<x-layouts.app title="Time Analytics">
 <div class="flex flex-col gap-5">
 <div class="flex justify-between items-center">
 <div>
 <h1 class="font-bold text-fg text-lg">Time Analytics</h1>
 <p class="text-xs text-fg-muted mt-1">Visualize your work hours and productivity trends.</p>
 </div>
 <div class="flex gap-2">
 <a href="{{ route('reports.entities') }}" wire:navigate class="bg-surface text-fg-muted border border-border px-4 py-2 rounded-md text-sm font-semibold hover:bg-surface dark:hover:bg-gray-800 transition-colors shadow-sm">
 View Focus Report
 </a>
 </div>
 </div>

 <livewire:reports.time-report />
 </div>
</x-layouts.app>
