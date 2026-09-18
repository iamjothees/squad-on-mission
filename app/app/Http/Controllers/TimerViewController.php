<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TimerViewController extends Controller
{
    public function unassigned()
    {
        $timers = Timer::whereNull('timerable_type')
            ->whereNull('timerable_id')
            ->latest('updated_at')
            ->get();
            
        $projects = Project::orderBy('name')->get();
        $tasks = Task::orderBy('title')->get();

        return view('timers.unassigned', compact('timers', 'projects', 'tasks'));
    }

    public function show(Timer $timer)
    {
        $timer->load(['logs' => function ($q) {
            $q->orderBy('started_at', 'desc');
        }, 'timerable']);

        $projects = Project::orderBy('name')->get();
        $tasks = Task::orderBy('title')->get();

        return view('timers.show', compact('timer', 'projects', 'tasks'));
    }
    
    public function assign(Request $request, Timer $timer)
    {
        $request->validate([
            'timerable' => 'required|string',
        ]);
        
        $parts = explode(':', $request->timerable);
        if (count($parts) === 2) {
            $type = $parts[0] === 'project' ? Project::class : Task::class;
            $id = $parts[1];
            
            $timer->update([
                'timerable_type' => $type,
                'timerable_id' => $id,
                'purpose' => 'task_tracking',
            ]);
            
            return back()->with('success', 'Timer successfully assigned!');
        }
        
        return back()->with('error', 'Invalid assignment data.');
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*' => 'nullable|string',
        ]);
        
        $count = 0;
        foreach ($request->assignments as $timerId => $timerable) {
            if (empty($timerable)) continue;
            
            $timer = Timer::find($timerId);
            if (!$timer) continue;
            
            $parts = explode(':', $timerable);
            if (count($parts) === 2) {
                $type = $parts[0] === 'project' ? Project::class : Task::class;
                $id = $parts[1];
                
                $timer->update([
                    'timerable_type' => $type,
                    'timerable_id' => $id,
                    'purpose' => 'task_tracking',
                ]);
                $count++;
            }
        }
        
        return back()->with('success', $count . ' timers successfully assigned!');
    }
}
