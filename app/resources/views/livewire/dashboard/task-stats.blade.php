<div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-fg text-sm">Tasks Overview</h3>
 <x-lucide-check-square class="w-4 h-4 text-green-500" />
 </div>
 
 <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
 <div>
 <div class="text-2xl font-bold text-fg">{{ $open }}</div>
 <div class="text-xs text-fg-muted font-medium">Open</div>
 </div>
 <div>
 <div class="text-2xl font-bold text-fg">{{ $total }}</div>
 <div class="text-xs text-fg-muted font-medium">Total</div>
 </div>
 <div>
 <div class="text-2xl font-bold {{ $dueSoon > 0 ? 'text-yellow-600 dark:text-yellow-500' : 'text-fg' }}">{{ $dueSoon }}</div>
 <div class="text-xs text-fg-muted font-medium">Due Soon (3 Days)</div>
 </div>
 <div>
 <div class="text-2xl font-bold {{ $overdue > 0 ? 'text-red-600 dark:text-red-500' : 'text-fg' }}">{{ $overdue }}</div>
 <div class="text-xs text-fg-muted font-medium">Overdue</div>

 <div>
 <div class="text-2xl font-bold text-fg">{{ $noDueDate }}</div>
 <div class="text-xs text-fg-muted font-medium">No Due Date</div>
 </div>
 </div>
</div>
</div>
