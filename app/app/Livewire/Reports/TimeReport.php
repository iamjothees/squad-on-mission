<?php

namespace App\Livewire\Reports;

use App\Models\Timer;
use Livewire\Component;
use Illuminate\Support\Carbon;

class TimeReport extends Component
{
    public $userId = 'all';
    public $basePeriod = 'this_week';
    public $comparePeriod = 'last_week';
    
    public function render()
    {
        $today = now();
        $yesterday = now()->subDay();
        $thisWeekStart = now()->startOfWeek();
        $thisWeekEnd = now()->endOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $todaySeconds = $this->getPeriodSeconds($today->startOfDay(), $today->endOfDay());
        $yesterdaySeconds = $this->getPeriodSeconds($yesterday->startOfDay(), $yesterday->endOfDay());
        
        $thisWeekSeconds = $this->getPeriodSeconds($thisWeekStart, $thisWeekEnd);
        $lastWeekSeconds = $this->getPeriodSeconds($lastWeekStart, $lastWeekEnd);
        
        $thisMonthSeconds = $this->getPeriodSeconds($thisMonthStart, $thisMonthEnd);
        $lastMonthSeconds = $this->getPeriodSeconds($lastMonthStart, $lastMonthEnd);

        $customBaseSeconds = $this->getCustomPeriodSeconds($this->basePeriod);
        $customCompareSeconds = $this->getCustomPeriodSeconds($this->comparePeriod);

        return view('livewire.reports.time-report', [
            'todaySeconds' => $todaySeconds,
            'yesterdaySeconds' => $yesterdaySeconds,
            'thisWeekSeconds' => $thisWeekSeconds,
            'lastWeekSeconds' => $lastWeekSeconds,
            'thisMonthSeconds' => $thisMonthSeconds,
            'lastMonthSeconds' => $lastMonthSeconds,
            'customBaseSeconds' => $customBaseSeconds,
            'customCompareSeconds' => $customCompareSeconds,
        ]);
    }

    private function getPeriodSeconds($start, $end)
    {
        $query = Timer::whereNotNull('completed_at')
            ->whereBetween('started_at', [$start, $end]);
            
        if ($this->userId !== 'all') {
            $query->where('user_id', $this->userId);
        }
        
        return $query->sum('duration_seconds');
    }

    private function getCustomPeriodSeconds($period)
    {
        switch ($period) {
            case 'today': return $this->getPeriodSeconds(now()->startOfDay(), now()->endOfDay());
            case 'yesterday': return $this->getPeriodSeconds(now()->subDay()->startOfDay(), now()->subDay()->endOfDay());
            case 'this_week': return $this->getPeriodSeconds(now()->startOfWeek(), now()->endOfWeek());
            case 'last_week': return $this->getPeriodSeconds(now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());
            case '2_weeks_ago': return $this->getPeriodSeconds(now()->subWeeks(2)->startOfWeek(), now()->subWeeks(2)->endOfWeek());
            case '3_weeks_ago': return $this->getPeriodSeconds(now()->subWeeks(3)->startOfWeek(), now()->subWeeks(3)->endOfWeek());
            case 'this_month': return $this->getPeriodSeconds(now()->startOfMonth(), now()->endOfMonth());
            case 'last_month': return $this->getPeriodSeconds(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
            default: return 0;
        }
    }
}
