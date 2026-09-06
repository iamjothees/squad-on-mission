<x-layouts.app title="Edit Tag">
    <div class="flex flex-col gap-4 max-w-lg mx-auto w-full">
        <div class="flex items-center gap-3 bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <a href="{{ route('tags.index') }}" wire:navigate class="p-2 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors bg-gray-50 dark:bg-gray-900 rounded-md">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Edit Tag</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Customize tag appearance.</p>
            </div>
        </div>

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950 p-5">
            <form action="{{ route('tags.update', $tag) }}" method="POST" class="space-y-4 text-sm">
                @csrf
                @method('PUT')
                
                <div class="space-y-1.5">
                    <label for="name" class="block font-semibold text-gray-700 dark:text-gray-300">Tag Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $tag->name) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="color" class="block font-semibold text-gray-700 dark:text-gray-300">Badge Color</label>
                        <x-form.select id="color" name="color" :no-create="true">
    <option value="">Default (Gray)</option>
    @foreach(\App\Enums\TagColor::cases() as $tagColorEnum)
        <option value="{{ $tagColorEnum->value }}" 
            data-custom-html='<span class="inline-block px-2 py-0.5 rounded-sm font-semibold {{ $tagColorEnum->bgClass() }} {{ $tagColorEnum->textClass() }}">{{ $tagColorEnum->label() }}</span>'
            {{ old('color', $tag->color?->value) === $tagColorEnum->value ? 'selected' : '' }}>
            {{ $tagColorEnum->label() }}
        </option>
    @endforeach
</x-form.select>
                        @error('color') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="submit" class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-6 py-2.5 rounded-md font-bold hover:bg-gray-700 dark:hover:bg-white transition-colors">
                        Save Tag
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
