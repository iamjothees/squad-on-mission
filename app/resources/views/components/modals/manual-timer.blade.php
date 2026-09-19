@props(['timerableType' => null, 'timerableId' => null])

<div x-data="{ open: false }" @open-manual-timer-modal.window="open = true">
    <button type="button" @click="open = true" {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 transition-colors']) }}>
        <x-lucide-plus class="w-4 h-4" />
        <span>{{ $slot->isEmpty() ? 'Create Manual Timer' : $slot }}</span>
    </button>

    <!-- Modal Backdrop -->
    <div x-show="open" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[100] bg-gray-900/80 backdrop-blur-sm" 
         style="display: none;"
         aria-hidden="true"></div>

    <!-- Modal Panel -->
    <div x-show="open"
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-[101] flex items-center justify-center p-4 sm:p-6"
         style="display: none;">
        
        <div x-show="open"
             @click.away="open = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white dark:bg-gray-900 rounded-xl shadow-xl w-full max-w-md overflow-hidden border border-gray-200 dark:border-gray-800">
            
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create Manual Timer</h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <form action="{{ route('timers.store') }}" method="POST">
                @csrf
                <div class="px-6 py-6 space-y-4">
                    @if($timerableType && $timerableId)
                        <input type="hidden" name="timerable_type" value="{{ $timerableType }}">
                        <input type="hidden" name="timerable_id" value="{{ $timerableId }}">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 bg-indigo-50 dark:bg-indigo-900/30 p-3 rounded-lg border border-indigo-100 dark:border-indigo-800">
                            This timer will be automatically assigned to the current entity.
                        </div>
                    @endif

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select User</label>
                        <select id="user_id" name="user_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(\App\Models\User::where('name', '!=', 'System')->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">System user cannot own timers.</p>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">Cancel</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 shadow-sm">
                        Create Timer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
