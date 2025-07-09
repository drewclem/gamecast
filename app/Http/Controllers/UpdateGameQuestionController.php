<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Enums\QuestionStatus;
use App\Http\Requests\UpdateGameQuestionRequest;

class UpdateGameQuestionController extends Controller
{
  public function __invoke(UpdateGameQuestionRequest $request, Game $game, Question $question)
  {
    $question->update($request->validated());

    if ($request->input('status') === QuestionStatus::REVEALED) {
      $question->load(['votes', 'game.votableHosts']);
    }

    return back()->with('success', 'Question updated successfully');
  }
}
