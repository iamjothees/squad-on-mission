<?php

use Livewire\Component;
use App\Models\Lead;

new class extends Component
{
    public $leads;
    
    public $name = '';
    public $company = '';
    public $email = '';
    public $phone = '';
    public $status = 'new';
    public $notes = '';
    public $value = '';
    
    public $editingId = null;
    
    public function mount() {
        $this->loadLeads();
    }
    
    public function loadLeads() {
        $this->leads = Lead::where('user_id', auth()->id())->latest()->get();
    }
    
    public function save() {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'value' => 'nullable|numeric',
        ]);
        
        if ($this->editingId) {
            $lead = Lead::where('user_id', auth()->id())->find($this->editingId);
            $lead->update([
                'name' => $this->name,
                'company' => $this->company,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'notes' => $this->notes,
                'value' => $this->value,
            ]);
        } else {
            Lead::create([
                'user_id' => auth()->id(),
                'name' => $this->name,
                'company' => $this->company,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'notes' => $this->notes,
                'value' => $this->value,
            ]);
        }
        
        $this->resetForm();
        $this->loadLeads();
    }
    
    public function edit($id) {
        $lead = Lead::where('user_id', auth()->id())->find($id);
        $this->editingId = $lead->id;
        $this->name = $lead->name;
        $this->company = $lead->company;
        $this->email = $lead->email;
        $this->phone = $lead->phone;
        $this->status = $lead->status;
        $this->notes = $lead->notes;
        $this->value = $lead->value;
    }
    
    public function delete($id) {
        Lead::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->loadLeads();
    }
    
    public function resetForm() {
        $this->reset(['name', 'company', 'email', 'phone', 'status', 'notes', 'value', 'editingId']);
    }
};
?>

<div class="space-y-6">
    <div class="bg-[#1C1C1E] border border-gray-800 rounded p-4">
        <h3 class="text-sm font-semibold text-gray-300 mb-4">{{ $editingId ? 'Edit Lead' : 'New Lead' }}</h3>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Name *</label>
                <input type="text" wire:model="name" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Company</label>
                <input type="text" wire:model="company" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Phone</label>
                <input type="text" wire:model="phone" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Value ($)</label>
                <input type="number" step="0.01" wire:model="value" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select wire:model="status" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="negotiating">Negotiating</option>
                    <option value="won">Won</option>
                    <option value="lost">Lost</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Notes / Call Log</label>
                <input type="text" wire:model="notes" class="w-full bg-[#2C2C2E] border border-gray-700 rounded px-2 py-1 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            </div>
            <div class="lg:col-span-4 flex justify-end gap-2 mt-2">
                @if($editingId)
                    <button type="button" wire:click="resetForm" class="px-3 py-1.5 text-xs text-gray-400 hover:text-gray-200 transition-colors">Cancel</button>
                @endif
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-1.5 rounded text-xs font-medium transition-colors">
                    {{ $editingId ? 'Update Lead' : 'Add Lead' }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-[#1C1C1E] border border-gray-800 rounded overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-[#2C2C2E] text-gray-400 border-b border-gray-800 text-xs">
                    <th class="px-4 py-2 font-medium">Name</th>
                    <th class="px-4 py-2 font-medium">Company</th>
                    <th class="px-4 py-2 font-medium">Contact</th>
                    <th class="px-4 py-2 font-medium">Value</th>
                    <th class="px-4 py-2 font-medium">Status</th>
                    <th class="px-4 py-2 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($leads as $lead)
                    <tr class="hover:bg-[#252527] transition-colors">
                        <td class="px-4 py-3 text-gray-200 font-medium">
                            {{ $lead->name }}
                            @if($lead->notes)
                                <div class="text-[10px] text-gray-500 mt-0.5 truncate max-w-xs" title="{{ $lead->notes }}">{{ $lead->notes }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ $lead->company ?: '-' }}</td>
                        <td class="px-4 py-3 text-gray-400">
                            <div class="flex flex-col gap-0.5">
                                @if($lead->email)<span class="text-xs">{{ $lead->email }}</span>@endif
                                @if($lead->phone)<span class="text-xs">{{ $lead->phone }}</span>@endif
                                @if(!$lead->email && !$lead->phone)-@endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-green-400 tabular-nums">{{ $lead->value ? '$'.number_format($lead->value, 2) : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium uppercase tracking-wider
                                @if($lead->status === 'new') bg-blue-500/10 text-blue-400
                                @elseif($lead->status === 'contacted') bg-yellow-500/10 text-yellow-400
                                @elseif($lead->status === 'negotiating') bg-orange-500/10 text-orange-400
                                @elseif($lead->status === 'won') bg-green-500/10 text-green-400
                                @else bg-red-500/10 text-red-400
                                @endif
                            ">
                                {{ $lead->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button wire:click="edit({{ $lead->id }})" class="text-gray-500 hover:text-indigo-400 transition-colors">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button wire:click="delete({{ $lead->id }})" wire:confirm="Are you sure you want to delete this lead?" class="text-gray-500 hover:text-red-400 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-sm">No leads recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>