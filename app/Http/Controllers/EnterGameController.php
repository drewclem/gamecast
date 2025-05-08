<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use App\Models\Watcher;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use Illuminate\Support\Facades\Session;

class EnterGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
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
