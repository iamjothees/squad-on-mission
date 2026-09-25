<x-layouts.app title="Leads">
 <div class="flex flex-col gap-5">
 <div class="flex justify-between items-center">
 <div>
 <h1 class="font-bold text-fg text-lg">Leads</h1>
 <p class="text-xs text-fg-muted mt-1">Manage and track your potential clients and incoming opportunities.</p>
 </div>
 <a href="{{ route('leads.create') }}" wire:navigate class="bg-accent text-accent-fg text-accent-fg px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90 hover:opacity-90 transition-colors shadow-sm">
 + New Lead
 </a>
 </div>

 @if (session('success'))
 <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-md text-sm font-medium">
 {{ session('success') }}
 </div>
 @endif

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface border-b border-border text-xs uppercase tracking-wider text-fg-muted">
 <th class="px-4 py-3 border-r border-border font-semibold">Name</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Company</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Contact</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Value</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Status</th>
 <th class="px-4 py-3 font-semibold text-right">Actions</th>
 </tr>
 </thead>
 <tbody class="text-sm">
 @forelse($leads as $lead)
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50">
 <div class="text-fg font-medium">{{ $lead->name }}</div>
 @if($lead->notes)
 <div class="text-[10px] text-fg-muted mt-0.5 truncate max-w-xs">{{ $lead->notes }}</div>
 @endif
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted">{{ $lead->company ?: '-' }}</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted">
 <div class="flex flex-col gap-0.5">
 @if($lead->email)<span class="text-xs">{{ $lead->email }}</span>@endif
 @if($lead->phone)<span class="text-xs">{{ $lead->phone }}</span>@endif
 @if(!$lead->email && !$lead->phone)-@endif
 </div>
 </td>
 <td class="px-4 py-2.5 border-r border-border/50 text-green-600 dark:text-green-400">{{ $lead->value ? '₹'.number_format($lead->value, 2) : '-' }}</td>
 <td class="px-4 py-2.5 border-r border-border/50">
 <span class="inline-flex items-center py-0.5 px-2 rounded-md text-[10px] font-semibold uppercase tracking-wider
 @if($lead->status === 'new') bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400
 @elseif($lead->status === 'contacted') bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400
 @elseif($lead->status === 'negotiating') bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400
 @elseif($lead->status === 'won') bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400
 @else bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400
 @endif
">
 {{ $lead->status }}
 </span>
 </td>
 <td class="px-4 py-2.5 text-right">
 <div class="flex items-center justify-end gap-1">
 <a href="{{ route('leads.edit', $lead) }}" wire:navigate class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
 <x-lucide-pencil class="w-4 h-4" />
 </a>
 <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Delete this lead?');">
 @csrf
 @method('DELETE')
 <button type="submit" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors" title="Delete">
 <x-lucide-trash-2 class="w-4 h-4" />
 </button>
 </form>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-4 py-8 text-center text-fg-muted text-sm">
 No leads found.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
</x-layouts.app>
