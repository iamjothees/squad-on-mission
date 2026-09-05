<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="h-full font-sans antialiased text-gray-900 bg-gray-100 flex overflow-hidden">
        
        <!-- Sidebar (Laptop First) -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-gray-900">
                <div class="flex flex-col flex-1 h-0 overflow-y-auto">
                    <div class="flex items-center justify-center h-16 px-4 bg-gray-900 shrink-0">
                        <span class="text-xl font-bold text-white">{{ config('app.name') }}</span>
                    </div>
                    <nav class="flex-1 px-2 py-4 space-y-1 bg-gray-900">
                        <a href="/" wire:navigate class="flex items-center px-2 py-2 text-sm font-medium text-white rounded-md bg-gray-800 group">
                            Dashboard
                        </a>
                        <a href="/about" wire:navigate class="flex items-center px-2 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-700 hover:text-white group">
                            About
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <!-- Top Navbar for mobile (and user menu on desktop) -->
            <div class="relative z-10 flex h-16 shrink-0 bg-white shadow-sm">
                <!-- Mobile menu button -->
                <button type="button" class="px-4 text-gray-500 border-r border-gray-200 md:hidden hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg>
                </button>
                
                <div class="flex justify-between flex-1 px-4">
                    <div class="flex flex-1">
                        <!-- Search or other header tools -->
                    </div>
                    <div class="flex items-center ml-4 md:ml-6">
                        <!-- Profile dropdown -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" type="button" class="flex items-center max-w-xs text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=User&background=random" alt="">
                                </button>
                            </div>
                            <!-- Dropdown menu -->
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="relative flex-1 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="px-4 mx-auto max-w-7xl sm:px-6 md:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                    <div class="px-4 mx-auto max-w-7xl sm:px-6 md:px-8 mt-4">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
