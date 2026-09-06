<?php

namespace App\Services;

use App\Models\Task;
use App\Enums\TaskStatus;

class TaskService
{
    /**
     * Get all tasks, ordered by due date and latest.
     */
    public function getAllTasks(array $filters = [])
    {
        return Task::with('project')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('title', 'like', '%'.$search.'%')
                      ->orWhere('description', 'like', '%'.$search.'%');
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if (is_array($status)) {
                    $query->whereIn('status', $status);
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($filters['project_id'] ?? null, function ($query, $project_id) {
                if (is_array($project_id)) {
                    $query->whereIn('project_id', $project_id);
                } else {
                    $query->where('project_id', $project_id);
                }
            })
            ->when($filters['tags'] ?? null, function ($query, $tags) {
                if (!is_array($tags)) $tags = [$tags];
                $query->whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('tags.id', $tags);
                });
            })
            ->orderByRaw('ISNULL(due_date), due_date ASC')
            ->latest()
            ->get();
    }

    /**
     * Create a new task.
     */
    public function createTask(array $data): Task
    {
        $task = Task::create($data);
        
        if (isset($data['tags'])) {
            $task->syncTags($data['tags']);
        }
        
        return $task;
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        
        if (isset($data['tags'])) {
            $task->syncTags($data['tags']);
        }
        
        // If status changed to DONE, automatically set completed_at
        if (isset($data['status']) && $data['status'] === TaskStatus::DONE->value && is_null($task->completed_at)) {
            $task->update(['completed_at' => now()]);
        } elseif (isset($data['status']) && $data['status'] !== TaskStatus::DONE->value && !is_null($task->completed_at)) {
            $task->update(['completed_at' => null]);
        }
        
        return $task;
    }

    /**
     * Mark task as complete.
     */
    public function completeTask(Task $task): Task
    {
        return $this->updateTask($task, ['status' => TaskStatus::DONE->value]);
    }
}
