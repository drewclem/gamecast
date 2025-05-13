<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CurrentQuestionChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Game $game, public readonly ?Question $question) {}

    public function broadcastOn()
    {
        return [new Channel('game.' . $this->game->id)];
    }

    public function broadcastWith()
    {
        return [
            'game_id' => $this->game->id,
            'question' => $this->question ? $this->question->toBroadcast() : null,
        ];
    }
}
