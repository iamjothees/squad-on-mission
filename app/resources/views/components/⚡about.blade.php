<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
 //
};
?>

<div class="border border-border rounded-lg overflow-hidden shadow-sm bg-surface">
 <div class="bg-surface border-b border-border px-4 py-3 text-xs uppercase font-bold text-fg-muted tracking-wider">
 System Information
 </div>
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse text-sm">
 <tbody>
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-3 font-semibold text-fg-muted w-48 border-r border-border/50">Framework</td>
 <td class="px-4 py-3 text-fg font-medium">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</td>
 </tr>
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-3 font-semibold text-fg-muted border-r border-border/50">Stack</td>
 <td class="px-4 py-3 text-fg font-medium">TALL (Tailwind v4, Alpine.js, Livewire v3)</td>
 </tr>
 <tr class="border-b border-border/50 hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-3 font-semibold text-fg-muted border-r border-border/50">Design System</td>
 <td class="px-4 py-3 text-fg font-medium">Modern Dense UI / Balanced Breathing Room</td>
 </tr>
 <tr class="hover:bg-surface dark:hover:bg-gray-900 transition-colors">
 <td class="px-4 py-3 font-semibold text-fg-muted border-r border-border/50">Environment</td>
 <td class="px-4 py-3 text-fg font-mono text-xs bg-surface inline-block px-2 py-1 rounded-md mt-1.5 ml-2 mb-1.5">{{ env('APP_ENV', 'production') }}</td>
 </tr>
 </tbody>
 </table>
 </div>
</div>
