<?php

namespace App\Support;

class TimeHelper
{
    /**
     * Format a duration in seconds to "Xd HH:MM:SS" or "HH:MM:SS".
     *
     * @param int $seconds
     * @return string
     */
    public static function formatDuration(int $seconds): string
    {
        $hoursPerDay = (float) config('squad.work_hours_per_day', 24);
        $secondsPerDay = $hoursPerDay * 3600;
        
        $days = floor($seconds / $secondsPerDay);
        $remainingSeconds = $seconds % $secondsPerDay;
        
        $hours = floor($remainingSeconds / 3600);
        $minutes = floor(($remainingSeconds % 3600) / 60);
        $secs = $remainingSeconds % 60;
        
        $timeString = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        
        if ($days > 0) {
            return $days . 'd ' . $timeString;
        }
        
        return $timeString;
    }
}
