<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameQuestionRequest;
use App\Events\QuestionsUpdated;

class StoreGameQuestionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreGameQuestionRequest $request, Game $game)
    {
        $highestOrderIndex = $game->questions()->max('order_index');

        $game->questions()->create([
            'question' => $request->validated('question'),
            'order_index' => $highestOrderIndex + 1,
            'host_id' => auth()->user()->currentHost->id,
        ]);

        broadcast(new QuestionsUpdated($game));

        return redirect()->back()->with('success', 'Question added successfully');
    }
}
