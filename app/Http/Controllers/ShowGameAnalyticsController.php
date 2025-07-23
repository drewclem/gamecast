<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;

class ShowGameAnalyticsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        $voteCounts = [];
        $game->load(['questions.votes', 'questions.host', 'watchers', 'activeQuestion']);

        $questions = $game->questions()
            ->with(['votes', 'host', 'game'])
            ->orderByRaw('RANDOM()')
            ->limit(5)
            ->get();

        $game->setRelation('questions', $questions);

        if ($game->activeQuestion) {
            $winners = $game->activeQuestion->getWinningHosts();
            $game->activeQuestion->load('host');

            $game->activeQuestion['winners'] = $winners;
            $voteCounts = $game->activeQuestion->getVoteCounts();
        }

        return Inertia::render('Games/GameAnalytics', [
            'game' => GameResource::make($game),
            'hosts' => $game->votableHosts,
            'activeQuestion' => $game->activeQuestion,
            'activeWatchers' => $game->watchers->count(),
            'voteCounts' => $voteCounts,
        ]);
    }
}
