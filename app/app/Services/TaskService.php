<?php

namespace App\Services;

use App\Models\Task;
use App\Enums\TaskStatus;

class TaskService
{
    /**
     * Get all tasks, ordered by due date and latest.
     */
    public function getAllTasks()
    {
        return Task::with('project')
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
