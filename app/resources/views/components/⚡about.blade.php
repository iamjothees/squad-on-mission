<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm bg-white dark:bg-gray-950">
    <div class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 px-4 py-3 text-xs uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wider">
        System Information
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <tbody>
                <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <td class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 w-48 border-r border-gray-100 dark:border-gray-800/50">Framework</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <td class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-100 dark:border-gray-800/50">Stack</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">TALL (Tailwind v4, Alpine.js, Livewire v3)</td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <td class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-100 dark:border-gray-800/50">Design System</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">Modern Dense UI / Balanced Breathing Room</td>
                </tr>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <td class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-100 dark:border-gray-800/50">Environment</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-mono text-xs bg-gray-100 dark:bg-gray-800 inline-block px-2 py-1 rounded-md mt-1.5 ml-2 mb-1.5">{{ env('APP_ENV', 'production') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
