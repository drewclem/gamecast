<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;

class ShareGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        $voteCounts = [];
        if ($game->activeQuestion) {
            $winners = $game->activeQuestion->getWinningHosts();
            $game->activeQuestion->load('host');

            $game->activeQuestion['winners'] = $winners;
            $voteCounts = $game->activeQuestion->getVoteCounts();
        }

        return Inertia::render('Games/ShareGame', [
            'game' => GameResource::make($game),
            'hosts' => $game->votableHosts,
            'activeQuestion' => $game->activeQuestion,
            'activeWatchers' => $game->watchers->count(),
            'voteCounts' => $voteCounts,
        ]);
    }
}
