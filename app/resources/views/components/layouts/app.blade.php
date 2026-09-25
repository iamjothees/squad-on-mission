<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-bg">
 <head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>{{ $title ?? config('app.name') }}</title>
 <link rel="icon" href="{{ asset('assets/fav.png') }}" type="image/png">
 @vite(['resources/css/app.css', 'resources/js/app.js'])
 @livewireStyles
 <script>
 (function () {
 var preference = localStorage.getItem('admin-theme') ?? 'system';
 var dark = preference === 'dark' || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
 document.documentElement.classList.toggle('dark', dark);
 })();
 </script>
</head>
 <body x-data="{ sidebarOpen: false }" class="h-full font-sans antialiased text-fg bg-bg flex overflow-hidden leading-snug">
 
 <!-- Mobile sidebar backdrop -->
 <div x-show="sidebarOpen" 
 x-transition.opacity.duration.300ms
 class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm md:hidden" 
 @click="sidebarOpen = false"
 
 aria-hidden="true"></div>

 <!-- Sidebar -->
 <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
 class="fixed inset-y-0 left-0 z-50 flex flex-col w-52 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:flex md:flex-shrink-0 border-r border-sidebar-border bg-sidebar">
 
 <!-- Close button for mobile -->
 <div class="absolute top-0 right-0 -mr-12 pt-2 md:hidden" x-show="sidebarOpen" >
 <button @click="sidebarOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
 <span class="sr-only">Close sidebar</span>
 <x-lucide-x class="w-6 h-6 text-white" />
 </button>
 </div>

 <div class="flex flex-col flex-1 h-0 overflow-y-auto w-full">
 <div class="flex items-center gap-2 h-14 px-4 border-b border-sidebar-border shrink-0 bg-sidebar">
 <img src="{{ asset('assets/fav.png') }}" alt="{{ config('app.name') }} Logo" class="h-8 w-auto invert dark:invert-0">
 <span class="font-bold text-sidebar-fg tracking-tight truncate">{{ config('app.name') }}</span>
 </div>
 <nav class="flex-1 p-2 space-y-0.5 bg-sidebar">
 <div class="pt-2 pb-1 px-3 text-xs uppercase font-bold text-sidebar-fg-muted tracking-wider">Overview</div>
 <a href="/" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-layout-dashboard class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Dashboard
 </a>
 <a href="{{ route('reports.time') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-pie-chart class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Time Analytics
 </a>
 <a href="{{ route('reports.entities') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-bar-chart-2 class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Focus Analytics
 </a>

 <div class="pt-4 pb-1 px-3 text-xs uppercase font-bold text-sidebar-fg-muted tracking-wider">Workspace</div>
 <a href="{{ route('projects.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-folder class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Projects
 </a>
 <a href="{{ route('tasks.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-check-square class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Tasks
 </a>
 <a href="{{ route('timers.unassigned') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-clock class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Unassigned
 </a>

 <div class="pt-4 pb-1 px-3 text-xs uppercase font-bold text-sidebar-fg-muted tracking-wider">CRM & Network</div>
 <a href="{{ route('leads.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-user-plus class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Leads
 </a>
 <a href="{{ route('clients.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-users class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Clients
 </a>

 <div class="pt-4 pb-1 px-3 text-xs uppercase font-bold text-sidebar-fg-muted tracking-wider">Knowledge</div>
 <a href="{{ route('ideas.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-lightbulb class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Ideas
 </a>
 <a href="{{ route('tags.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-tag class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Tags
 </a>
 
 <div class="pt-4 pb-1 px-3 text-xs uppercase font-bold text-sidebar-fg-muted tracking-wider">System</div>
 <a href="{{ route('users.index') }}" wire:navigate class="flex items-center px-3 py-1.5 font-medium text-sidebar-fg-muted rounded-md hover:bg-sidebar dark:hover:bg-gray-800 focus:bg-sidebar dark:focus:bg-gray-800 focus:outline-none transition-colors">
 <x-lucide-user-cog class="w-4 h-4 mr-3 text-sidebar-fg-muted" />
 Users
 </a>
 </nav>
 </div>
 </aside>

 <!-- Main Content -->
 <div class="flex flex-col flex-1 w-0 overflow-hidden">
 <!-- Top Navbar -->
 <div class="relative z-10 flex h-14 shrink-0 bg-surface border-b border-sidebar-border">
 <!-- Mobile menu button -->
 <button @click="sidebarOpen = true" type="button" class="px-4 text-sidebar-fg-muted border-r border-sidebar-border md:hidden hover:text-sidebar-fg focus:outline-none transition-colors">
 <span class="sr-only">Open sidebar</span>
 <x-lucide-menu class="w-5 h-5" />
 </button>
 
 <div class="flex justify-between flex-1 px-5 items-center">
 <div class="flex flex-1 items-center space-x-2">
 <span class="font-semibold text-sidebar-fg text-sm tracking-wide">{{ $title ?? 'Dashboard' }}</span>
 </div>
 <div class="flex items-center space-x-4 text-sm">
 <a href="{{ route('profile.show') }}" class="text-sidebar-fg-muted font-medium hover:text-accent hover:underline transition-colors" title="Manage Profile">{{ auth()->user()->name ?? 'Guest' }}</a>
                            <div class="h-5 w-px bg-sidebar-border"></div>
                            <div x-data="{ theme: localStorage.getItem('admin-theme') || 'system' }" class="flex items-center">
                                <button @click="
                                    theme = { 'light': 'dark', 'dark': 'system', 'system': 'light' }[theme];
                                    theme === 'system' ? localStorage.removeItem('admin-theme') : localStorage.setItem('admin-theme', theme);
                                    document.documentElement.classList.toggle('dark', theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches));
                                " class="text-sidebar-fg-muted hover:text-sidebar-fg transition-colors w-7 h-7 rounded flex items-center justify-center" :title="'Theme: ' + theme.charAt(0).toUpperCase() + theme.slice(1)">
                                    <span x-show="theme === 'light'" ><x-lucide-sun class="w-4 h-4" /></span>
                                    <span x-show="theme === 'dark'" ><x-lucide-moon class="w-4 h-4" /></span>
                                    <span x-show="theme === 'system'" ><x-lucide-monitor class="w-4 h-4" /></span>
                                </button>
                            </div>
                            <div class="h-5 w-px bg-sidebar-border"></div>
                            
 <form action="{{ route('logout') }}" method="POST" class="inline">
 @csrf
 <button type="submit" class="text-sidebar-fg-muted hover:text-sidebar-fg hover:underline focus:outline-none transition-colors">Logout</button>
 </form>
 </div>
 </div>
 </div>

 <main class="relative flex-1 overflow-y-auto bg-bg focus:outline-none">
 <div class="p-4 md:p-6 w-full">
 @if(session('success'))
 <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded border border-green-200 dark:border-green-800/50 flex items-center gap-2">
 <x-lucide-check-circle class="w-4 h-4" />
 <span class="text-sm font-medium">{{ session('success') }}</span>
 </div>
 @endif
 @if(session('error'))
 <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded border border-red-200 dark:border-red-800/50 flex items-center gap-2">
 <x-lucide-alert-circle class="w-4 h-4" />
 <span class="text-sm font-medium">{{ session('error') }}</span>
 </div>
 @endif
 @if($errors->any())
 <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded border border-red-200 dark:border-red-800/50">
 <div class="flex items-center gap-2 mb-1">
 <x-lucide-alert-triangle class="w-4 h-4" />
 <span class="text-sm font-bold">Validation Error</span>
 </div>
 <ul class="list-disc list-inside text-sm ml-6">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif
 {{ $slot }}
 </div>
 </main>
 </div>

 <livewire:global-timer />
 @livewireScripts
 </body>
</html>
