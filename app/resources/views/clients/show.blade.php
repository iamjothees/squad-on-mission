
<x-layouts.app title="Client: {{ $client->key }}">
    <div class="max-w-3xl mx-auto w-full space-y-6">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
            <a href="{{ route('clients.index') }}" wire:navigate class="p-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <h1 class="font-bold text-gray-800 dark:text-white text-xl">{{ $client->name }}</h1>
            <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-mono bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">{{ $client->key }}</span>
            </div>
            <x-timers-slideover :model="$client" />
        </div>
        
        <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">Client Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500 block mb-1">Company:</span> <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $client->company ?? 'N/A' }}</span></div>
                <div><span class="text-gray-500 block mb-1">Email:</span> <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $client->email ?? 'N/A' }}</span></div>
                <div><span class="text-gray-500 block mb-1">Phone:</span> <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $client->phone ?? 'N/A' }}</span></div>
            </div>
            
            <h4 class="mt-6 mb-2 font-medium text-gray-700 dark:text-gray-300">Alias Keys History:</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($client->entityKeys as $alias)
                    <span class="inline-flex items-center py-0.5 px-2 rounded text-xs font-mono border {{ $alias->is_primary ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-900/30 dark:border-indigo-800 dark:text-indigo-400' : 'bg-gray-50 border-gray-200 text-gray-500 dark:bg-gray-800/50 dark:border-gray-700 dark:text-gray-400' }}">
                        {{ $alias->key }} {{ $alias->is_primary ? '(Primary)' : '' }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
