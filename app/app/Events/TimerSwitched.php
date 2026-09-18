<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerSwitched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $timerId;
    public $initialState;

    public function __construct($userId, $timerId, $initialState)
    {
        $this->userId = $userId;
        $this->timerId = $timerId;
        $this->initialState = $initialState;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('users.' . $this->userId),
        ];
    }
}
