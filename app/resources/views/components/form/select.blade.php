@props(['multiple' => false])

<div wire:ignore x-data="{
    init() {
        let ts = new TomSelect($refs.select, {
            plugins: ['remove_button'],
            create: true,
            persist: false,
        });
    }
}">
    <select x-ref="select" {{ $attributes->merge(['class' => 'w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600']) }} {{ $multiple ? 'multiple' : '' }}>
        {{ $slot }}
    </select>
</div>
