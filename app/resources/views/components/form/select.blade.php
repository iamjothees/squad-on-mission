@props(['multiple' => false, 'noCreate' => false, 'wrapperClass' => 'w-full'])

<div class="{{ $wrapperClass }}" wire:ignore x-data="{
 init() {
 let ts = new TomSelect($refs.select, {
 plugins: {!! $multiple ?"['remove_button']" :"[]" !!},
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
 return '\x3Cdiv class=\'truncate max-w-full\'\x3E' + escape(data.text) + '\x3C/div\x3E';
 },
 optgroup_header: function(data, escape) {
 return '\x3Cdiv class=\'optgroup-header font-bold px-2 py-1\'\x3E' + escape(data.label) + '\x3C/div\x3E';
 },
 item: function(data, escape) {
 let html = data.custom_html || data.customHtml || data['custom-html'];
 if (!html && data.$option && data.$option.dataset) html = data.$option.dataset.customHtml;
 if (html) {
 return '\x3Cdiv class=\'item\'\x3E' + html + '\x3C/div\x3E';
 }
 return '\x3Cdiv class=\'item truncate max-w-full\'\x3E' + escape(data.text) + '\x3C/div\x3E';
 }
 }
 });
 
 ts.wrapper.addEventListener('click', (e) => {
 if (!ts.isOpen) {
 ts.open();
 if (ts.control_input) {
 ts.control_input.focus();
 }
 }
 });
 }
}">
 <select x-ref="select" {{ $attributes->merge(['class' => 'w-full bg-bg border border-border rounded-md px-3 py-2 text-fg focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600']) }} {{ $multiple ? 'multiple' : '' }}>
 {{ $slot }}
 </select>
</div>
