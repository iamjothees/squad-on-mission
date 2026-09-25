<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
 //
};
?>

<div class="flex flex-col gap-4">
 <!-- Top Stats Row -->
 <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
 <div class="border border-border p-4 bg-surface flex flex-col rounded-lg shadow-sm transition-shadow hover:shadow-md">
 <div class="text-xs uppercase text-fg-muted font-semibold tracking-wider">Total Users</div>
 <div class="font-mono text-2xl leading-none mt-2 text-fg dark:text-gray-100 font-bold">1,024</div>
 </div>
 <div class="border border-border p-4 bg-surface flex flex-col rounded-lg shadow-sm transition-shadow hover:shadow-md">
 <div class="text-xs uppercase text-fg-muted font-semibold tracking-wider">Active Sessions</div>
 <div class="font-mono text-2xl leading-none mt-2 text-fg dark:text-gray-100 font-bold">56</div>
 </div>
 <div class="border border-border p-4 bg-surface flex flex-col rounded-lg shadow-sm transition-shadow hover:shadow-md">
 <div class="text-xs uppercase text-fg-muted font-semibold tracking-wider">Server Load</div>
 <div class="font-mono text-2xl leading-none mt-2 text-fg dark:text-gray-100 font-bold">12%</div>
 </div>
 <div class="border border-red-200 dark:border-red-900/50 p-4 bg-red-50 dark:bg-red-950 flex flex-col rounded-lg shadow-sm transition-shadow hover:shadow-md">
 <div class="text-xs uppercase text-red-600 dark:text-red-400 font-semibold tracking-wider">Critical Issues</div>
 <div class="font-mono text-2xl leading-none mt-2 text-red-700 dark:text-red-300 font-bold">3</div>
 </div>
 </div>
 
 <!-- Data Table -->
 <div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface border-b border-border text-xs uppercase tracking-wider text-fg-muted">
 <th class="px-4 py-3 border-r border-border font-semibold w-20">ID</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-64">Name</th>
 <th class="px-4 py-3 border-r border-border font-semibold w-40">Status</th>
 <th class="px-4 py-3 font-semibold">Last Active</th>
 </tr>
 </thead>
 <tbody class="text-sm">
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">1001</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg font-medium">John Doe</td>
 <td class="px-4 py-2.5 border-r border-border/50"><span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500 dark:bg-green-400"></span> Active</span></td>
 <td class="px-4 py-2.5 text-fg-muted">2 mins ago</td>
 </tr>
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">1002</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg font-medium">Jane Smith</td>
 <td class="px-4 py-2.5 border-r border-border/50"><span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md bg-surface text-fg-muted text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Offline</span></td>
 <td class="px-4 py-2.5 text-fg-muted">1 hr ago</td>
 </tr>
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">1003</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg font-medium">Mike Johnson</td>
 <td class="px-4 py-2.5 border-r border-border/50"><span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-red-500 dark:bg-red-400"></span> Banned</span></td>
 <td class="px-4 py-2.5 text-fg-muted">Yesterday</td>
 </tr>
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">1004</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg font-medium">Alice Williams</td>
 <td class="px-4 py-2.5 border-r border-border/50"><span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500 dark:bg-green-400"></span> Active</span></td>
 <td class="px-4 py-2.5 text-fg-muted">Just now</td>
 </tr>
 <tr class="hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-2.5 border-r border-border/50 font-mono text-fg-muted">1005</td>
 <td class="px-4 py-2.5 border-r border-border/50 text-fg font-medium">Bob Brown</td>
 <td class="px-4 py-2.5 border-r border-border/50"><span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md bg-surface text-fg-muted text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Offline</span></td>
 <td class="px-4 py-2.5 text-fg-muted">2 days ago</td>
 </tr>
 </tbody>
 </table>
 </div>
 </div>
</div>
