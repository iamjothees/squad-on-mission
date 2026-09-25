<x-layouts.app title="New Client">
 <div class="max-w-2xl mx-auto w-full">
 <div class="flex items-center gap-3 mb-6">
 <a href="{{ route('clients.index') }}" wire:navigate class="p-2 bg-surface border border-border rounded-md text-fg-muted hover:bg-surface dark:hover:bg-gray-800 transition-colors">
 <x-lucide-arrow-left class="w-4 h-4" />
 </a>
 <h1 class="font-bold text-fg text-xl">New Client</h1>
 </div>

 <form action="{{ route('clients.store') }}" method="POST" class="bg-surface border border-border rounded-lg shadow-sm p-6 space-y-6">
 @csrf

 <div>
 <label class="block text-sm font-medium text-fg-muted mb-1">Name <span class="text-red-500">*</span></label>
 <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-2 focus:ring-accent">
 @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-medium text-fg-muted mb-1">Company</label>
 <input type="text" name="company" value="{{ old('company') }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-2 focus:ring-accent">
 @error('company')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>
 <div>
 <label class="block text-sm font-medium text-fg-muted mb-1">Email</label>
 <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-2 focus:ring-accent">
 @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>
 </div>

 <div>
 <label class="block text-sm font-medium text-fg-muted mb-1">Phone</label>
 <input type="text" name="phone" value="{{ old('phone') }}" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-2 focus:ring-accent">
 @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div>
 <label class="block text-sm font-medium text-fg-muted mb-1">Notes</label>
 <textarea name="notes" rows="4" class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-2 focus:ring-accent">{{ old('notes') }}</textarea>
 @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div class="flex justify-end pt-4 border-t border-border">
 <button type="submit" class="bg-accent text-accent-fg hover:opacity-90 text-white px-5 py-2 rounded-md text-sm font-medium transition-colors">
 Save Client
 </button>
 </div>
 </form>
 </div>
</x-layouts.app>
