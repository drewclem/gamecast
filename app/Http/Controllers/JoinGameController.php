<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use App\Enums\GameStatus;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use Illuminate\Support\Facades\Session;

class JoinGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        // Check for existing session
        $sessionKey = 'watcher_' . $game->id;
        if (Session::has($sessionKey)) {
            return redirect()->route('games.play', $game->slug);
        }

        return Inertia::render('Games/JoinGame', [
            'game' => GameResource::make($game),
        ]);
    }
}
