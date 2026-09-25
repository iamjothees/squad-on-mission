<x-layouts.app title="Ideas">
    <div class="flex flex-col gap-5">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Ideas</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dump and organize your crazy ideas, brainstorms, and someday-projects.</p>
            </div>
            <a href="{{ route('ideas.create') }}" wire:navigate class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 dark:hover:bg-white transition-colors shadow-sm">
                + New Idea
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-md text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($ideas as $idea)
                <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 flex flex-col hover:border-gray-300 dark:hover:border-gray-700 transition-colors group">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 text-sm leading-tight group-hover:text-indigo-400 transition-colors">{{ $idea->title }}</h4>
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium uppercase tracking-wider whitespace-nowrap ml-2
                            @if($idea->status === 'new') bg-blue-500/10 text-blue-400
                            @elseif($idea->status === 'evaluating') bg-purple-500/10 text-purple-400
                            @elseif($idea->status === 'planned') bg-yellow-500/10 text-yellow-400
                            @elseif($idea->status === 'building') bg-orange-500/10 text-orange-400
                            @elseif($idea->status === 'shipped') bg-green-500/10 text-green-400
                            @else bg-gray-500/10 text-gray-600 dark:text-gray-400
                            @endif
                        ">
                            {{ $idea->status }}
                        </span>
                    </div>
                    
                    @if($idea->description)
                        <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-grow">{{ $idea->description }}</p>
                    @else
                        <div class="flex-grow"></div>
                    @endif
                    
                    <div class="flex justify-end items-center mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex gap-2">
                            <a href="{{ route('ideas.edit', $idea) }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded p-1.5 transition-colors">
                                <x-lucide-pencil class="w-4 h-4" />
                            </a>
                            <form action="{{ route('ideas.destroy', $idea) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Delete this idea?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded p-1.5 transition-colors">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center border border-dashed border-gray-200 dark:border-gray-800 rounded-lg text-gray-500 text-sm">
                    No crazy ideas yet. Time to brainstorm!
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
