<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Http\Requests\DestroyGameQuestionRequest;

class DestroyGameQuestionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DestroyGameQuestionRequest $request, Game $game, Question $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted successfully');
    }
}
