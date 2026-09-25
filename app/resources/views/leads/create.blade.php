<x-layouts.app title="New Lead">
    <div class="flex flex-col gap-4 max-w-3xl mx-auto w-full">
        <div class="flex items-center gap-3 bg-white dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <a href="{{ route('leads.index') }}" wire:navigate class="p-2 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors bg-gray-50 dark:bg-gray-900 rounded-md">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">New Lead</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Add a new potential client to your pipeline.</p>
            </div>
        </div>

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950 p-5">
            <form action="{{ route('leads.store') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5 md:col-span-2">
                        <label for="name" class="block font-semibold text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="company" class="block font-semibold text-gray-700 dark:text-gray-300">Company</label>
                        <input type="text" id="company" name="company" value="{{ old('company') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('company') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block font-semibold text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="phone" class="block font-semibold text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="value" class="block font-semibold text-gray-700 dark:text-gray-300">Estimated Value ($)</label>
                        <input type="number" step="0.01" id="value" name="value" value="{{ old('value') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                        @error('value') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="status" class="block font-semibold text-gray-700 dark:text-gray-300">Status</label>
                        <x-form.select :no-create="true" id="status" name="status" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
                            <option value="new" {{ old('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="negotiating" {{ old('status') === 'negotiating' ? 'selected' : '' }}>Negotiating</option>
                            <option value="won" {{ old('status') === 'won' ? 'selected' : '' }}>Won</option>
                            <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                        </x-form.select>
                        @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="notes" class="block font-semibold text-gray-700 dark:text-gray-300">Notes / Call Log</label>
                        <textarea id="notes" name="notes" rows="4" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">{{ old('notes') }}</textarea>
                        @error('notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="submit" class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-6 py-2.5 rounded-md font-bold hover:bg-gray-700 dark:hover:bg-white transition-colors">
                        Save Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
