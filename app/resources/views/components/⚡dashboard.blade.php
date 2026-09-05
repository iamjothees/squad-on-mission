<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app', ['title' => 'Dashboard'])] class extends Component
{
    //
};
?>

<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            {{ __("You're logged in to the SPA!") }}
            
            <div class="mt-4 p-4 bg-gray-50 border rounded text-sm">
                This page uses <code class="font-bold">wire:navigate</code> to feel like a true Single Page Application.
            </div>
        </div>
    </div>
</div>