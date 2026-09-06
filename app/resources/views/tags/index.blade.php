<x-layouts.app title="Tag Management">
    <div class="flex flex-col gap-4">
        <div class="flex justify-between items-center bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Tag Management</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Organize and customize tags used across the system.</p>
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
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-16">ID</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32">Badge Preview</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold w-32 text-center">Usage Count</th>
                            <th class="px-4 py-3 font-semibold w-32 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($tags as $tag)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-mono text-gray-500 dark:text-gray-400">{{ $tag->id }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $tag->name }}
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-sm shadow-sm font-semibold {{ $tag->color ? $tag->color->bgClass() : 'bg-gray-100 dark:bg-gray-800' }} {{ $tag->color ? $tag->color->textClass() : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $tag->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-center font-mono">
                                    {{ $tag->projects_count + $tag->tasks_count }}
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('tags.edit', $tag) }}" wire:navigate class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </a>
                                        
                                        @if(($tag->projects_count + $tag->tasks_count) == 0)
                                            <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Delete this unused tag?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors" title="Delete">
                                                    <x-lucide-trash-2 class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="p-1.5 text-gray-400 dark:text-gray-600 cursor-not-allowed" title="Cannot delete: Tag is in use">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No tags created yet. Add tags by typing them into a Project or Task form!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
