<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Enums\QuestionStatus;
use Illuminate\Http\RedirectResponse;

class ResetQuestionVotesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Question $question): RedirectResponse
    {
        $question->votes()->delete();
        $question->update(['status' => QuestionStatus::PENDING]);

        return redirect()->back()->with('success', 'Votes reset successfully');
    }
}
