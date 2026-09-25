<x-layouts.app title="Users">
 <div class="flex flex-col gap-5 w-full">
 <!-- Page Header -->
 <div class="flex justify-between items-center">
 <div>
 <h1 class="font-bold text-fg text-lg">System Users</h1>
 <p class="text-xs text-fg-muted mt-1">Manage user accounts and access.</p>
 </div>
 <a href="{{ route('users.create') }}" wire:navigate class="bg-accent text-accent-fg text-accent-fg px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90 hover:opacity-90 transition-colors shadow-sm">
 + New User
 </a>
 </div>

 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface mt-2">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface border-b border-border text-xs uppercase tracking-wider text-fg-muted">
 <th class="px-4 py-3 border-r border-border font-semibold">Name</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Username</th>
 <th class="px-4 py-3 border-r border-border font-semibold">Email</th>
 <th class="px-4 py-3 font-semibold w-24 text-right">Actions</th>
 </tr>
 </thead>
 <tbody class="text-sm">
 @forelse($users as $u)
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 text-fg font-medium">{{ $u->name }}</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted">{{ $u->username }}</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg-muted">
 <div class="flex items-center gap-2">
 {{ $u->email }}
 @if($u->email_verified_at)
 <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800/50" title="Verified on {{ $u->email_verified_at->format('M d, Y') }}">
 <x-lucide-check-circle class="w-3 h-3 mr-1" /> Verified
 </span>
 @else
 <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800/50" title="Unverified">
 <x-lucide-clock class="w-3 h-3 mr-1" /> Pending
 </span>
 @endif
 </div>
 </td>
 <td class="px-4 py-2.5 text-right">
 <div class="flex items-center justify-end gap-1">
 @if($u->id !== 1)
 <a href="{{ route('users.edit', $u) }}" wire:navigate class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
 <x-lucide-pencil class="w-4 h-4" />
 </a>
 @if($u->id !== auth()->id())
 <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this user?');">
 @csrf
 @method('DELETE')
 <button type="submit" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors" title="Delete">
 <x-lucide-trash-2 class="w-4 h-4" />
 </button>
 </form>
 @endif
 @else
 <span class="text-xs text-gray-400 dark:text-fg-muted italic mr-2">System</span>
 @endif
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="4" class="px-4 py-8 text-center text-fg-muted text-sm">
 No users found.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
</x-layouts.app>
