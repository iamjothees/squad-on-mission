<x-layouts.app title="New Idea">
    <div class="flex flex-col gap-4 max-w-3xl mx-auto w-full">
        <div class="flex items-center gap-3 bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <a href="{{ route('ideas.index') }}" wire:navigate class="p-2 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors bg-gray-50 dark:bg-gray-900 rounded-md">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">New Idea</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Brainstorm and document your next big thing.</p>
            </div>
        </div>

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950 p-5">
            <form action="{{ route('ideas.store') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5 md:col-span-2">
                        <label for="title" class="block font-semibold text-gray-700 dark:text-gray-300">Idea Name <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="status" class="block font-semibold text-gray-700 dark:text-gray-300">Status</label>
                        <x-form.select :no-create="true" id="status" name="status" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                            <option value="new" {{ old('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="evaluating" {{ old('status') === 'evaluating' ? 'selected' : '' }}>Evaluating</option>
                            <option value="planned" {{ old('status') === 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="building" {{ old('status') === 'building' ? 'selected' : '' }}>Building</option>
                            <option value="shipped" {{ old('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </x-form.select>
                        @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="description" class="block font-semibold text-gray-700 dark:text-gray-300">Details</label>
                        <textarea id="description" name="description" rows="5" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">{{ old('description') }}</textarea>
                        @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="submit" class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-6 py-2.5 rounded-md font-bold hover:bg-gray-700 dark:hover:bg-white transition-colors">
                        Save Idea
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
