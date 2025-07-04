<?php

namespace App\Events;

use App\Models\Vote;
use App\Models\Question;
use App\Http\Resources\GameResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class QuestionVotesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Question $question, public readonly ?Vote $vote = null) {}

    public function broadcastOn()
    {
        return [new Channel('game.' . $this->question->game_id)];
    }

    public function broadcastWith()
    {
        $voteCounts = $this->question->getVoteCounts();
        $this->question->load(['game.questions.votes', 'game.watchers', 'game.activeQuestion.votes']);

        $data = [
            'game' => GameResource::make($this->question->game) ?? null,
            'question_id' => $this->question->id,
            'votes_by_host' => $voteCounts['byHost'],
            'total_votes' => $voteCounts['total']
        ];

        if ($this->vote) {
            $data['watcher_id'] = $this->vote->watcher_id;
            $data['host_id'] = $this->vote->host_id;
        }

        return $data;
    }
}
