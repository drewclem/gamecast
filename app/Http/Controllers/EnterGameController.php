<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Watcher;
use App\Http\Requests\EnterGameRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EnterGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EnterGameRequest $request, Game $game)
    {
        $watcher = Watcher::firstOrCreate([
            'game_id' => $game->id,
            'gamertag' => $request->gamertag,
        ]);

        $watcher->update([
            'last_active_at' => now(),
        ]);

        Session::put('watcher_' . $game->id, $watcher->id);

        return redirect()->route('games.play', $game->slug);
    }
}
