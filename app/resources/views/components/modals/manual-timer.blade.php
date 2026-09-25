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
 class="bg-surface rounded-xl shadow-xl w-full max-w-md overflow-hidden border border-border">
 
 <div class="px-6 py-4 border-b border-border flex justify-between items-center">
 <h3 class="text-lg font-bold text-fg">Create Manual Timer</h3>
 <button type="button" @click="open = false" class="text-gray-400 hover:text-fg-muted focus:outline-none">
 <x-lucide-x class="w-5 h-5" />
 </button>
 </div>

 <form action="{{ route('timers.store') }}" method="POST">
 @csrf
 <div class="px-6 py-6 space-y-4">
 @if($timerableType && $timerableId)
 <input type="hidden" name="timerable_type" value="{{ $timerableType }}">
 <input type="hidden" name="timerable_id" value="{{ $timerableId }}">
 <div class="text-sm text-fg-muted mb-4 bg-accent/10 dark:bg-accent/20 p-3 rounded-lg border border-indigo-100 dark:border-indigo-800">
 This timer will be automatically assigned to the current entity.
 </div>
 @endif

 <div>
 <label for="user_id" class="block text-sm font-medium text-fg-muted mb-1">Select User</label>
 <select id="user_id" name="user_id" class="w-full bg-surface border border-border rounded-lg px-3 py-2 text-sm text-fg focus:outline-none focus:ring-2 focus:ring-accent">
 @foreach(\App\Models\User::where('name', '!=', 'System')->orderBy('name')->get() as $user)
 <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
 {{ $user->name }} ({{ $user->email }})
 </option>
 @endforeach
 </select>
 <p class="mt-1.5 text-xs text-fg-muted">System user cannot own timers.</p>
 </div>
 </div>
 
 <div class="px-6 py-4 border-t border-border bg-bg flex justify-end gap-3">
 <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-fg-muted hover:text-fg transition-colors">Cancel</button>
 <button type="submit" class="inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors bg-accent text-accent-fg text-white hover:opacity-90 px-4 py-2 shadow-sm">
 Create Timer
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
