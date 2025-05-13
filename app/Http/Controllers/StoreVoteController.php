<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Events\QuestionVotesUpdated;
use App\Http\Requests\StoreVoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StoreVoteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreVoteRequest $request, Game $game, Question $question)
    {
        DB::transaction(function () use ($request, $game, $question) {
            $watcherId = Session::get('watcher_' . $game->id);

            $question->votes()->create([
                'watcher_id' => $watcherId,
                'host_id' => $request->validated('host_id'),
                'game_id' => $game->id,
            ]);

            $question->getVoteCounts();

            broadcast(new QuestionVotesUpdated($question));

            return redirect()->back()->with('success', 'Vote cast successfully');
        });
    }
}
