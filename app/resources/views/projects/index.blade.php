<x-layouts.app title="Projects">
    <div class="flex flex-col gap-4">
        <div class="flex justify-between items-center bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Projects</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage your freelance career progress and active missions.</p>
            </div>
            <a href="{{ route('projects.create') }}" wire:navigate class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 dark:hover:bg-white transition-colors">
                + New Project
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-md text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-16">ID</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Project Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32">Client</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32">Status</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32">Budget</th>
                            <th class="px-4 py-3 font-semibold w-32 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($projects as $project)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-mono text-gray-500 dark:text-gray-400">{{ $project->id }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50">
                                    <div class="text-gray-800 dark:text-gray-200 font-medium">{{ $project->name }}</div>
                                    @if($project->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($project->tags as $tag)
                                                <span class="inline-block px-1.5 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-[10px] rounded-sm">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">{{ $project->client_name ?? '-' }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50">
                                    <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold {{ $project->status->colorClass() }}">
                                        {{ $project->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">{{ $project->budget ? '₹' . number_format($project->budget, 2) : '-' }}</td>
                                <td class="px-4 py-2.5 text-right space-x-2">
                                    <a href="{{ route('projects.edit', $project) }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                    
                                    @if($project->status !== \App\Enums\ProjectStatus::ARCHIVED)
                                    <form action="{{ route('projects.archive', $project) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-yellow-600 dark:text-yellow-400 hover:underline">Archive</button>
                                    </form>
                                    @endif

                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No projects found. Create your first mission!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
