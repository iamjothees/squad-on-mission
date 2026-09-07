<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Enums\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use App\Services\ProjectService;
use App\Services\ClientService;

class ProjectController extends Controller
{
    protected ProjectService $projectService;
    protected ClientService $clientService;

    public function __construct(ProjectService $projectService, ClientService $clientService)
    {
        $this->projectService = $projectService;
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        $projects = $this->projectService->getAllProjects($request->all());
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('projects.index', compact('projects', 'tags'));
    }

    public function create()
    {
        $clients = $this->clientService->getActiveClientsList();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(ProjectStatus::class)],
            'budget' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $this->projectService->createProject($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = $this->clientService->getActiveClientsList();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(ProjectStatus::class)],
            'budget' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $this->projectService->updateProject($project, $validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function archive(Project $project)
    {
        $this->projectService->archiveProject($project);
        return redirect()->route('projects.index')->with('success', 'Project archived successfully.');
    }

    public function destroy(Project $project)
    {
        $this->projectService->deleteProject($project);
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
