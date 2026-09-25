<x-layouts.app title="Edit Task">
 <div class="flex flex-col gap-4 max-w-3xl mx-auto w-full">
 <div class="flex items-center gap-3 bg-surface p-4 border border-border rounded-lg shadow-sm">
 <a href="{{ route('tasks.index') }}" wire:navigate class="p-2 text-fg-muted hover:text-fg transition-colors bg-bg rounded-md">
 <x-lucide-arrow-left class="w-4 h-4" />
 </a>
 <div>
 <h1 class="font-bold text-fg text-lg">Edit Task</h1>
 <p class="text-xs text-fg-muted mt-0.5">{{ $task->title }}</p>
 </div>
 </div>

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface p-5">
 <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-4 text-sm">
 @csrf
 @method('PUT')
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div class="space-y-1.5 md:col-span-2">
 <label for="title" class="block font-semibold text-fg-muted">Task Title <span class="text-red-500">*</span></label>
 <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="project_id" class="block font-semibold text-fg-muted">Link to Project</label>
 <x-form.select :no-create="true" id="project_id" name="project_id" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 <option value="">-- No Project (Standalone Task) --</option>
 @foreach($projects as $project)
 <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
 {{ $project->name }}
 </option>
 @endforeach
 </x-form.select>
 @error('project_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="status" class="block font-semibold text-fg-muted">Status</label>
 <x-form.select :no-create="true" id="status" name="status" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @foreach(\App\Enums\TaskStatus::cases() as $statusEnum)
 <option value="{{ $statusEnum->value }}" {{ old('status', $task->status?->value) === $statusEnum->value ? 'selected' : '' }}>
 {{ $statusEnum->label() }}
 </option>
 @endforeach
 </x-form.select>
 @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="priority" class="block font-semibold text-fg-muted">Priority</label>
 <x-form.select :no-create="true" id="priority" name="priority" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @foreach(\App\Enums\TaskPriority::cases() as $priorityEnum)
 <option value="{{ $priorityEnum->value }}" {{ old('priority', $task->priority?->value) === $priorityEnum->value ? 'selected' : '' }}>
 {{ $priorityEnum->label() }}
 </option>
 @endforeach
 </x-form.select>
 @error('priority') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="due_date" class="block font-semibold text-fg-muted">Due Date</label>
 <input type="date" id="due_date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="w-full md:w-1/2 bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('due_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="tags" class="block font-semibold text-fg-muted">Tags</label>
 <x-form.select id="tags" name="tags[]" multiple="true" class="w-full">
 @php $selectedTags = old('tags', $task->tags->pluck('name')->toArray()); @endphp
 @foreach(\App\Models\Tag::orderBy('name')->get() as $tag)
 <option value="{{ $tag->name }}" {{ (is_array($selectedTags) && in_array($tag->name, $selectedTags)) ? 'selected' : '' }}>{{ $tag->name }}</option>
 @endforeach
</x-form.select>
 @error('tags') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="description" class="block font-semibold text-fg-muted">Description</label>
 <textarea id="description" name="description" rows="3" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">{{ old('description', $task->description) }}</textarea>
 @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="flex justify-end pt-4 mt-4 border-t border-border">
 <button type="submit" class="bg-accent text-accent-fg text-accent-fg px-6 py-2.5 rounded-md font-bold hover:opacity-90 hover:opacity-90 transition-colors">
 Save Changes
 </button>
 </div>
 </form>
 </div>
 </div>
</x-layouts.app>
