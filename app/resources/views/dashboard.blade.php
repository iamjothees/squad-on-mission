<x-layouts.app title="Dashboard">
 <div class="flex flex-col gap-6 w-full max-w-7xl mx-auto">
 <!-- Page Header -->
 <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
 <div>
 <h1 class="font-bold text-fg text-xl">Mission Control</h1>
 <p class="text-sm text-fg-muted mt-1">Here is a summary of your freelance career progress.</p>
 </div>

 </div>

 <!-- Highlighted Stats Grid -->
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <livewire:dashboard.project-stats lazy />
 <livewire:dashboard.task-stats lazy />
 <livewire:dashboard.time-stats lazy />
 </div>
 
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
 <div class="bg-bg/50 border border-border border-dashed rounded-lg h-64 flex items-center justify-center text-sm font-medium text-gray-400 dark:text-fg-muted">
 Recent Projects Activity will appear here
 </div>
 <div class="bg-bg/50 border border-border border-dashed rounded-lg h-64 flex items-center justify-center text-sm font-medium text-gray-400 dark:text-fg-muted">
 Recent Tasks Activity will appear here
 </div>
 </div>
 </div>
</x-layouts.app>
