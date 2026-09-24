<x-layouts.app title="Time Analytics">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-200 leading-tight">
                {{ __('Time Analytics') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:reports.time-report />
        </div>
    </div>
</x-layouts.app>
