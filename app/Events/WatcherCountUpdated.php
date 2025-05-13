<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WatcherCountUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Game $game, public readonly int $count) {}

    public function broadcastOn()
    {
        return [new Channel('game.' . $this->game->id)];
    }

    public function broadcastWith()
    {
        return [
            'game_id' => $this->game->id,
            'watcher_count' => $this->count,
        ];
    }
}
