<x-layouts.app title="Timer Details">
    <div class="flex flex-col gap-4 w-full">
        <div class="bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="font-bold text-gray-800 dark:text-white text-lg">
                        Timer Log for 
                        @if($timer->timerable)
                            <span class="text-indigo-600 dark:text-indigo-400">{{ $timer->timerable->title ?? $timer->timerable->name ?? 'Entity' }}</span>
                        @else
                            Global Timer
                        @endif
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Accumulated Time: @php
                            $elapsed = $timer->is_running && $timer->last_started_at 
                                ? floor((floor(microtime(true) * 1000) - $timer->last_started_at) / 1000) 
                                : 0;
                            $totalSeconds = $timer->accumulated_seconds + $elapsed;
                        @endphp
                        {{ \App\Support\TimeHelper::formatDuration($totalSeconds) }}</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <form action="{{ route('timers.assign', $timer) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <x-form.select :no-create="true" name="timerable" wrapperClass="w-[400px]" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-2 py-1 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500" >
                            <option value="">Select Task or Project...</option>
                            <optgroup label="Tasks">
                                @foreach($tasks as $task)
                                    <option value="task:{{ $task->id }}" @selected($timer->timerable_type === \App\Models\Task::class && $timer->timerable_id == $task->id)>{{ $task->title }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Projects">
                                @foreach($projects as $project)
                                    <option value="project:{{ $project->id }}" @selected($timer->timerable_type === \App\Models\Project::class && $timer->timerable_id == $project->id)>{{ $project->name }}</option>
                                @endforeach
                            </optgroup>
                        </x-form.select>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Assign
                        </button>
                    </form>
                    
                    @if($timer->is_running)
                        <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Currently Running</span>
                    @else
                        <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Stopped</span>
                    @endif
                </div>
            </div>
            
            @if(session('success'))
                <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-md text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-2.5 rounded-md text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950 p-5">
            <h3 class="font-bold text-gray-800 dark:text-white mb-4">Chronological Logs</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-16">ID</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Started At</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Stopped At</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32 text-right">Duration</th>
                            <th class="px-4 py-3 font-semibold w-16 text-center">Act</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($timer->logs as $log)
                            <tr x-data="{ editing: false }" class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-mono text-gray-500 dark:text-gray-400">{{ $log->id }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">
                                    <div x-show="!editing">{{ \Carbon\Carbon::createFromTimestampMs($log->started_at)->format('M d, Y h:i:s A') }}</div>
                                    <div x-show="editing" x-cloak>
                                        <input type="datetime-local" step="1" form="edit-log-{{ $log->id }}" name="started_at" value="{{ \Carbon\Carbon::createFromTimestampMs($log->started_at)->format('Y-m-d\TH:i:s') }}" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">
                                    <div x-show="!editing">
                                        @if($log->stopped_at)
                                            {{ \Carbon\Carbon::createFromTimestampMs($log->stopped_at)->format('M d, Y h:i:s A') }}
                                        @else
                                            <span class="text-green-500 italic">Running...</span>
                                        @endif
                                    </div>
                                    <div x-show="editing" x-cloak>
                                        <input type="datetime-local" step="1" form="edit-log-{{ $log->id }}" name="stopped_at" value="{{ $log->stopped_at ? \Carbon\Carbon::createFromTimestampMs($log->stopped_at)->format('Y-m-d\TH:i:s') : '' }}" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-right font-mono text-gray-800 dark:text-gray-200">
                                    @if($log->stopped_at)
                                        {{ \App\Support\TimeHelper::formatDuration($log->duration_seconds) }}
                                    @else
                                        @php
                                            $logElapsed = floor((floor(microtime(true) * 1000) - $log->started_at) / 1000);
                                        @endphp
                                        {{ \App\Support\TimeHelper::formatDuration($logElapsed) }}
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <button type="button" x-show="!editing" @click="editing = true" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit Log">
                                        <x-lucide-pencil class="w-4 h-4 mx-auto" />
                                    </button>
                                    <div x-show="editing" x-cloak class="flex items-center justify-center gap-2">
                                        <form id="edit-log-{{ $log->id }}" action="{{ route('timers.logs.update', [$timer, $log]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400 transition-colors" title="Save">
                                                <x-lucide-check class="w-4 h-4" />
                                            </button>
                                        </form>
                                        <button type="button" @click="editing = false" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Cancel">
                                            <x-lucide-x class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No sessions recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
