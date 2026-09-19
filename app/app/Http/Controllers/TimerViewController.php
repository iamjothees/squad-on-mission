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

    public function updateLog(Request $request, Timer $timer, \App\Models\TimerLog $log)
    {
        if ($log->timer_id !== $timer->id) {
            abort(404);
        }

        $request->validate([
            'started_at' => 'required|date',
            'stopped_at' => 'nullable|date|after:started_at',
        ]);

        $startedMs = strtotime($request->started_at) * 1000;
        $stoppedMs = $request->stopped_at ? strtotime($request->stopped_at) * 1000 : null;
        
        // Strong logical validations
        if ($stoppedMs && $startedMs >= $stoppedMs) {
            return back()->with('error', 'Start time must be before stop time.');
        }
        
        // Check overlaps with other logs on the same timer
        $overlap = $timer->logs()->where('id', '!=', $log->id)
            ->where(function ($query) use ($startedMs, $stoppedMs) {
                // If the other log is still running, its stopped_at is null (effectively infinity)
                if ($stoppedMs) {
                    $query->where('started_at', '<', $stoppedMs)
                          ->where(function ($q) use ($startedMs) {
                              $q->where('stopped_at', '>', $startedMs)
                                ->orWhereNull('stopped_at');
                          });
                } else {
                    // New log is running (no stop time)
                    $query->where('stopped_at', '>', $startedMs)
                          ->orWhereNull('stopped_at');
                }
            })->exists();

        if ($overlap) {
            return back()->with('error', 'Log time overlaps with an existing time log.');
        }

        $duration = $stoppedMs ? floor(($stoppedMs - $startedMs) / 1000) : 0;

        $log->update([
            'started_at' => $startedMs,
            'stopped_at' => $stoppedMs,
            'duration_seconds' => $duration,
        ]);

        // Recalculate parent timer's accumulated_seconds
        $totalDuration = $timer->logs()->whereNotNull('stopped_at')->sum('duration_seconds');
        $timer->update([
            'accumulated_seconds' => $totalDuration,
        ]);

        return back()->with('success', 'Timer log updated successfully.');
    }

    public function storeLog(Request $request, Timer $timer)
    {
        $request->validate([
            'started_at' => 'required|date',
            'stopped_at' => 'required|date|after:started_at',
        ]);

        $startedMs = strtotime($request->started_at) * 1000;
        $stoppedMs = strtotime($request->stopped_at) * 1000;
        
        if ($startedMs >= $stoppedMs) {
            return back()->with('error', 'Start time must be before stop time.');
        }
        
        // Check overlaps with other logs on the same timer
        $overlap = $timer->logs()->where(function ($query) use ($startedMs, $stoppedMs) {
            $query->where('started_at', '<', $stoppedMs)
                  ->where(function ($q) use ($startedMs) {
                      $q->where('stopped_at', '>', $startedMs)
                        ->orWhereNull('stopped_at');
                  });
        })->exists();

        if ($overlap) {
            return back()->with('error', 'Log time overlaps with an existing time log.');
        }

        $duration = floor(($stoppedMs - $startedMs) / 1000);

        $timer->logs()->create([
            'started_at' => $startedMs,
            'stopped_at' => $stoppedMs,
            'duration_seconds' => $duration,
        ]);

        // Recalculate parent timer's accumulated_seconds
        $totalDuration = $timer->logs()->whereNotNull('stopped_at')->sum('duration_seconds');
        $timer->update([
            'accumulated_seconds' => $totalDuration,
        ]);

        return back()->with('success', 'Manual log added successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'timerable_type' => 'nullable|string',
            'timerable_id' => 'nullable|integer',
        ]);
        
        $timer = Timer::create([
            'user_id' => 1,
            'purpose' => 'task_tracking',
            'is_running' => false,
            'accumulated_seconds' => 0,
            'timerable_type' => $request->timerable_type,
            'timerable_id' => $request->timerable_id,
        ]);

        $message = $request->timerable_type 
            ? 'Manual timer created and assigned successfully. You can now add logs.' 
            : 'Manual timer created successfully. You can now assign it and add logs.';

        return redirect()->route('timers.show', $timer)->with('success', $message);
    }
}
