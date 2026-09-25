<x-layouts.app title="Unassigned Timers">
 <div class="flex flex-col gap-5 w-full">
 <!-- Page Header -->
 <div class="flex justify-between items-center">
 <div>
 <h1 class="font-bold text-fg text-lg">Unassigned Timers</h1>
 <p class="text-xs text-fg-muted mt-1">Review your global focus sessions and assign them to specific projects or tasks.</p>
 </div>
 <x-modals.manual-timer class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent bg-accent text-accent-fg text-white hover:opacity-90 h-9 px-4 py-2 gap-2 shadow-sm">
 Create Manual Timer
 </x-modals.manual-timer>
 </div>

 
 

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface">
 <div class="overflow-x-auto">
 <form action="{{ route('timers.bulk-assign') }}" method="POST" id="bulk-assign-form">
 @csrf
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface border-b border-border text-xs uppercase tracking-wider text-fg-muted">
 <th class="px-4 py-3 border-r border-border font-semibold w-16">ID</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-32">Status</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Duration</th>
 <th class="px-4 py-3 border-r border-border font-semibold whitespace-nowrap w-32">Last Updated</th>
 <th class="px-4 py-3 font-semibold text-right w-full">Assign To</th>
 </tr>
 </thead>
 <tbody class="text-sm">
 @forelse($timers as $timer)
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">
 <a href="{{ route('timers.show', $timer) }}" wire:navigate class="text-accent hover:underline">#{{ $timer->id }}</a>
 </td>
 <td class="px-4 py-2.5 border-r border-border/50">
 @if($timer->is_running)
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Running</span>
 @elseif($timer->completed_at)
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-surface text-fg-muted">Completed</span>
 @else
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Paused</span>
 @endif
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg">
 @php
 $elapsed = $timer->is_running && $timer->last_started_at 
 ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) 
 : 0;
 $totalSeconds = $timer->accumulated_seconds + $elapsed;
 @endphp
 {{ \App\Support\TimeHelper::formatDuration($totalSeconds) }}
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted whitespace-nowrap">
 {{ $timer->updated_at->diffForHumans() }}
 </td>
 <td class="px-4 py-2.5 text-right">
 <div class="flex items-center justify-end gap-2 m-0 p-0 w-full">
 <div class="flex-1 text-left"><x-form.select :no-create="true" name="assignments[{{ $timer->id }}]" class="w-full bg-bg border border-border rounded-md px-2 py-1 text-xs text-fg focus:outline-none focus:ring-1 focus:ring-accent" >
 <option value="">Select Task or Project...</option>
 <optgroup label="Tasks">
 @foreach($tasks as $task)
 <option value="task:{{ $task->id }}">{{ $task->title }}</option>
 @endforeach
 </optgroup>
 <optgroup label="Projects">
 @foreach($projects as $project)
 <option value="project:{{ $project->id }}">{{ $project->name }}</option>
 @endforeach
 </optgroup>
 </x-form.select></div>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-4 py-8 text-center text-fg-muted text-sm">
 No unassigned timers found!
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 <!-- Floating Action Button -->
 @if($timers->isNotEmpty())
 <button type="submit" class="fixed bottom-24 right-8 bg-accent text-accent-fg hover:bg-accent/100 text-white px-5 py-3 rounded-full shadow-lg shadow-indigo-900/20 font-semibold text-sm transition-transform hover:scale-105 flex items-center gap-2 z-[90]">
 <x-lucide-save class="w-5 h-5" />
 <span>Save Assignments</span>
 </button>
 @endif
 </form>
 </div>
 </div>
 </div>
</x-layouts.app>
