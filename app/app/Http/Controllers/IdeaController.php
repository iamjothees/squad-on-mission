<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index()
    {
        $ideas = Idea::where('user_id', auth()->id())->latest()->get();
        return view('ideas.index', compact('ideas'));
    }

    public function create()
    {
        return view('ideas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:new,evaluating,planned,building,shipped,archived'
        ]);

        $validated['user_id'] = auth()->id();
        Idea::create($validated);

        return redirect()->route('ideas.index')->with('success', 'Idea saved successfully.');
    }

    public function edit(Idea $idea)
    {
        if ($idea->user_id !== auth()->id()) abort(403);
        return view('ideas.edit', compact('idea'));
    }

    public function update(Request $request, Idea $idea)
    {
        if ($idea->user_id !== auth()->id()) abort(403);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:new,evaluating,planned,building,shipped,archived'
        ]);

        $idea->update($validated);

        return redirect()->route('ideas.index')->with('success', 'Idea updated successfully.');
    }

    public function destroy(Idea $idea)
    {
        if ($idea->user_id !== auth()->id()) abort(403);
        $idea->delete();
        return redirect()->route('ideas.index')->with('success', 'Idea deleted successfully.');
    }
}
