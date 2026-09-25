@props(['model'])

@php
 $timers = $model->getAllTimers();
 $totalSeconds = 0;
 $ownSeconds = 0;
 $sharedSeconds = 0;
 foreach($timers as $t) {
 $elapsed = $t->is_running && $t->last_started_at ? floor((floor(microtime(true) * 1000) - $t->last_started_at) / 1000) : 0;
 $tSec = $t->accumulated_seconds + $elapsed;
 $totalSeconds += $tSec;
 
 $isShared = $t->timerables->count() > 1;
 if ($isShared) {
 $sharedSeconds += $tSec;
 } else {
 $ownSeconds += $tSec;
 }
 }
@endphp

<div x-data="{ openTimers: false }" class="inline-block">
 <div class="relative group inline-block">
 <button @click="openTimers = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface dark:hover:opacity-90 text-fg-muted rounded-md text-sm font-medium transition-colors border border-border shadow-sm">
 <x-lucide-clock class="w-4 h-4" />
 <span>{{ \App\Support\TimeHelper::formatDuration($totalSeconds) }} Logged</span>
 </button>
 <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-50 whitespace-nowrap bg-gray-900 dark:bg-surface text-accent-fg text-xs px-2 py-1.5 rounded shadow-lg">
 <div class="font-medium">Own Time: {{ \App\Support\TimeHelper::formatDuration($ownSeconds) }}</div>
 <div class="font-medium mt-0.5">Shared Time: {{ \App\Support\TimeHelper::formatDuration($sharedSeconds) }}</div>
 <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-surface rotate-45"></div>
 </div>
 </div>

 <!-- Slide-over -->
 <div x-show="openTimers" 
 class="fixed inset-0 overflow-hidden z-[100]" 
 aria-labelledby="slide-over-title" 
 role="dialog" 
 aria-modal="true"
 style="display: none;">
 <div class="absolute inset-0 overflow-hidden">
 <!-- Background backdrop -->
 <div x-show="openTimers" 
 x-transition:enter="ease-in-out duration-500" 
 x-transition:enter-start="opacity-0" 
 x-transition:enter-end="opacity-100" 
 x-transition:leave="ease-in-out duration-500" 
 x-transition:leave-start="opacity-100" 
 x-transition:leave-end="opacity-0" 
 class="absolute inset-0 bg-gray-900/75 /90 backdrop-blur-sm transition-opacity" 
 @click="openTimers = false"
 aria-hidden="true"></div>

 <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
 <!-- Slide-over panel -->
 <div x-show="openTimers" 
 x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
 x-transition:enter-start="translate-x-full" 
 x-transition:enter-end="translate-x-0" 
 x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
 x-transition:leave-start="translate-x-0" 
 x-transition:leave-end="translate-x-full" 
 class="pointer-events-auto w-screen max-w-md"
 @click.away="openTimers = false">
 <div class="flex h-full flex-col overflow-y-scroll bg-surface shadow-xl border-l border-border">
 <div class="px-4 py-6 sm:px-6 bg-bg border-b border-border flex items-center justify-between">
 <div>
 <h2 class="text-base font-semibold leading-6 text-fg" id="slide-over-title">Time Logs</h2>
 <p class="mt-1 text-sm text-fg-muted">Total: {{ \App\Support\TimeHelper::formatDuration($totalSeconds) }}</p>
 </div>
 <div class="flex items-center gap-2">
 <x-modals.manual-timer 
 :timerableType="get_class($model)" 
 :timerableId="$model->id"
 class="px-2.5 py-1.5 bg-accent/10 dark:bg-accent/20 text-indigo-700 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-md text-xs font-medium border border-indigo-200 dark:border-indigo-800/50"
 >
 Add Manual Timer
 </x-modals.manual-timer>
 <button type="button" @click="openTimers = false" class="rounded-md bg-bg text-gray-400 hover:text-fg-muted focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
 <span class="sr-only">Close panel</span>
 <x-lucide-x class="h-6 w-6" />
 </button>
 </div>
 </div>
 <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-4">
 @forelse($timers as $timer)
 @php
 $tElapsed = $timer->is_running && $timer->last_started_at ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) : 0;
 $tSec = $timer->accumulated_seconds + $tElapsed;
 @endphp
 <div class="bg-surface /50 border border-border rounded-lg p-3 flex justify-between items-center">
 <div>
 <div class="flex items-center gap-2">
 <a href="{{ route('timers.show', $timer) }}" class="font-mono text-sm text-accent font-bold hover:underline">#{{ $timer->id }}</a>
 @if($timer->is_running)
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Running</span>
 @elseif($timer->completed_at)
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-surface text-fg-muted">Completed</span>
 @else
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Paused</span>
 @endif
 @if($timer->timerables->count() > 1)
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">Shared</span>
 @endif
 </div>
 <div class="text-xs text-fg-muted mt-1">
 {{ $timer->timerables->count() > 0 ? $timer->timerables->map(fn($t) => class_basename($t) . ' ' . $t->key)->join(', ') : 'Unassigned' }}
 </div>
 <div class="text-[10px] text-gray-400 dark:text-fg-muted mt-0.5">
 Updated {{ $timer->updated_at->diffForHumans() }}
 </div>
 </div>
 <div class="font-mono text-lg text-fg font-bold">
 {{ \App\Support\TimeHelper::formatDuration($tSec) }}
 </div>
 </div>
 @empty
 <div class="text-center py-10 text-fg-muted text-sm">
 No timers logged yet.
 </div>
 @endforelse
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
