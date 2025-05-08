<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ControlPanelGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        $game->load(['questions.votes', 'watchers', 'activeQuestion']);

        return Inertia::render('Games/ControlPanel', [
            'game' => $game,
            'activeQuestion' => $game->activeQuestion,
        ]);
    }
}
