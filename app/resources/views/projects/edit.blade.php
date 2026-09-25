<x-layouts.app title="Edit Project">
 <div class="flex flex-col gap-4 max-w-3xl mx-auto w-full">
 <div class="flex items-center gap-3 bg-surface p-4 border border-border rounded-lg shadow-sm">
 <a href="{{ route('projects.index') }}" wire:navigate class="p-2 text-fg-muted hover:text-fg transition-colors bg-bg rounded-md">
 <x-lucide-arrow-left class="w-4 h-4" />
 </a>
 <div>
 <h1 class="font-bold text-fg text-lg">Edit Mission: {{ $project->name }}</h1>
 <p class="text-xs text-fg-muted mt-0.5">Update project details.</p>
 </div>
 </div>

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface p-5">
 <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4 text-sm">
 @csrf
 @method('PUT')
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div class="space-y-1.5 md:col-span-2">
 <label for="name" class="block font-semibold text-fg-muted">Project Name <span class="text-red-500">*</span></label>
 <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="client_id" class="block font-semibold text-fg-muted">Client</label>
 <x-form.select :no-create="true" id="client_id" name="client_id" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 <option value="">No Client (Internal)</option>
 @foreach($clients as $client)
 <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id) == $client->id)>{{ $client->name }} ({{ $client->key }})</option>
 @endforeach
 </x-form.select>
 @error('client_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="budget" class="block font-semibold text-fg-muted">Budget (₹)</label>
 <input type="number" step="0.01" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('budget') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="start_date" class="block font-semibold text-fg-muted">Start Date</label>
 <input type="date" id="start_date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('start_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="end_date" class="block font-semibold text-fg-muted">Target End Date</label>
 <input type="date" id="end_date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('end_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="status" class="block font-semibold text-fg-muted">Status</label>
 <x-form.select :no-create="true" id="status" name="status" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @foreach(\App\Enums\ProjectStatus::cases() as $statusEnum)
 <option value="{{ $statusEnum->value }}" {{ old('status', $project->status?->value) === $statusEnum->value ? 'selected' : '' }}>
 {{ $statusEnum->label() }}
 </option>
 @endforeach
 </x-form.select>
 @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="tags" class="block font-semibold text-fg-muted">Tags</label>
 <x-form.select id="tags" name="tags[]" multiple="true" class="w-full">
 @php $selectedTags = old('tags', $project->tags->pluck('name')->toArray()); @endphp
 @foreach(\App\Models\Tag::orderBy('name')->get() as $tag)
 <option value="{{ $tag->name }}" {{ (is_array($selectedTags) && in_array($tag->name, $selectedTags)) ? 'selected' : '' }}>{{ $tag->name }}</option>
 @endforeach
</x-form.select>
 @error('tags') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="description" class="block font-semibold text-fg-muted">Description / Scope</label>
 <textarea id="description" name="description" rows="4" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">{{ old('description', $project->description) }}</textarea>
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
