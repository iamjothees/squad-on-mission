<x-layouts.app title="Timer Details">
    <div class="flex flex-col gap-4 max-w-4xl mx-auto w-full">
        <div class="flex justify-between items-center bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">
                    Timer Log for 
                    @if($timer->timerable)
                        <span class="text-indigo-600 dark:text-indigo-400">{{ $timer->timerable->title ?? $timer->timerable->name ?? 'Entity' }}</span>
                    @else
                        Global Timer
                    @endif
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Accumulated Time: {{ gmdate("H:i:s", $timer->accumulated_seconds) }}</p>
            </div>
            <div>
                @if($timer->is_running)
                    <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Currently Running</span>
                @else
                    <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Stopped</span>
                @endif
            </div>
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
                            <th class="px-4 py-3 font-semibold w-32 text-right">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($timer->logs as $log)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-mono text-gray-500 dark:text-gray-400">{{ $log->id }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::createFromTimestampMs($log->started_at)->format('M d, Y h:i:s A') }}
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">
                                    @if($log->stopped_at)
                                        {{ \Carbon\Carbon::createFromTimestampMs($log->stopped_at)->format('M d, Y h:i:s A') }}
                                    @else
                                        <span class="text-green-500 italic">Running...</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono text-gray-800 dark:text-gray-200">
                                    @if($log->stopped_at)
                                        {{ gmdate("H:i:s", $log->duration_seconds) }}
                                    @else
                                        -
                                    @endif
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
