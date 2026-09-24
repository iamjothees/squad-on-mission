<x-layouts.app title="Reports">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-200 leading-tight">
                {{ __('Visual Reports & Analytics') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:report-dashboard />
        </div>
    </div>
</x-layouts.app>
