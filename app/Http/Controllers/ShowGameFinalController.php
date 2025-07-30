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
        $game->load(['questions.votes', 'questions.host']);

        return Inertia::render('Games/GameResults', [
            'game' => GameResource::make($game),
            'votableHosts' => $game->votableHosts,
        ]);
    }
}
