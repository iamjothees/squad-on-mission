<x-layouts.app title="Tasks">
 <div class="flex flex-col gap-5">
 <!-- Page Header -->
 <div class="flex justify-between items-center">
 <div>
 <h1 class="font-bold text-fg text-lg">Productivity Tracker</h1>
 <p class="text-xs text-fg-muted mt-1">Stay on top of your missions and daily tasks.</p>
 </div>
 <a href="{{ route('tasks.create') }}" wire:navigate class="bg-accent text-accent-fg text-accent-fg px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90 hover:opacity-90 transition-colors shadow-sm">
 + New Task
 </a>
 </div>

 <!-- Top Action Bar -->
 <form action="{{ route('tasks.index') }}" method="GET" id="filter-form">
 <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
 <!-- Search -->
 <div class="relative flex-1 max-w-md">
 <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
 <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..." onblur="this.form.submit()" class="w-full pl-9 pr-4 py-2 bg-surface border border-border rounded-full text-sm text-fg focus:ring-2 focus:ring-accent outline-none shadow-sm transition-all">
 </div>
 </div>
 </div>

 <!-- Filters Bar -->
 <div class="flex flex-wrap items-center gap-3">
 <!-- Project Filter -->
 @php
 $projectOptions = [];
 foreach($projects as $p) {
 $projectOptions[$p->id] = $p->name;
 }
 @endphp
 <x-filter-dropdown 
 name="project_id" 
 label="Project" 
 :options="$projectOptions" 
 :selected="request('project_id', [])" 
 />

 <!-- Status Filter -->
 @php
 $statusOptions = [];
 foreach(\App\Enums\TaskStatus::cases() as $status) {
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

 @if(request()->hasAny(['search', 'status', 'project_id', 'tags']) && (request('search') || request('status') || request('project_id') || request('tags')))
 <a href="{{ route('tasks.index') }}" wire:navigate class="flex items-center gap-1.5 text-sm text-fg-muted hover:text-fg-muted font-medium px-2 py-1.5 transition-colors">
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

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface border-b border-border text-xs uppercase tracking-wider text-fg-muted">
 <th class="px-4 py-3 border-r border-border font-semibold w-32">Key</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Task</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-48">Project</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-32">Priority</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-40">Status</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-32">Due Date</th>
 <th class="px-4 py-3 font-semibold w-32 text-right">Actions</th>
 </tr>
 </thead>
 <tbody class="text-sm">
 @forelse($tasks as $task)
 <tr x-data @click="if(window.Livewire) { Livewire.navigate('{{ route('tasks.show', $task) }}') } else { window.location.href = '{{ route('tasks.show', $task) }}' }" class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors cursor-pointer {{ $task->status === \App\Enums\TaskStatus::DONE ? 'opacity-60' : '' }}">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-xs text-fg-muted">{{ $task->key }}</td>
 <td class="px-4 py-2.5 border-r border-border/50">
 <div class="font-medium text-fg {{ $task->status === \App\Enums\TaskStatus::DONE ? 'line-through text-fg-muted dark:text-fg-muted' : '' }}">
 {{ $task->title }}
 </div>
 @if($task->tags->isNotEmpty())
 <div class="flex flex-wrap gap-1 mt-1">
 @foreach($task->tags as $tag)
 <span class="inline-block px-1.5 py-0.5 text-[10px] rounded-sm font-semibold shadow-sm {{ $tag->color ? $tag->color->bgClass() : 'bg-surface ' }} {{ $tag->color ? $tag->color->textClass() : 'text-fg-muted' }}">{{ $tag->name }}</span>
 @endforeach
 </div>
 @endif
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted">
 <div class="relative group cursor-help w-40" x-data>
 <div class="truncate">
 {{ $task->project ? $task->project->name : 'No Project' }}
 </div>
 <div class="absolute left-0 bottom-full mb-1 hidden group-hover:block bg-accent text-accent-fg text-accent-fg text-xs rounded px-2 py-1 whitespace-nowrap z-50 shadow-lg pointer-events-none">
 {{ $task->project ? $task->project->name : 'No Project' }}
 </div>
 </div>
 </td>
 <td class="px-4 py-2.5 border-r border-border/50">
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold {{ $task->priority->colorClass() }}">
 {{ $task->priority->label() }}
 </span>
 </td>
 <td class="px-4 py-2.5 border-r border-border/50">
 <div class="flex items-center justify-between group">
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-semibold {{ $task->status->colorClass() }}">
 {{ $task->status->label() }}
 </span>
 @if($task->status !== \App\Enums\TaskStatus::DONE)
 <form action="{{ route('tasks.next-status', $task) }}" method="POST" class="inline m-0 p-0" @click.stop>
 @csrf
 @method('PATCH')
 <button type="submit" class="p-1 text-gray-400 hover:text-accent rounded transition-colors" title="Move to Next Status">
 <x-lucide-arrow-right class="w-3.5 h-3.5" />
 </button>
 </form>
 @endif
 </div>
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted">
 {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
 </td>
 <td class="px-4 py-2.5 text-right" @click.stop>
 <div class="flex items-center justify-end gap-1">
 
 
 <livewire:timer-play-button :type="\App\Models\Task::class" :id="$task->id" :wire:key="'timer-play-task-'.$task->id" />
 
 <a href="{{ route('tasks.edit', $task) }}" wire:navigate class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
 <x-lucide-pencil class="w-4 h-4" />
 </a>
 
 <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Delete this task?');">
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
 <td colspan="7" class="px-4 py-8 text-center text-fg-muted text-sm">
 No tasks right now. Take a break!
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
</x-layouts.app>
