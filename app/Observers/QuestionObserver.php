<?php

namespace App\Observers;

use App\Models\Question;
use App\Events\QuestionStatusChanged;
use App\Events\QuestionVotesUpdated;

class QuestionObserver
{
    /**
     * Handle the Question "updated" event.
     */
    public function updated(Question $question): void
    {

        if ($question->isDirty('status')) {
            $question->refresh();

            $question->load('votes');

            broadcast(new QuestionStatusChanged($question));

            if ($question->status === 'revealed') {
                broadcast(new QuestionVotesUpdated($question));
            }
        }
    }
}
