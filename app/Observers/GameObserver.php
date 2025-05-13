<?php

namespace App\Observers;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\Log;
use App\Events\CurrentQuestionChanged;

class GameObserver
{
    /**
     * Handle the Game "updated" event.
     */
    public function updated(Game $game): void
    {
        if ($game->isDirty('current_question_id')) {
            $newQuestionId = $game->current_question_id;

            $question = $newQuestionId ? Question::find($newQuestionId) : null;

            broadcast(new CurrentQuestionChanged($game, $question));
        }
    }
}
