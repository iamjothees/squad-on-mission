<?php

namespace App\Services;

use App\Models\Project;

use App\Enums\ProjectStatus;

class ProjectService
{
    /**
     * Get all projects, ordered by latest.
     */
    public function getAllProjects()
    {
        return Project::latest()->get();
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
