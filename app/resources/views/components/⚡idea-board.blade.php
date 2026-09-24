<?php

use Livewire\Component;
use App\Models\Idea;

new class extends Component
{
    public $ideas;
    
    public $title = '';
    public $description = '';
    public $status = 'new';
    
    public $editingId = null;
    
    public function mount() {
        $this->loadIdeas();
    }
    
    public function loadIdeas() {
        $this->ideas = Idea::where('user_id', auth()->id())->latest()->get();
    }
    
    public function save() {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($this->editingId) {
            $idea = Idea::where('user_id', auth()->id())->find($this->editingId);
            $idea->update([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
            ]);
        } else {
            Idea::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
            ]);
        }
        
        $this->resetForm();
        $this->loadIdeas();
    }
    
    public function edit($id) {
        $idea = Idea::where('user_id', auth()->id())->find($id);
        $this->editingId = $idea->id;
        $this->title = $idea->title;
        $this->description = $idea->description;
        $this->status = $idea->status;
    }
    
    public function delete($id) {
        Idea::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->loadIdeas();
    }
    
    public function resetForm() {
        $this->reset(['title', 'description', 'status', 'editingId']);
    }
    
    public function updateStatus($id, $newStatus) {
        Idea::where('user_id', auth()->id())->where('id', $id)->update(['status' => $newStatus]);
        $this->loadIdeas();
    }
};
?>

<div class="space-y-6">
    <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-800 dark:text-gray-200 mb-4">{{ $editingId ? 'Edit Idea' : 'Dump a Crazy Idea' }}</h3>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-3">
                <input type="text" wire:model="title" placeholder="What's the idea?" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border border-gray-700 rounded px-3 py-2 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-indigo-500" required>
            </div>
            <div>
                <select wire:model="status" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border border-gray-700 rounded px-3 py-2 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-indigo-500">
                    <option value="new">New</option>
                    <option value="evaluating">Evaluating</option>
                    <option value="planned">Planned</option>
                    <option value="building">Building</option>
                    <option value="shipped">Shipped</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <textarea wire:model="description" placeholder="Details, architecture, why it's crazy..." rows="2" class="w-full bg-gray-50/50 dark:bg-gray-900/50 border border-gray-700 rounded px-3 py-2 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-indigo-500"></textarea>
            </div>
            <div class="md:col-span-4 flex justify-end gap-2">
                @if($editingId)
                    <button type="button" wire:click="resetForm" class="px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:text-gray-200 transition-colors">Cancel</button>
                @endif
                <button type="submit" class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-white px-4 py-1.5 rounded text-xs font-medium transition-colors">
                    {{ $editingId ? 'Update Idea' : 'Save Idea' }}
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($ideas as $idea)
            <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4 flex flex-col hover:border-gray-300 dark:hover:border-gray-700 transition-colors group">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-medium text-gray-800 dark:text-gray-200 text-sm leading-tight group-hover:text-indigo-400 transition-colors">{{ $idea->title }}</h4>
                    <span class="px-2 py-0.5 rounded text-[10px] font-medium uppercase tracking-wider whitespace-nowrap ml-2
                        @if($idea->status === 'new') bg-blue-500/10 text-blue-400
                        @elseif($idea->status === 'evaluating') bg-purple-500/10 text-purple-400
                        @elseif($idea->status === 'planned') bg-yellow-500/10 text-yellow-400
                        @elseif($idea->status === 'building') bg-orange-500/10 text-orange-400
                        @elseif($idea->status === 'shipped') bg-green-500/10 text-green-400
                        @else bg-gray-500/10 text-gray-600 dark:text-gray-400
                        @endif
                    ">
                        {{ $idea->status }}
                    </span>
                </div>
                
                @if($idea->description)
                    <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-grow">{{ $idea->description }}</p>
                @else
                    <div class="flex-grow"></div>
                @endif
                
                <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-200 dark:border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-1">
                        <select wire:change="updateStatus({{ $idea->id }}, $event.target.value)" class="bg-transparent text-xs text-gray-500 hover:text-gray-800 dark:text-gray-800 dark:text-gray-200 focus:outline-none cursor-pointer">
                            <option value="new" @if($idea->status === 'new') selected @endif>New</option>
                            <option value="evaluating" @if($idea->status === 'evaluating') selected @endif>Evaluating</option>
                            <option value="planned" @if($idea->status === 'planned') selected @endif>Planned</option>
                            <option value="building" @if($idea->status === 'building') selected @endif>Building</option>
                            <option value="shipped" @if($idea->status === 'shipped') selected @endif>Shipped</option>
                            <option value="archived" @if($idea->status === 'archived') selected @endif>Archived</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="edit({{ $idea->id }})" class="text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded p-1 transition-colors">
                            <x-lucide-edit-2 class="w-3.5 h-3.5" />
                        </button>
                        <button wire:click="delete({{ $idea->id }})" wire:confirm="Delete this idea?" class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded p-1 transition-colors">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center border border-dashed border-gray-200 dark:border-gray-800 rounded text-gray-500 text-sm">
                No crazy ideas yet. Time to brainstorm!
            </div>
        @endforelse
    </div>
</div>