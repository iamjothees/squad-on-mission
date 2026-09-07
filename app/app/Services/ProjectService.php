<?php

namespace App\Services;

use App\Models\Project;
use App\Enums\ProjectStatus;

class ProjectService
{
    protected EntityKeyService $entityKeyService;

    public function __construct(EntityKeyService $entityKeyService)
    {
        $this->entityKeyService = $entityKeyService;
    }

    public function getAllProjects(array $filters = [])
    {
        return Project::with(['tags', 'client'])
            ->withCount(['tasks' => function ($query) {
                $query->where('status', '!=', \App\Enums\TaskStatus::DONE->value);
            }])
            ->when(isset($filters['search']) && $filters['search'], function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            })
            ->when(isset($filters['status']) && $filters['status'], function ($query) use ($filters) {
                if (is_array($filters['status'])) {
                    $query->whereIn('status', $filters['status']);
                } else {
                    $query->where('status', $filters['status']);
                }
            })
            ->when(isset($filters['client_id']) && $filters['client_id'], function ($query) use ($filters) {
                if (is_array($filters['client_id'])) {
                    $query->whereIn('client_id', $filters['client_id']);
                } else {
                    $query->where('client_id', $filters['client_id']);
                }
            })
            ->when(isset($filters['tags']) && $filters['tags'], function ($query) use ($filters) {
                $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
                $query->whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('tags.id', $tags);
                });
            })
            ->latest()
            ->get();
    }

    public function getActiveProjectsList()
    {
        return Project::where('status', '!=', ProjectStatus::ARCHIVED->value)
            ->orderBy('name')
            ->get();
    }

    public function createProject(array $data): Project
    {
        $project = Project::create($data);
        
        if (isset($data['tags'])) {
            $project->syncTags($data['tags']);
        }
        
        $this->entityKeyService->generateProjectKey($project);
        
        return $project;
    }

    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);
        
        if (isset($data['tags'])) {
            $project->syncTags($data['tags']);
        }
        
        if (array_key_exists('client_id', $data)) {
            $this->entityKeyService->generateProjectKey($project);
        }
        
        return $project;
    }

    public function archiveProject(Project $project): void
    {
        $project->update(['status' => ProjectStatus::ARCHIVED->value]);
    }

    public function deleteProject(Project $project): void
    {
        $project->delete();
    }
}
