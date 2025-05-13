<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameQuestionRequest;

class StoreGameQuestionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreGameQuestionRequest $request, Game $game)
    {
        $highestOrderIndex = $game->questions()->max('order_index');

        $game->questions()->create([
            'question' => $request->input('question'),
            'order_index' => $highestOrderIndex + 1,
            'host_id' => $request->user()->currentHost->id,
        ]);

        return redirect()->back()->with('success', 'Question added successfully');
    }
}
