<?php

namespace App\Http\Controllers;

use App\Models\Timer;

class TimerViewController extends Controller
{
    public function show(Timer $timer)
    {
        $timer->load(['logs' => function ($q) {
            $q->orderBy('started_at', 'desc');
        }, 'timerable']);

        return view('timers.show', compact('timer'));
    }
}
