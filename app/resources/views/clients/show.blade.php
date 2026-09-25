
<x-layouts.app title="Client: {{ $client->key }}">
 <div class="max-w-3xl mx-auto w-full space-y-6">
 <div class="flex items-center justify-between w-full">
 <div class="flex items-center gap-3">
 <a href="{{ route('clients.index') }}" wire:navigate class="p-2 bg-surface border border-border rounded-md text-fg-muted hover:bg-surface dark:hover:bg-gray-800 transition-colors">
 <x-lucide-arrow-left class="w-4 h-4" />
 </a>
 <h1 class="font-bold text-fg text-xl">{{ $client->name }}</h1>
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-mono bg-surface text-fg-muted border border-border">{{ $client->key }}</span>
 </div>
 <x-timers-slideover :model="$client" />
 </div>
 
 <div class="bg-surface border border-border rounded-lg shadow-sm p-6">
 <h3 class="text-lg font-semibold text-fg border-b border-border pb-3 mb-4">Client Details</h3>
 <div class="grid grid-cols-2 gap-4 text-sm">
 <div><span class="text-fg-muted block mb-1">Company:</span> <span class="text-fg font-medium">{{ $client->company ?? 'N/A' }}</span></div>
 <div><span class="text-fg-muted block mb-1">Email:</span> <span class="text-fg font-medium">{{ $client->email ?? 'N/A' }}</span></div>
 <div><span class="text-fg-muted block mb-1">Phone:</span> <span class="text-fg font-medium">{{ $client->phone ?? 'N/A' }}</span></div>
 </div>
 
 <h4 class="mt-6 mb-2 font-medium text-fg-muted">Alias Keys History:</h4>
 <div class="flex flex-wrap gap-2">
 @foreach($client->entityKeys as $alias)
 <span class="inline-flex items-center py-0.5 px-2 rounded text-xs font-mono border {{ $alias->is_primary ? 'bg-accent/10 border-indigo-200 text-indigo-700 dark:bg-accent/20 dark:border-indigo-800 ' : 'bg-surface border-border text-fg-muted /50 ' }}">
 {{ $alias->key }} {{ $alias->is_primary ? '(Primary)' : '' }}
 </span>
 @endforeach
 </div>
 </div>
 </div>
</x-layouts.app>
