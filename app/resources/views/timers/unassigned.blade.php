<x-layouts.app title="Unassigned Timers">
    <div class="flex flex-col gap-5 w-full">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Unassigned Timers</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Review your global focus sessions and assign them to specific projects or tasks.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-md text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-2.5 rounded-md text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950">
            <div class="overflow-x-auto">
                <form action="{{ route('timers.bulk-assign') }}" method="POST" id="bulk-assign-form">
                @csrf
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-16">ID</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32">Status</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Duration</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold whitespace-nowrap w-32">Last Updated</th>
                            <th class="px-4 py-3 font-semibold text-right w-full">Assign To</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($timers as $timer)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-mono text-gray-500 dark:text-gray-400">
                                    <a href="{{ route('timers.show', $timer) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">#{{ $timer->id }}</a>
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50">
                                    @if($timer->is_running)
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Running</span>
                                    @elseif($timer->completed_at)
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Completed</span>
                                    @else
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Paused</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-mono text-gray-800 dark:text-gray-200">
                                    @php
                                        $elapsed = $timer->is_running && $timer->last_started_at 
                                            ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) 
                                            : 0;
                                        $totalSeconds = $timer->accumulated_seconds + $elapsed;
                                    @endphp
                                    {{ gmdate("H:i:s", $totalSeconds) }}
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $timer->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-2 m-0 p-0 w-full">
                                        <div class="flex-1 text-left"><x-form.select :no-create="true" name="assignments[{{ $timer->id }}]" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-2 py-1 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500" >
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
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No unassigned timers found!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                                </table>
                <!-- Floating Action Button -->
                @if($timers->isNotEmpty())
                <button type="submit" class="fixed bottom-24 right-8 bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-3 rounded-full shadow-lg shadow-indigo-900/20 font-semibold text-sm transition-transform hover:scale-105 flex items-center gap-2 z-[90]">
                    <x-lucide-save class="w-5 h-5" />
                    <span>Save Assignments</span>
                </button>
                @endif
            </form>
            </div>
        </div>
    </div>
</x-layouts.app>
