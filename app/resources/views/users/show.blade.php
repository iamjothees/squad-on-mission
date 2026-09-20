<x-layouts.app :title="$user->name">
    <div class="flex flex-col gap-6 w-full">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-200 dark:border-gray-800 pb-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-bold text-gray-800 dark:text-white text-2xl">{{ $user->name }}</h1>
                    @if($user->id === 1)
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">System</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                    <x-lucide-user class="w-4 h-4">
                    {{ $user->username }}
                    <span class="text-gray-300 dark:text-gray-700">&bull;</span>
                    <x-lucide-mail class="w-4 h-4">
                    {{ $user->email }}
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                @if($user->id !== 1)
                <a href="{{ route('users.edit', $user) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-sm">
                    <x-lucide-pencil class="w-4 h-4">
                    Edit User
                </a>
                @endif
                <a href="{{ route('users.index') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Details -->
            <div class="md:col-span-1 space-y-6">
                <!-- User Details Card -->
                <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <x-lucide-info class="w-4 h-4 text-gray-500">
                        Details
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mb-1">Created At</div>
                            <div class="text-sm text-gray-800 dark:text-gray-300">{{ $user->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mb-1">Last Updated</div>
                            <div class="text-sm text-gray-800 dark:text-gray-300">{{ $user->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Content/Stats -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-10 flex flex-col items-center justify-center text-center shadow-sm">
                    <x-lucide-activity class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">User Activity</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-sm">Activity history and related metrics for this user will appear here.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
