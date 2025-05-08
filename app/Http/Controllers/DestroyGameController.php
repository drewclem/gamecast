<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\DestroyGameRequest;

class DestroyGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DestroyGameRequest $request, Game $game)
    {
        $game->delete();

        return redirect()->route('games.index')->with('success', 'Game deleted successfully');
    }
}
