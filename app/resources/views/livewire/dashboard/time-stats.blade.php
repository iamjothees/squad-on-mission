<div class="bg-surface border border-border rounded-lg p-5 shadow-sm flex flex-col justify-between">
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-fg text-sm">Time Tracked</h3>
 <x-lucide-clock class="w-4 h-4 text-blue-500" />
 </div>
 
 <div class="space-y-4">
 <div>
 <div class="text-3xl font-bold text-fg font-mono">{{ \App\Support\TimeHelper::formatDuration($todaySeconds) }}</div>
 <div class="text-xs text-fg-muted font-medium">Today</div>
 </div>
 <div class="pt-4 border-t border-border">
 <div class="text-xl font-bold text-fg font-mono">{{ \App\Support\TimeHelper::formatDuration($weekSeconds) }}</div>
 <div class="text-xs text-fg-muted font-medium">This Week</div>
 </div>
 </div>
</div>
