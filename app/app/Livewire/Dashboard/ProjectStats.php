<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use App\Enums\ProjectStatus;
use Livewire\Component;

class ProjectStats extends Component
{
    public function render()
    {
        $projects = Project::all();
        $total = $projects->count();
        $active = $projects->where('status', ProjectStatus::ACTIVE)->count();
        $completed = $projects->where('status', ProjectStatus::COMPLETED)->count();
        $onHold = $projects->where('status', ProjectStatus::ON_HOLD)->count();

        return view('livewire.dashboard.project-stats', compact('total', 'active', 'completed', 'onHold'));
    }
}
