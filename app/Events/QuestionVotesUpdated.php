<?php

namespace App\Events;

use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionVotesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Question $question) {}

    public function broadcastOn()
    {
        return [new Channel('game.' . $this->question->game_id)];
    }

    public function broadcastWith()
    {
        $voteCounts = $this->question->getVoteCounts();

        return [
            'question_id' => $this->question->id,
            'votes_by_host' => $voteCounts['byHost'],
            'total_votes' => $voteCounts['total']
        ];
    }
}
