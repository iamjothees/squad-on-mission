<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

    protected function startedAtCarbon(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->started_at ? Carbon::createFromTimestampMs($this->started_at)->timezone(config('app.timezone')) : null,
        );
    }

    protected function stoppedAtCarbon(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stopped_at ? Carbon::createFromTimestampMs($this->stopped_at)->timezone(config('app.timezone')) : null,
        );
    }
}
