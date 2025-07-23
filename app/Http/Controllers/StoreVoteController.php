<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Events\VoteCast;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Events\QuestionVotesUpdated;
use App\Http\Requests\StoreVoteRequest;
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
            broadcast(new VoteCast($question));

            return redirect()->back()->with('success', 'Vote cast successfully');
        });
    }
}
