<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Http\Requests\UpdateGameQuestionRequest;

class UpdateGameQuestionController extends Controller
{
  public function __invoke(UpdateGameQuestionRequest $request, Game $game, Question $question)
  {
    $question->update($request->validated());

    return back()->with('success', 'Question updated successfully');
  }
}
