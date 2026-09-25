<x-layouts.app title="New User">
 <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
 
 <div class="mb-6 flex items-center justify-between">
 <h1 class="font-bold text-fg text-xl">Create New User</h1>
 <a href="{{ route('users.index') }}" wire:navigate class="text-sm text-fg-muted hover:text-fg-muted transition-colors">
 &larr; Back to Users
 </a>
 </div>

 <div class="bg-surface shadow border border-border sm:rounded-lg overflow-hidden">
 <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
 @csrf
 
 @if($errors->any())
 <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-md text-sm">
 <ul class="list-disc pl-5 space-y-1">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
 <div class="sm:col-span-3">
 <label for="name" class="block text-sm font-medium text-fg-muted mb-1">Name *</label>
 <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-1 focus:ring-accent">
 </div>

 <div class="sm:col-span-3">
 <label for="username" class="block text-sm font-medium text-fg-muted mb-1">Username *</label>
 <input type="text" name="username" id="username" value="{{ old('username') }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-1 focus:ring-accent">
 </div>

 <div class="sm:col-span-6">
 <label for="email" class="block text-sm font-medium text-fg-muted mb-1">Email Address *</label>
 <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full bg-bg border border-border rounded-md px-3 py-2 text-sm text-fg focus:outline-none focus:ring-1 focus:ring-accent">
 </div>

 

 
 </div>

 <div class="pt-5 flex justify-end">
 <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent text-accent-fg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent focus:ring-offset-gray-900">
 Create User
 </button>
 </div>
 </form>
 </div>
 </div>
</x-layouts.app>
