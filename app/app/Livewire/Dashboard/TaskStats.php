<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use App\Enums\TaskStatus;
use Livewire\Component;
use Carbon\Carbon;

class TaskStats extends Component
{
    public function render()
    {
        $tasks = Task::all();
        $total = $tasks->count();
        $open = $tasks->where('status', '!=', TaskStatus::DONE)->count();
        $dueSoon = $tasks->where('status', '!=', TaskStatus::DONE)
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(3))
            ->count();
        $overdue = $tasks->where('status', '!=', TaskStatus::DONE)
            ->where('due_date', '<', now())
            ->count();

        $noDueDate = $tasks->where('status', '!=', TaskStatus::DONE)->whereNull('due_date')->count();

        return view('livewire.dashboard.task-stats', compact('total', 'open', 'dueSoon', 'overdue', 'noDueDate'));
    }
}
