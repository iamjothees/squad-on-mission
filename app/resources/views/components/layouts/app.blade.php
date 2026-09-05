<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full font-sans antialiased text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900 flex overflow-hidden leading-snug">
        
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-shrink-0 border-r border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
            <div class="flex flex-col w-52">
                <div class="flex flex-col flex-1 h-0 overflow-y-auto">
                    <div class="flex items-center h-14 px-4 border-b border-gray-300 dark:border-gray-800 shrink-0 bg-white dark:bg-gray-950">
                        <span class="font-bold text-gray-800 dark:text-white tracking-tight truncate">{{ config('app.name') }}</span>
                    </div>
                    <nav class="flex-1 p-2 space-y-0.5 bg-gray-50 dark:bg-gray-900">
                        <a href="/" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            Dashboard
                        </a>
                        <div class="pt-2 pb-1 px-3 text-xs uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wider">Modules</div>
                        <a href="#" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            Users
                        </a>
                        <a href="#" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            Settings
                        </a>
                        <a href="#" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            Reports
                        </a>
                        <div class="pt-2 pb-1 px-3 text-xs uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wider">System</div>
                        <a href="/about" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            About
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <!-- Top Navbar -->
            <div class="relative z-10 flex h-14 shrink-0 bg-white dark:bg-gray-950 border-b border-gray-300 dark:border-gray-800">
                <!-- Mobile menu button -->
                <button type="button" class="px-4 text-gray-500 border-r border-gray-300 dark:border-gray-800 md:hidden hover:text-gray-900 dark:hover:text-white focus:outline-none transition-colors">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg>
                </button>
                
                <div class="flex justify-between flex-1 px-5 items-center">
                    <div class="flex flex-1 items-center space-x-2">
                        <span class="font-semibold text-gray-800 dark:text-white text-sm tracking-wide">{{ $title ?? 'Dashboard' }}</span>
                    </div>
                    <div class="flex items-center space-x-4 text-sm">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">user@example.com</span>
                        <div class="h-5 w-px bg-gray-300 dark:bg-gray-700"></div>
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:underline focus:outline-none transition-colors">Logout</a>
                    </div>
                </div>
            </div>

            <main class="relative flex-1 overflow-y-auto bg-gray-50/50 dark:bg-gray-900 focus:outline-none">
                <div class="p-4 md:p-6 w-full">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
