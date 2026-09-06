<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="icon" href="{{ asset('assets/fav.png') }}" type="image/png">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body x-data="{ sidebarOpen: false }" class="h-full font-sans antialiased text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900 flex overflow-hidden leading-snug">
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" 
             x-transition.opacity.duration.300ms
             class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm md:hidden" 
             @click="sidebarOpen = false"
             style="display: none;"
             aria-hidden="true"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 flex flex-col w-52 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:flex md:flex-shrink-0 border-r border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
            
            <!-- Close button for mobile -->
            <div class="absolute top-0 right-0 -mr-12 pt-2 md:hidden" x-show="sidebarOpen" style="display: none;">
                <button @click="sidebarOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <x-lucide-x class="w-6 h-6 text-white" />
                </button>
            </div>

            <div class="flex flex-col flex-1 h-0 overflow-y-auto w-full">
                    <div class="flex items-center gap-2 h-14 px-4 border-b border-gray-300 dark:border-gray-800 shrink-0 bg-white dark:bg-gray-950">
                        <img src="{{ asset('assets/fav.png') }}" alt="{{ config('app.name') }} Logo" class="h-8 w-auto">
                        <span class="font-bold text-gray-800 dark:text-white tracking-tight truncate">{{ config('app.name') }}</span>
                    </div>
                    <nav class="flex-1 p-2 space-y-0.5 bg-gray-50 dark:bg-gray-900">
                        <a href="/" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            <x-lucide-layout-dashboard class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" />
                            Dashboard
                        </a>
                        <div class="pt-2 pb-1 px-3 text-xs uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wider">Modules</div>
                        <a href="{{ route('projects.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            <x-lucide-folder class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" />
                            Projects
                        </a>
                        <a href="{{ route('tasks.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            <x-lucide-check-square class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" />
                            Tasks
                        </a>
                        <a href="{{ route('tags.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            <x-lucide-tag class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" />
                            Tags
                        </a>
                        <div class="pt-2 pb-1 px-3 text-xs uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wider">System</div>
                        <a href="/about" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-800 focus:outline-none transition-colors">
                            <x-lucide-info class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" />
                            About
                        </a>
                    </nav>
                </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <!-- Top Navbar -->
            <div class="relative z-10 flex h-14 shrink-0 bg-white dark:bg-gray-950 border-b border-gray-300 dark:border-gray-800">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" type="button" class="px-4 text-gray-500 border-r border-gray-300 dark:border-gray-800 md:hidden hover:text-gray-900 dark:hover:text-white focus:outline-none transition-colors">
                    <span class="sr-only">Open sidebar</span>
                    <x-lucide-menu class="w-5 h-5" />
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

        <livewire:global-timer />
        @livewireScripts
    </body>
</html>
