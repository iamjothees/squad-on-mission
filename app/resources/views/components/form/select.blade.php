@props(['multiple' => false, 'noCreate' => false])

<div wire:ignore x-data="{
    init() {
        let ts = new TomSelect($refs.select, {
            plugins: {!! $multiple ? "['remove_button']" : "[]" !!},
            create: {{ $noCreate ? 'false' : 'true' }},
            persist: false,
            dropdownParent: 'body',
            render: {
                option: function(data, escape) {
                    let html = data.custom_html || data.customHtml || data['custom-html'];
                    if (!html && data.$option && data.$option.dataset) html = data.$option.dataset.customHtml;
                    if (html) {
                        return '\x3Cdiv\x3E' + html + '\x3C/div\x3E';
                    }
                    return '\x3Cdiv\x3E' + escape(data.text) + '\x3C/div\x3E';
                },
                item: function(data, escape) {
                    let html = data.custom_html || data.customHtml || data['custom-html'];
                    if (!html && data.$option && data.$option.dataset) html = data.$option.dataset.customHtml;
                    if (html) {
                        return '\x3Cdiv class=\'item\'\x3E' + html + '\x3C/div\x3E';
                    }
                    return '\x3Cdiv class=\'item\'\x3E' + escape(data.text) + '\x3C/div\x3E';
                }
            }
        });
    }
}">
    <select x-ref="select" {{ $attributes->merge(['class' => 'w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600']) }} {{ $multiple ? 'multiple' : '' }}>
        {{ $slot }}
    </select>
</div>
