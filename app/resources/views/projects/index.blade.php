<x-layouts.app title="Projects">
    <div class="flex flex-col gap-5">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Projects</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage your freelance career progress and active missions.</p>
            </div>
            <a href="{{ route('projects.create') }}" wire:navigate class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 dark:hover:bg-white transition-colors shadow-sm">
                + New Project
            </a>
        </div>

        <!-- Top Action Bar -->
        <form action="{{ route('projects.index') }}" method="GET" id="filter-form">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." onblur="this.form.submit()" class="w-full pl-9 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-full text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm transition-all">
                </div>
            </div>
            </div>

            <!-- Filters Bar -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Status Filter -->
                @php
                    $statusOptions = [];
                    foreach(\App\Enums\ProjectStatus::cases() as $status) {
                        $statusOptions[$status->value] = $status->label();
                    }
                @endphp
                <x-filter-dropdown 
                    name="status" 
                    label="Status" 
                    :options="$statusOptions" 
                    :selected="request('status', [])" 
                />

                <!-- Tags Filter -->
                @php
                    $tagOptions = [];
                    foreach($tags as $t) {
                        $tagOptions[$t->id] = $t->name;
                    }
                @endphp
                <x-filter-dropdown 
                    name="tags" 
                    label="Tags" 
                    :options="$tagOptions" 
                    :selected="request('tags', [])" 
                />

                @if(request()->hasAny(['search', 'status', 'tags']) && (request('search') || request('status') || request('tags')))
                    <a href="{{ route('projects.index') }}" wire:navigate class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium px-2 py-1.5 transition-colors">
                        <x-lucide-x class="w-3.5 h-3.5" /> Clear all
                    </a>
                @endif
            </div>
        </form>
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
                                                <span class="inline-block px-1.5 py-0.5 text-[10px] rounded-sm font-semibold shadow-sm {{ $tag->color ? $tag->color->bgClass() : 'bg-gray-100 dark:bg-gray-800' }} {{ $tag->color ? $tag->color->textClass() : 'text-gray-500 dark:text-gray-400' }}">{{ $tag->name }}</span>
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
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick="Livewire.dispatch('start-timer', { type: 'App\\Models\\Project', id: {{ $project->id }} })" class="p-1.5 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 rounded transition-colors" title="Start Timer">
                                            <x-lucide-play class="w-4 h-4 fill-current" />
                                        </button>
                                        <a href="{{ route('projects.edit', $project) }}" wire:navigate class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </a>
                                        
                                        @if($project->status !== \App\Enums\ProjectStatus::ARCHIVED)
                                        <form action="{{ route('projects.archive', $project) }}" method="POST" class="inline m-0 p-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded transition-colors" title="Archive">
                                                <x-lucide-archive class="w-4 h-4" />
                                            </button>
                                        </form>
                                        @endif

                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors" title="Delete">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
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
