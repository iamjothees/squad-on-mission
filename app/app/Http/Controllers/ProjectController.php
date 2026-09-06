<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Enums\ProjectStatus;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $filters = $request->only(['search', 'status', 'tags']);
        $projects = $this->projectService->getAllProjects($filters);
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('projects.index', compact('projects', 'tags'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(ProjectStatus::class)],
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'client_name' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $this->projectService->createProject($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(ProjectStatus::class)],
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'client_name' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $this->projectService->updateProject($project, $validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function archive(Project $project)
    {
        $this->projectService->archiveProject($project);
        return redirect()->route('projects.index')->with('success', 'Project archived successfully.');
    }
}
