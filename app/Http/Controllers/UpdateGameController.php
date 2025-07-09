<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Enums\QuestionStatus;
use App\Http\Requests\UpdateGameRequest;

class UpdateGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateGameRequest $request, Game $game)
    {
        $validated = $request->validated();

        // Check if title has changed
        if (isset($validated['title']) && $validated['title'] !== $game->title) {
            $game->regenerateQrCode();
        }

        $oldQuestionId = $game->current_question_id;
        $newQuestionId = $validated['current_question_id'] ?? null;

        if ($oldQuestionId && $oldQuestionId !== $newQuestionId) {
            $oldQuestion = Question::find($oldQuestionId);
            if ($oldQuestion && $oldQuestion->status !== QuestionStatus::REVEALED) {
                $oldQuestion->update(['status' => 'closed']);
            }
        }

        if ($newQuestionId && $oldQuestionId !== $newQuestionId) {
            $newQuestion = Question::find($newQuestionId);
            if ($newQuestion) {
                $newQuestion->update(['status' => 'active']);
            }
        }

        if (isset($validated['votable_host_1_id']) && $validated['votable_host_1_id'] !== $game->votable_host_1_id) {
            $game->update(['votable_host_1_id' => $validated['votable_host_1_id']]);
        }

        if (isset($validated['votable_host_2_id']) && $validated['votable_host_2_id'] !== $game->votable_host_2_id) {
            $game->update(['votable_host_2_id' => $validated['votable_host_2_id']]);
        }

        $game->update($validated);

        return redirect()->back()->with('success', 'Game updated successfully');
    }
}
