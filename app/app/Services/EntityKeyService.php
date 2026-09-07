<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\EntityKey;
use Illuminate\Support\Str;

class EntityKeyService
{
    public function generateClientKey(Client $client)
    {
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', explode(' ', $client->name)[0]));
        if (empty($prefix)) $prefix = 'CLI';
        
        $key = $prefix;
        $counter = 1;
        // Exclude current client's existing primary key if any
        while (EntityKey::where('key', $key)->where(function($q) use ($client) {
            $q->where('keyable_id', '!=', $client->id)
              ->orWhere('keyable_type', '!=', Client::class);
        })->exists()) {
            $key = $prefix . $counter;
            $counter++;
        }
        
        if ($client->key !== $key) {
            $client->update(['key' => $key]);
            $this->syncRegistry($client, $key);
            
            // Cascade to projects
            foreach ($client->projects as $project) {
                $this->generateProjectKey($project);
            }
        }
    }

    public function generateProjectKey(Project $project)
    {
        $clientKey = $project->client ? $project->client->key : 'INT';
        
        $key = $clientKey . '-P' . $project->id;
        $counter = 1;
        while (EntityKey::where('key', $key)->where(function($q) use ($project) {
            $q->where('keyable_id', '!=', $project->id)
              ->orWhere('keyable_type', '!=', Project::class);
        })->exists()) {
            $key = $clientKey . '-P' . $project->id . '-' . $counter;
            $counter++;
        }
        
        if ($project->key !== $key) {
            $project->update(['key' => $key]);
            $this->syncRegistry($project, $key);
            
            // Cascade to tasks
            foreach ($project->tasks as $task) {
                $this->generateTaskKey($task);
            }
        }
    }

    public function generateTaskKey(Task $task)
    {
        $projectKey = $task->project ? $task->project->key : 'GEN';
        
        $key = $projectKey . '-T' . $task->id;
        $counter = 1;
        while (EntityKey::where('key', $key)->where(function($q) use ($task) {
            $q->where('keyable_id', '!=', $task->id)
              ->orWhere('keyable_type', '!=', Task::class);
        })->exists()) {
            $key = $projectKey . '-T' . $task->id . '-' . $counter;
            $counter++;
        }
        
        if ($task->key !== $key) {
            $task->update(['key' => $key]);
            $this->syncRegistry($task, $key);
        }
    }

    protected function syncRegistry($model, $newKey)
    {
        // Demote old keys
        $model->entityKeys()->update(['is_primary' => false]);
        
        // Create or promote new key
        $existing = $model->entityKeys()->where('key', $newKey)->first();
        if ($existing) {
            $existing->update(['is_primary' => true]);
        } else {
            EntityKey::create([
                'key' => $newKey,
                'keyable_type' => get_class($model),
                'keyable_id' => $model->id,
                'is_primary' => true,
            ]);
        }
    }
}
