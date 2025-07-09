<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;

class ControlPanelGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        $game->load(['questions.votes', 'watchers', 'activeQuestion']);

        $voteCounts = [];
        if ($game->activeQuestion) {
            $winners = $game->activeQuestion->getWinningHosts();

            $game->activeQuestion['winners'] = $winners;
            $voteCounts = $game->activeQuestion->getVoteCounts();
        }

        return Inertia::render('Games/ControlPanel', [
            'hosts' => $game->votableHosts,
            'game' => GameResource::make($game),
            'activeQuestion' => $game->activeQuestion,
            'activeWatchers' => $game->watchers->count(),
            'voteCounts' => $voteCounts,
        ]);
    }
}
