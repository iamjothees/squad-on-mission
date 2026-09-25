<?php

namespace App\Livewire\Reports;

use App\Models\Timer;
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

        $baseTimers = Timer::whereNotNull('completed_at')->whereBetween('started_at', [$start, $end])->with(['task', 'project', 'client'])->get();
        $prevTimers = Timer::whereNotNull('completed_at')->whereBetween('started_at', [$prevStart, $prevEnd])->with(['task', 'project', 'client'])->get();
        
        $topTasks = $this->aggregateEntities($baseTimers, $prevTimers, 'task');
        $topProjects = $this->aggregateEntities($baseTimers, $prevTimers, 'project');
        $topClients = $this->aggregateEntities($baseTimers, $prevTimers, 'client');

        return view('livewire.reports.entity-report', compact('topTasks', 'topProjects', 'topClients'));
    }
    
    private function aggregateEntities($baseTimers, $prevTimers, $relation)
    {
        $current = [];
        $previous = [];
        
        foreach ($baseTimers as $t) {
            if ($t->$relation) {
                $id = $t->$relation->id;
                $current[$id] = ($current[$id] ?? 0) + $t->duration_seconds;
            }
        }
        
        foreach ($prevTimers as $t) {
            if ($t->$relation) {
                $id = $t->$relation->id;
                $previous[$id] = ($previous[$id] ?? 0) + $t->duration_seconds;
            }
        }
        
        $results = [];
        foreach ($current as $id => $seconds) {
            $prevSecs = $previous[$id] ?? 0;
            $entity = $baseTimers->firstWhere($relation . '.id', $id)->$relation;
            $results[] = [
                'name' => $entity->name ?? $entity->title,
                'seconds' => $seconds,
                'prev_seconds' => $prevSecs,
                'delta_percent' => $prevSecs > 0 ? round((($seconds - $prevSecs) / $prevSecs) * 100) : 100
            ];
        }
        
        usort($results, fn($a, $b) => $b['seconds'] <=> $a['seconds']);
        return array_slice($results, 0, 5);
    }
}
