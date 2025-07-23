<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Question;

class VoteCast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Question $question) {}

    public function broadcastOn()
    {
        return [new Channel('game.' . $this->question->game_id)];
    }

    public function broadcastWith()
    {
        $this->question->load(['votes', 'host', 'votesByHost']);
        return [
            'question_id' => $this->question->id,
            'votes' => $this->question->votes,
            'votesByHost' => $this->question->votesByHost(),
        ];
    }
}
