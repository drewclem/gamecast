<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Enums\QuestionStatus;
use App\Http\Requests\UpdateGameQuestionRequest;
use App\Events\QuestionsUpdated;

class UpdateGameQuestionController extends Controller
{
  public function __invoke(UpdateGameQuestionRequest $request, Game $game, Question $question)
  {
    $question->update($request->validated());

    if ($request->validated('status') === QuestionStatus::REVEALED) {
      $question->load(['votes', 'game.votableHosts']);
    }

    broadcast(new QuestionsUpdated($game));

    return back()->with('success', 'Question updated successfully');
  }
}
