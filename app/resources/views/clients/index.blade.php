<x-layouts.app title="Clients">
    <div class="flex flex-col gap-5 w-full">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-gray-800 dark:text-white text-lg">Client Database</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage your clients and organizations.</p>
            </div>
            <a href="{{ route('clients.create') }}" wire:navigate class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 dark:hover:bg-white transition-colors shadow-sm">
                + New Client
            </a>
        </div>

        <form action="{{ route('clients.index') }}" method="GET" id="filter-form">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div class="relative flex-1 max-w-md">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clients..." onblur="this.form.submit()" class="w-full pl-9 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-full text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm transition-all">
                </div>
            </div>
        </form>

        @if (session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-2.5 rounded-md text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-md text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Company</th>
                            <th class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-semibold">Contact</th>
                            <th class="px-4 py-3 font-semibold w-24 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($clients as $client)
                            <tr x-data @click="if(window.Livewire) { Livewire.navigate('{{ route('clients.show', $client) }}') } else { window.location.href = '{{ route('clients.show', $client) }}' }" class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors cursor-pointer">
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-800 dark:text-gray-200 font-medium">{{ $client->name }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">{{ $client->company ?? '-' }}</td>
                                <td class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-800/50 text-gray-600 dark:text-gray-400">
                                    <div class="text-xs">{{ $client->email }}</div>
                                    <div class="text-xs">{{ $client->phone }}</div>
                                </td>
                                <td class="px-4 py-2.5 text-right" @click.stop>
                                    <div class="flex items-center justify-end gap-1">
                                        @if($client->id !== 1)
                                        <a href="{{ route('clients.edit', $client) }}" wire:navigate class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors" title="Delete">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic mr-2">System</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No clients found. Add your first client!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
