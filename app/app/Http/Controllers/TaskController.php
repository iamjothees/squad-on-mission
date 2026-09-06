<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $filters = $request->only(['search', 'status', 'project_id', 'tags']);
        $tasks = $this->taskService->getAllTasks($filters);
        $projects = \App\Models\Project::orderBy('name')->get();
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('tasks.index', compact('tasks', 'projects', 'tags'));
    }

    public function create()
    {
        $projects = Project::where('status', '!=', 'archived')->orderBy('name')->get();
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

    public function edit(Task $task)
    {
        $projects = Project::where('status', '!=', 'archived')->orderBy('name')->get();
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

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function complete(Task $task)
    {
        $this->taskService->completeTask($task);
        return redirect()->route('tasks.index')->with('success', 'Task marked as complete!');
    }
}
