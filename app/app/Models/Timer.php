<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'purpose',
        'accumulated_seconds',
        'is_running',
        'last_started_at',
        'completed_at',
    ];

    protected $casts = [
        'is_running' => 'boolean',
        'accumulated_seconds' => 'integer',
        'last_started_at' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function tasks()
    {
        return $this->morphedByMany(Task::class, 'timerable');
    }

    public function projects()
    {
        return $this->morphedByMany(Project::class, 'timerable');
    }

    public function clients()
    {
        return $this->morphedByMany(Client::class, 'timerable');
    }

    public function getTimerablesAttribute()
    {
        return $this->tasks->merge($this->projects)->merge($this->clients);
    }

    public function logs()
    {
        return $this->hasMany(TimerLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ensure a timer is not attached redundantly across the hierarchy.
     * Throws an exception if invalid.
     */
    public function validateHierarchyAttachments(string $newType, int $newId)
    {
        $existingTasks = $this->tasks()->pluck('tasks.id')->toArray();
        $existingProjects = $this->projects()->pluck('projects.id')->toArray();
        $existingClients = $this->clients()->pluck('clients.id')->toArray();

        if ($newType === Task::class) {
            $task = Task::find($newId);
            if ($task && in_array($task->project_id, $existingProjects)) {
                throw new \Exception("Cannot attach Task because its parent Project is already attached to this timer.");
            }
            if ($task && $task->project && in_array($task->project->client_id, $existingClients)) {
                throw new \Exception("Cannot attach Task because its grandparent Client is already attached to this timer.");
            }
        } elseif ($newType === Project::class) {
            $project = Project::find($newId);
            if ($project && in_array($project->client_id, $existingClients)) {
                throw new \Exception("Cannot attach Project because its parent Client is already attached to this timer.");
            }
            // Check if any child tasks are already attached
            $childTaskIds = Task::where('project_id', $newId)->pluck('id')->toArray();
            if (array_intersect($childTaskIds, $existingTasks)) {
                throw new \Exception("Cannot attach Project because one of its Tasks is already directly attached to this timer.");
            }
        } elseif ($newType === Client::class) {
            // Check if any child projects or tasks are already attached
            $childProjectIds = Project::where('client_id', $newId)->pluck('id')->toArray();
            if (array_intersect($childProjectIds, $existingProjects)) {
                throw new \Exception("Cannot attach Client because one of its Projects is already directly attached to this timer.");
            }
            $childTaskIds = Task::whereIn('project_id', $childProjectIds)->pluck('id')->toArray();
            if (array_intersect($childTaskIds, $existingTasks)) {
                throw new \Exception("Cannot attach Client because one of its Tasks is already directly attached to this timer.");
            }
        }
    }
}
