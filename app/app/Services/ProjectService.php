<?php

namespace App\Services;

use App\Models\Project;

use App\Enums\ProjectStatus;

class ProjectService
{
    /**
     * Get all projects, ordered by latest.
     */
    public function getAllProjects(array $filters = [])
    {
        return Project::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%')
                      ->orWhere('client_name', 'like', '%'.$search.'%');
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if (is_array($status)) {
                    $query->whereIn('status', $status);
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($filters['tags'] ?? null, function ($query, $tags) {
                if (!is_array($tags)) $tags = [$tags];
                $query->whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('tags.id', $tags);
                });
            })
            ->latest()
            ->get();
    }

    /**
     * Create a new project.
     */
    public function createProject(array $data): Project
    {
        $project = Project::create($data);
        
        if (isset($data['tags'])) {
            $project->syncTags($data['tags']);
        }
        
        return $project;
    }

    /**
     * Update an existing project.
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);
        
        if (isset($data['tags'])) {
            $project->syncTags($data['tags']);
        }
        
        return $project;
    }

    /**
     * Change the status of a project.
     */
    public function changeStatus(Project $project, ProjectStatus $status): Project
    {
        $project->update(['status' => $status]);
        return $project;
    }

    /**
     * Archive a project (set status to archived).
     */
    public function archiveProject(Project $project): Project
    {
        return $this->changeStatus($project, ProjectStatus::ARCHIVED);
    }
}
