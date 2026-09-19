@props(['model'])

@php
    $timers = $model->getAllTimers();
    $totalSeconds = 0;
    foreach($timers as $t) {
        $elapsed = $t->is_running && $t->last_started_at ? floor((floor(microtime(true) * 1000) - $t->last_started_at) / 1000) : 0;
        $totalSeconds += $t->accumulated_seconds + $elapsed;
    }
    
@endphp

<div x-data="{ openTimers: false }" class="inline-block">
    <button @click="openTimers = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium transition-colors border border-gray-200 dark:border-gray-700 shadow-sm">
        <x-lucide-clock class="w-4 h-4" />
        <span>{{ \App\Support\TimeHelper::formatDuration($totalSeconds) }} Logged</span>
    </button>

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
                 class="absolute inset-0 bg-gray-900/75 dark:bg-gray-900/90 backdrop-blur-sm transition-opacity" 
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
                    <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-gray-900 shadow-xl border-l border-gray-200 dark:border-gray-800">
                        <div class="px-4 py-6 sm:px-6 bg-gray-50 dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100" id="slide-over-title">Time Logs</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: {{ \App\Support\TimeHelper::formatDuration($totalSeconds) }}</p>
                            </div>
                            <button type="button" @click="openTimers = false" class="rounded-md bg-gray-50 dark:bg-gray-950 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Close panel</span>
                                <x-lucide-x class="h-6 w-6" />
                            </button>
                        </div>
                        <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-4">
                            @forelse($timers as $timer)
                                @php
                                    $tElapsed = $timer->is_running && $timer->last_started_at ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) : 0;
                                    $tSec = $timer->accumulated_seconds + $tElapsed;
                                @endphp
                                <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3 flex justify-between items-center">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('timers.show', $timer) }}" class="font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold hover:underline">#{{ $timer->id }}</a>
                                            @if($timer->is_running)
                                                <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Running</span>
                                            @elseif($timer->completed_at)
                                                <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Completed</span>
                                            @else
                                                <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Paused</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $timer->timerable ? class_basename($timer->timerable_type) . ' ' . $timer->timerable->key : 'Unassigned' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                            Updated {{ $timer->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="font-mono text-lg text-gray-800 dark:text-gray-200 font-bold">
                                        {{ \App\Support\TimeHelper::formatDuration($tSec) }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-gray-500 dark:text-gray-400 text-sm">
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
