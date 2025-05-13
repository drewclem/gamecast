<?php

namespace App\Events;

use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Question $question) {}

    public function broadcastOn()
    {
        return [new Channel('game.' . $this->question->game_id)];
    }

    public function broadcastWith()
    {
        $this->question->refresh();

        $this->question->load('votes');

        $voteData = $this->question->getVoteCounts();
        $winners = $this->question->getWinningHosts();

        return [
            'id' => $this->question->id,
            'game_id' => $this->question->game_id,
            'status' => $this->question->status,
            'vote_counts' => $voteData,
            'winners' => $winners,
        ];
    }
}
