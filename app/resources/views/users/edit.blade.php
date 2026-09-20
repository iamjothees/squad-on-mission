<x-layouts.app title="Edit User">
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-bold text-gray-800 dark:text-white text-xl">Edit User: {{ $user->name }}</h1>
            <a href="{{ route('users.index') }}" wire:navigate class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                &larr; Back to Users
            </a>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow border border-gray-200 dark:border-gray-800 sm:rounded-lg overflow-hidden">
            <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
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
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username *</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-6">
                        <hr class="border-gray-200 dark:border-gray-800 my-2">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Change Password</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Leave blank if you do not want to change the password.</p>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="password" id="password" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="pt-5 flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-gray-900">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
