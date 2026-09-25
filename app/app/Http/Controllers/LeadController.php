<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::where('user_id', auth()->id())->latest()->get();
        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:new,contacted,negotiating,won,lost',
            'notes' => 'nullable|string',
            'value' => 'nullable|numeric|min:0'
        ]);

        $validated['user_id'] = auth()->id();
        Lead::create($validated);

        return redirect()->route('leads.index')->with('success', 'Lead added successfully.');
    }

    public function edit(Lead $lead)
    {
        if ($lead->user_id !== auth()->id()) abort(403);
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        if ($lead->user_id !== auth()->id()) abort(403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:new,contacted,negotiating,won,lost',
            'notes' => 'nullable|string',
            'value' => 'nullable|numeric|min:0'
        ]);

        $lead->update($validated);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        if ($lead->user_id !== auth()->id()) abort(403);
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }
}
