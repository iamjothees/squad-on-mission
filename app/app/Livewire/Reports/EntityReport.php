<?php

namespace App\Livewire\Reports;

use App\Models\TimerLog;
use Livewire\Component;

class EntityReport extends Component
{
    public $period = 'this_week';
    
    public function render()
    {
        $start = match($this->period) {
            'today' => now()->startOfDay(),
            'this_week' => now()->startOfWeek(),
            'last_week' => now()->subWeek()->startOfWeek(),
            'this_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            default => now()->startOfWeek(),
        };
        
        $end = match($this->period) {
            'today' => now()->endOfDay(),
            'this_week' => now()->endOfWeek(),
            'last_week' => now()->subWeek()->endOfWeek(),
            'this_month' => now()->endOfMonth(),
            'last_month' => now()->subMonth()->endOfMonth(),
            default => now()->endOfWeek(),
        };
        
        $prevStart = match($this->period) {
            'today' => now()->subDay()->startOfDay(),
            'this_week' => now()->subWeek()->startOfWeek(),
            'last_week' => now()->subWeeks(2)->startOfWeek(),
            'this_month' => now()->subMonth()->startOfMonth(),
            'last_month' => now()->subMonths(2)->startOfMonth(),
            default => now()->subWeek()->startOfWeek(),
        };
        
        $prevEnd = match($this->period) {
            'today' => now()->subDay()->endOfDay(),
            'this_week' => now()->subWeek()->endOfWeek(),
            'last_week' => now()->subWeeks(2)->endOfWeek(),
            'this_month' => now()->subMonth()->endOfMonth(),
            'last_month' => now()->subMonths(2)->endOfMonth(),
            default => now()->subWeek()->endOfWeek(),
        };

        $baseLogs = TimerLog::whereBetween('started_at', [$start->timestamp, $end->timestamp])->with(['timer.tasks', 'timer.projects', 'timer.clients'])->get();
        $prevLogs = TimerLog::whereBetween('started_at', [$prevStart->timestamp, $prevEnd->timestamp])->with(['timer.tasks', 'timer.projects', 'timer.clients'])->get();
        
        $topTasks = $this->aggregateEntities($baseLogs, $prevLogs, 'tasks');
        $topProjects = $this->aggregateEntities($baseLogs, $prevLogs, 'projects');
        $topClients = $this->aggregateEntities($baseLogs, $prevLogs, 'clients');

        return view('livewire.reports.entity-report', compact('topTasks', 'topProjects', 'topClients'));
    }
    
    private function aggregateEntities($baseLogs, $prevLogs, $relation)
    {
        $current = [];
        $previous = [];
        $entityNames = [];
        
        foreach ($baseLogs as $log) {
            if ($log->timer && $log->timer->$relation) {
                foreach ($log->timer->$relation as $entity) {
                    $id = $entity->id;
                    $current[$id] = ($current[$id] ?? 0) + $log->duration_seconds;
                    $entityNames[$id] = $entity->name ?? $entity->title;
                }
            }
        }
        
        foreach ($prevLogs as $log) {
            if ($log->timer && $log->timer->$relation) {
                foreach ($log->timer->$relation as $entity) {
                    $id = $entity->id;
                    $previous[$id] = ($previous[$id] ?? 0) + $log->duration_seconds;
                    if (!isset($entityNames[$id])) {
                        $entityNames[$id] = $entity->name ?? $entity->title;
                    }
                }
            }
        }
        
        $results = [];
        foreach ($current as $id => $seconds) {
            $prevSecs = $previous[$id] ?? 0;
            $results[] = [
                'name' => $entityNames[$id],
                'seconds' => $seconds,
                'prev_seconds' => $prevSecs,
                'delta_percent' => $prevSecs > 0 ? round((($seconds - $prevSecs) / $prevSecs) * 100) : 100
            ];
        }
        
        usort($results, fn($a, $b) => $b['seconds'] <=> $a['seconds']);
        return array_slice($results, 0, 5);
    }
}
