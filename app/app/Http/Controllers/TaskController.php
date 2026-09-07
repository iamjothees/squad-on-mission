<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use App\Services\TaskService;
use App\Services\ProjectService;

class TaskController extends Controller
{
    protected TaskService $taskService;
    protected ProjectService $projectService;

    public function __construct(TaskService $taskService, ProjectService $projectService)
    {
        $this->taskService = $taskService;
        $this->projectService = $projectService;
    }

    public function index(Request $request)
    {
        $tasks = $this->taskService->getAllTasks($request->all());
        $projects = $this->projectService->getActiveProjectsList();
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('tasks.index', compact('tasks', 'projects', 'tags'));
    }

    public function create()
    {
        $projects = $this->projectService->getActiveProjectsList();
        return view('tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(TaskStatus::class)],
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'priority' => ['required', new Enum(TaskPriority::class)],
            'due_date' => 'nullable|date',
        ]);

        $this->taskService->createTask($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = $this->projectService->getActiveProjectsList();
        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(TaskStatus::class)],
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'priority' => ['required', new Enum(TaskPriority::class)],
            'due_date' => 'nullable|date',
        ]);

        $this->taskService->updateTask($task, $validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function complete(Task $task)
    {
        $this->taskService->completeTask($task);
        return back()->with('success', 'Task marked as done.');
    }

    public function nextStatus(Task $task)
    {
        $this->taskService->nextStatus($task);
        return back()->with('success', 'Task status updated.');
    }

    public function destroy(Task $task)
    {
        $this->taskService->deleteTask($task);
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
