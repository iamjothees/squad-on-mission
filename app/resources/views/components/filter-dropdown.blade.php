@props([
    'name',
    'label',
    'options' => [],
    'selected' => []
])

@php
    $selected = is_array($selected) ? $selected : (is_string($selected) ? [$selected] : collect($selected)->toArray());
    $selectedCount = count($selected);
    
    $displayText = $label;
    if ($selectedCount == 1) {
        $displayText = $label . ': ' . ($options[$selected[0]] ?? $selected[0]);
    } elseif ($selectedCount == 2) {
        $displayText = $label . ': ' . ($options[$selected[0]] ?? $selected[0]) . ', ' . ($options[$selected[1]] ?? $selected[1]);
    } elseif ($selectedCount > 2) {
        $displayText = $label . ': ' . $selectedCount . ' selected';
    }
@endphp

<div x-data="{ open: false, search: '' }" class="relative" @click.away="open = false; search = ''">
    <button type="button" @click="open = !open; if(!open) search = ''" 
            class="flex items-center gap-2 bg-white dark:bg-gray-900 border {{ $selectedCount > 0 ? 'border-indigo-300 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-800' }} rounded-full px-4 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none shadow-sm transition-colors">
        {{ $displayText }}
        <x-lucide-chevron-down class="w-4 h-4 text-gray-400" x-bind:class="open ? 'rotate-180' : ''" style="transition: transform 0.2s;" />
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 ring-1 ring-black ring-opacity-5 z-50"
         style="display: none;">
        @if(count($options) > 5)
        <div class="p-2 border-b border-gray-100 dark:border-gray-800 sticky top-0 bg-white dark:bg-gray-950 z-10">
            <div class="relative">
                <x-lucide-search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
                <input type="text" x-model="search" placeholder="Search..." class="w-full pl-8 pr-2 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-colors placeholder-gray-400" @keydown.enter.prevent="">
            </div>
        </div>
        @endif
        <div class="py-1 max-h-60 overflow-y-auto" role="menu" aria-orientation="vertical">
            @foreach($options as $value => $optionLabel)
                <label x-show="search === '' || '{{ htmlspecialchars(strtolower($optionLabel), ENT_QUOTES) }}'.includes(search.toLowerCase())" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 cursor-pointer">
                    <input type="checkbox" name="{{ $name }}[]" value="{{ $value }}" 
                           @checked(in_array((string)$value, array_map('strval', $selected)))
                           onchange="this.form.submit()"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:focus:ring-indigo-600 mr-3">
                    <span class="truncate">{{ $optionLabel }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
