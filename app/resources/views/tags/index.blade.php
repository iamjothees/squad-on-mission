<x-layouts.app title="Tag Management">
 <div class="flex flex-col gap-5">
 <!-- Page Header -->
 <div class="flex justify-between items-center">
 <div>
 <h1 class="font-bold text-fg text-lg">Tag Management</h1>
 <p class="text-xs text-fg-muted mt-1">Organize and customize tags used across the system.</p>
 </div>
 </div>

 <!-- Top Action Bar -->
 <form action="{{ route('tags.index') }}" method="GET" id="filter-form">
 <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
 <!-- Search -->
 <div class="relative flex-1 max-w-md">
 <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
 <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tags by name..." onblur="this.form.submit()" class="w-full pl-9 pr-4 py-2 bg-surface border border-border rounded-full text-sm text-fg focus:ring-2 focus:ring-accent outline-none shadow-sm transition-all">
 </div>
 </div>
 </div>

 <!-- Filters Bar -->
 <div class="flex flex-wrap items-center gap-3">
 @if(request()->has('search') && request('search'))
 <a href="{{ route('tags.index') }}" wire:navigate class="flex items-center gap-1.5 text-sm text-fg-muted hover:text-fg-muted font-medium px-2 py-1.5 transition-colors">
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
 @if (session('error'))
 <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-2.5 rounded-md text-sm font-medium">
 {{ session('error') }}
 </div>
 @endif

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface border-b border-border text-xs uppercase tracking-wider text-fg-muted">
 <th class="px-4 py-3 border-r border-border font-semibold w-16">ID</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Name</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-32">Badge Preview</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-32 text-center">Usage Count</th>
 <th class="px-4 py-3 font-semibold w-32 text-right">Actions</th>
 </tr>
 </thead>
 <tbody class="text-sm">
 @forelse($tags as $tag)
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">{{ $tag->id }}</td>
 <td class="px-4 py-2.5 border-r border-border/50 font-medium text-fg">
 {{ $tag->name }}
 </td>
 <td class="px-4 py-2.5 border-r border-border/50">
 <span class="inline-block px-2 py-0.5 text-xs rounded-sm shadow-sm font-semibold {{ $tag->color ? $tag->color->bgClass() : 'bg-surface ' }} {{ $tag->color ? $tag->color->textClass() : 'text-fg-muted' }}">
 {{ $tag->name }}
 </span>
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 text-center font-mono">
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
 <button type="button" disabled class="p-1.5 text-gray-400 dark:text-fg-muted cursor-not-allowed" title="Cannot delete: Tag is in use">
 <x-lucide-trash-2 class="w-4 h-4" />
 </button>
 @endif
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-4 py-8 text-center text-fg-muted text-sm">
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
