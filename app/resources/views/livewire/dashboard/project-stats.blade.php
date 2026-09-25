<div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-fg text-sm">Projects Overview</h3>
 <x-lucide-folder class="w-4 h-4 text-accent" />
 </div>
 
 <div class="grid grid-cols-2 gap-4">
 <div>
 <div class="text-2xl font-bold text-fg">{{ $active }}</div>
 <div class="text-xs text-fg-muted font-medium">Active</div>
 </div>
 <div>
 <div class="text-2xl font-bold text-fg">{{ $total }}</div>
 <div class="text-xs text-fg-muted font-medium">Total</div>
 </div>
 <div>
 <div class="text-2xl font-bold text-fg">{{ $completed }}</div>
 <div class="text-xs text-fg-muted font-medium">Completed</div>
 </div>
 <div>
 <div class="text-2xl font-bold text-fg">{{ $onHold }}</div>
 <div class="text-xs text-fg-muted font-medium">On Hold</div>
 </div>
 </div>
</div>
