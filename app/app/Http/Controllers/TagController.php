<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = $request->input('search');
        $tags = Tag::withCount(['projects', 'tasks'])
            ->when($search, function($q, $s) {
                $q->where('name', 'like', '%'.$s.'%');
            })
            ->orderBy('name')
            ->get();
        return view('tags.index', compact('tags'));
    }

    public function edit(Tag $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'color' => ['nullable', new \Illuminate\Validation\Rules\Enum(\App\Enums\TagColor::class)],
        ]);

        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $usageCount = $tag->projects()->count() + $tag->tasks()->count();
        
        if ($usageCount > 0) {
            return redirect()->route('tags.index')->with('error', 'Cannot delete tag. It is attached to ' . $usageCount . ' item(s).');
        }

        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}
