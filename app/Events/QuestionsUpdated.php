<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionsUpdated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(public readonly Game $game) {}

  public function broadcastOn()
  {
    return [new Channel('game.' . $this->game->id)];
  }

  public function broadcastWith()
  {
    $this->game->load('questions');
    return [
      'questions' => $this->game->questions->map(function ($question) {
        return [
          'id' => $question->id,
          'question' => $question->question,
          'status' => $question->status,
          'is_active' => $question->is_active,
          'type' => $question->getType(),
        ];
      }),
    ];
  }
}
