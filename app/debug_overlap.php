<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$timer = \App\Models\Timer::find(1);

// We simulate what the controller receives when the user edits log 2
$startedMs = strtotime('2026-09-30 22:30:00') * 1000;
$stoppedMs = strtotime('2026-10-01 02:30:00') * 1000;

$overlap = $timer->logs()->where('id', '!=', 2)
    ->where(function ($query) use ($startedMs, $stoppedMs) {
        if ($stoppedMs) {
            $query->where('started_at', '<', $stoppedMs)
                  ->where(function ($q) use ($startedMs) {
                      $q->where('stopped_at', '>', $startedMs)
                        ->orWhereNull('stopped_at');
                  });
        } else {
            $query->where('stopped_at', '>', $startedMs)
                  ->orWhereNull('stopped_at');
        }
    })->exists();

var_dump($overlap);
$overlappingLogs = $timer->logs()->where('id', '!=', 2)
    ->where(function ($query) use ($startedMs, $stoppedMs) {
        if ($stoppedMs) {
            $query->where('started_at', '<', $stoppedMs)
                  ->where(function ($q) use ($startedMs) {
                      $q->where('stopped_at', '>', $startedMs)
                        ->orWhereNull('stopped_at');
                  });
        } else {
            $query->where('stopped_at', '>', $startedMs)
                  ->orWhereNull('stopped_at');
        }
    })->get();

foreach ($overlappingLogs as $l) {
    echo "Overlaps with Log ID {$l->id} (Start: {$l->started_at}, Stop: {$l->stopped_at})\n";
}
