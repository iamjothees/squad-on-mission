<x-layouts.app title="Edit Task">
    <div class="flex flex-col gap-4 max-w-3xl mx-auto w-full">
        <div class="flex items-center gap-3 bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <a href="{{ route('tasks.index') }}" wire:navigate class="p-2 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors bg-gray-50 dark:bg-gray-900 rounded-md">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Edit Task</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $task->title }}</p>
            </div>
        </div>

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950 p-5">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-4 text-sm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5 md:col-span-2">
                        <label for="title" class="block font-semibold text-gray-700 dark:text-gray-300">Task Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="project_id" class="block font-semibold text-gray-700 dark:text-gray-300">Link to Project</label>
                        <select id="project_id" name="project_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                            <option value="">-- No Project (Standalone Task) --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="status" class="block font-semibold text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" name="status" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                            @foreach(\App\Enums\TaskStatus::cases() as $statusEnum)
                                <option value="{{ $statusEnum->value }}" {{ old('status', $task->status?->value) === $statusEnum->value ? 'selected' : '' }}>
                                    {{ $statusEnum->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="priority" class="block font-semibold text-gray-700 dark:text-gray-300">Priority</label>
                        <select id="priority" name="priority" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                            @foreach(\App\Enums\TaskPriority::cases() as $priorityEnum)
                                <option value="{{ $priorityEnum->value }}" {{ old('priority', $task->priority?->value) === $priorityEnum->value ? 'selected' : '' }}>
                                    {{ $priorityEnum->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="due_date" class="block font-semibold text-gray-700 dark:text-gray-300">Due Date</label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="w-full md:w-1/2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('due_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="tags" class="block font-semibold text-gray-700 dark:text-gray-300">Tags</label>
                        <x-form.select id="tags" name="tags[]" multiple="true" class="w-full">
    @php $selectedTags = old('tags', $task->tags->pluck('name')->toArray()); @endphp
    @foreach(\App\Models\Tag::orderBy('name')->get() as $tag)
        <option value="{{ $tag->name }}" {{ (is_array($selectedTags) && in_array($tag->name, $selectedTags)) ? 'selected' : '' }}>{{ $tag->name }}</option>
    @endforeach
</x-form.select>
                        @error('tags') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="description" class="block font-semibold text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">{{ old('description', $task->description) }}</textarea>
                        @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="submit" class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-6 py-2.5 rounded-md font-bold hover:bg-gray-700 dark:hover:bg-white transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
