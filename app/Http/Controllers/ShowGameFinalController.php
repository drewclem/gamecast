<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;

class ShowGameFinalController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        $game->load(['questions.votes', 'questions.host', 'votes', 'votableHost1.votes', 'votableHost2.votes']);

        $hosts = $game->votableHosts;

        return Inertia::render('Games/GameResults', [
            'game' => GameResource::make($game),
            'votableHosts' => $hosts,
        ]);
    }
}
