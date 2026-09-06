<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'timerable_id',
        'timerable_type',
        'purpose',
        'accumulated_seconds',
        'is_running',
        'last_started_at',
    ];

    protected $casts = [
        'is_running' => 'boolean',
        'accumulated_seconds' => 'integer',
        'last_started_at' => 'integer',
    ];

    public function timerable(): MorphTo
    {
        return $this->morphTo();
    }
}
