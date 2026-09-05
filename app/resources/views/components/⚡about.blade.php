<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app', ['title' => 'About Us'])] class extends Component
{
    //
};
?>

<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h2 class="text-xl font-bold mb-4">About This App</h2>
            <p>
                This application is set up with the TALL stack (Tailwind, Alpine, Laravel, Livewire) 
                using a Laptop-first SPA approach. 
            </p>
            <p class="mt-4">
                Navigating between the Dashboard and this page feels instant thanks to <code class="font-mono text-sm bg-gray-100 p-1 rounded">wire:navigate</code>.
            </p>
        </div>
    </div>
</div>