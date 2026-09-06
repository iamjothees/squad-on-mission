<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'timer_id',
        'started_at',
        'stopped_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at' => 'integer',
        'stopped_at' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function timer()
    {
        return $this->belongsTo(Timer::class);
    }
}
