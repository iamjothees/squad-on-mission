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
        $timers = Timer::where('user_id', auth()->id())->doesntHave('tasks')->doesntHave('projects')->doesntHave('clients')->latest('updated_at')
            ->get();
            
        $projects = Project::orderBy('name')->get();
        $tasks = Task::orderBy('title')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('timers.unassigned', compact('timers', 'projects', 'tasks', 'users'));
    }

    public function show(Timer $timer)
    {
        $timer->load(['logs' => function ($q) {
            $q->orderBy('started_at', 'desc');
        }, 'tasks', 'projects', 'clients']);

        $projects = Project::orderBy('name')->get();
        $tasks = Task::orderBy('title')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('timers.show', compact('timer', 'projects', 'tasks'));
    }
    
    public function assign(Request $request, Timer $timer)
    {
        $request->validate([
            'timerables' => 'nullable|array',
            'timerables.*' => 'string',
        ]);
        
        // Detach all existing to cleanly sync the array
        $timer->tasks()->detach();
        $timer->projects()->detach();
        $timer->clients()->detach();

        if (empty($request->timerables)) {
            return back()->with('success', 'Timer assignments cleared.');
        }

        foreach ($request->timerables as $timerable) {
            $parts = explode(':', $timerable);
            if (count($parts) === 2) {
                $type = $parts[0] === 'project' ? Project::class : Task::class;
                $id = $parts[1];
                
                try {
                    $timer->validateHierarchyAttachments($type, $id);
                    if ($type === \App\Models\Task::class) {
                        $timer->tasks()->syncWithoutDetaching([$id]);
                    } elseif ($type === \App\Models\Project::class) {
                        $timer->projects()->syncWithoutDetaching([$id]);
                    } elseif ($type === \App\Models\Client::class) {
                        $timer->clients()->syncWithoutDetaching([$id]);
                    }
                    $timer->update(['purpose' => 'task_tracking']);
                } catch (\Exception $e) {
                    return back()->with('error', $e->getMessage());
                }
            }
        }
        
        return back()->with('success', 'Timer successfully assigned!');
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
                
                try {
                    $timer->validateHierarchyAttachments($type, $id);
                    if ($type === \App\Models\Task::class) {
                        $timer->tasks()->syncWithoutDetaching([$id]);
                    } elseif ($type === \App\Models\Project::class) {
                        $timer->projects()->syncWithoutDetaching([$id]);
                    } elseif ($type === \App\Models\Client::class) {
                        $timer->clients()->syncWithoutDetaching([$id]);
                    }
                    $timer->update(['purpose' => 'task_tracking']);
                    $count++;
                } catch (\Exception $e) {
                    continue; // Skip invalid bulk assignments
                }
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

        $wasRunningLog = is_null($log->stopped_at);

        $duration = $stoppedMs ? floor(($stoppedMs - $startedMs) / 1000) : 0;

        $log->update([
            'started_at' => $startedMs,
            'stopped_at' => $stoppedMs,
            'duration_seconds' => $duration,
        ]);

        // Recalculate parent timer's accumulated_seconds
        $totalDuration = $timer->logs()->whereNotNull('stopped_at')->sum('duration_seconds');
        
        $timerUpdates = [
            'accumulated_seconds' => $totalDuration,
        ];

        // If modifying the currently active log
        if ($wasRunningLog && $timer->is_running) {
            if ($stoppedMs) {
                // User manually stopped the active log
                $timerUpdates['is_running'] = false;
                $timerUpdates['last_started_at'] = null;
            } else {
                // User just shifted the start time
                $timerUpdates['last_started_at'] = $startedMs;
            }
        } else if (!$wasRunningLog && !$stoppedMs) {
            // User resumed a past log by removing stopped_at
            if (!$timer->is_running) {
                $timerUpdates['is_running'] = true;
                $timerUpdates['last_started_at'] = $startedMs;
            }
        }

        $timer->update($timerUpdates);
        
        // Broadcast to sync all clients (Web and Macropad)
        broadcast(new \App\Events\TimerUpdated($timer));

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

    public function destroy(Timer $timer)
    {
        // First stop the timer if it's running so we broadcast an update
        if ($timer->is_running) {
            $timer->update(['is_running' => false, 'last_started_at' => null]);
            broadcast(new \App\Events\TimerUpdated($timer));
        }
        
        $timer->delete();
        
        return redirect()->route('dashboard')->with('success', 'Timer deleted successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'timerable_type' => 'nullable|string',
            'timerable_id' => 'nullable|integer',
        ]);
        
        $targetUserId = $request->user_id ?? auth()->id();
        $targetUser = \App\Models\User::find($targetUserId);
        
        if ($targetUser && strtolower($targetUser->name) === 'system') {
            return back()->with('error', 'The System user cannot own timers.');
        }
        
        $timer = Timer::create([
            'user_id' => $request->user_id ?? auth()->id(),
            'purpose' => 'task_tracking',
            'is_running' => false,
            'accumulated_seconds' => 0,

        ]);

        if ($request->timerable_type && $request->timerable_id) {
            try {
                $timer->validateHierarchyAttachments($request->timerable_type, $request->timerable_id);
                if ($request->timerable_type === \App\Models\Task::class) {
                    $timer->tasks()->attach($request->timerable_id);
                } elseif ($request->timerable_type === \App\Models\Project::class) {
                    $timer->projects()->attach($request->timerable_id);
                } elseif ($request->timerable_type === \App\Models\Client::class) {
                    $timer->clients()->attach($request->timerable_id);
                }
            } catch (\Exception $e) {
                // If it fails, delete the timer we just created and return error
                $timer->delete();
                return back()->with('error', $e->getMessage());
            }
        }

        $message = $request->timerable_type 
            ? 'Manual timer created and assigned successfully. You can now add logs.' 
            : 'Manual timer created successfully. You can now assign it and add logs.';

        return redirect()->route('timers.show', $timer)->with('success', $message);
    }
}
