<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use App\Models\Watcher;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use Illuminate\Support\Facades\Session;

class PlayGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Game $game)
    {
        $sessionKey = 'watcher_' . $game->id;

        if (!Session::has($sessionKey)) {
            return redirect()->route('games.join', $game->slug);
        }

        $watcher = Watcher::find(Session::get($sessionKey));

        if (!$watcher || $watcher->game_id !== $game->id) {
            Session::forget($sessionKey);
            return redirect()->route('games.join', $game->slug);
        }

        $watcher->update([
            'last_active_at' => now(),
        ]);

        // Only load what's needed for the initial render
        $game->load(['activeQuestion', 'watchers' => function ($query) {
            $query->where('last_active_at', '>=', now()->subMinutes(5));
        }]);

        // Get vote counts for the active question
        $voteCounts = [];
        if ($game->activeQuestion) {
            $voteCounts[$game->activeQuestion->id] = [
                'host1' => $game->activeQuestion->getVotesForHost1(),
                'host2' => $game->activeQuestion->getVotesForHost2(),
            ];
        }

        return Inertia::render('Games/PlayGame', [
            'game' => GameResource::make($game),
            'watcher' => $watcher,
            'activeQuestion' => $game->activeQuestion,
            'activeWatchers' => $game->watchers->count(),
            'voteCounts' => $voteCounts,
        ]);
    }
}
