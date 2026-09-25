<x-layouts.app title="Edit Lead">
 <div class="flex flex-col gap-4 max-w-3xl mx-auto w-full">
 <div class="flex items-center gap-3 bg-surface p-4 border border-border rounded-lg shadow-sm">
 <a href="{{ route('leads.index') }}" wire:navigate class="p-2 text-fg-muted hover:text-fg transition-colors bg-bg rounded-md">
 <x-lucide-arrow-left class="w-4 h-4" />
 </a>
 <div>
 <h1 class="font-bold text-fg text-lg">Edit Lead</h1>
 <p class="text-xs text-fg-muted mt-0.5">Update lead details and status.</p>
 </div>
 </div>

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface p-5">
 <form action="{{ route('leads.update', $lead) }}" method="POST" class="space-y-4 text-sm">
 @csrf
 @method('PUT')
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div class="space-y-1.5 md:col-span-2">
 <label for="name" class="block font-semibold text-fg-muted">Name <span class="text-red-500">*</span></label>
 <input type="text" id="name" name="name" value="{{ old('name', $lead->name) }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="company" class="block font-semibold text-fg-muted">Company</label>
 <input type="text" id="company" name="company" value="{{ old('company', $lead->company) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('company') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="email" class="block font-semibold text-fg-muted">Email</label>
 <input type="email" id="email" name="email" value="{{ old('email', $lead->email) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="phone" class="block font-semibold text-fg-muted">Phone</label>
 <input type="text" id="phone" name="phone" value="{{ old('phone', $lead->phone) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="value" class="block font-semibold text-fg-muted">Estimated Value (₹)</label>
 <input type="number" step="0.01" id="value" name="value" value="{{ old('value', $lead->value) }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 @error('value') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5">
 <label for="status" class="block font-semibold text-fg-muted">Status</label>
 <x-form.select :no-create="true" id="status" name="status" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">
 <option value="new" {{ old('status', $lead->status) === 'new' ? 'selected' : '' }}>New</option>
 <option value="contacted" {{ old('status', $lead->status) === 'contacted' ? 'selected' : '' }}>Contacted</option>
 <option value="negotiating" {{ old('status', $lead->status) === 'negotiating' ? 'selected' : '' }}>Negotiating</option>
 <option value="won" {{ old('status', $lead->status) === 'won' ? 'selected' : '' }}>Won</option>
 <option value="lost" {{ old('status', $lead->status) === 'lost' ? 'selected' : '' }}>Lost</option>
 </x-form.select>
 @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>

 <div class="space-y-1.5 md:col-span-2">
 <label for="notes" class="block font-semibold text-fg-muted">Notes / Call Log</label>
 <textarea id="notes" name="notes" rows="4" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 transition-shadow">{{ old('notes', $lead->notes) }}</textarea>
 @error('notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="flex justify-end pt-4 mt-4 border-t border-border">
 <button type="submit" class="bg-accent text-accent-fg text-accent-fg px-6 py-2.5 rounded-md font-bold hover:opacity-90 hover:opacity-90 transition-colors">
 Update Lead
 </button>
 </div>
 </form>
 </div>
 </div>
</x-layouts.app>
