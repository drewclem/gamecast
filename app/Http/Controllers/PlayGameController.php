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
        // Only load what's needed for the initial render
        $game->load([
            'show.hosts',
            'activeQuestion',
            'watchers' => function ($query) {
                $query->where('last_active_at', '>=', now()->subMinutes(5));
            }
        ]);

        $sessionKey = 'watcher_' . $game->id;

        if (!Session::has($sessionKey)) {
            return redirect()->route('games.join', $game->slug);
        }

        $watcher = Watcher::find(Session::get($sessionKey));
        $watcher->loadMissing('votes');

        if (!$watcher || $watcher->game_id !== $game->id) {
            Session::forget($sessionKey);
            return redirect()->route('games.join', $game->slug);
        }

        $watcher->update([
            'last_active_at' => now(),
        ]);


        // Get vote counts for the active question
        $voteCounts = [];
        if ($game->activeQuestion) {
            $winners = $game->activeQuestion->getWinningHosts();

            $game->activeQuestion['winners'] = $winners;
            $voteCounts[$game->activeQuestion->id] = $game->activeQuestion->getVoteCounts();
        }

        return Inertia::render('Games/PlayGame', [
            'hosts' => $game->show->hosts,
            'game' => GameResource::make($game),
            'watcher' => $watcher,
            'activeQuestion' => $game->activeQuestion,
            'activeWatchers' => $game->watchers->count(),
            'voteCounts' => $voteCounts,
        ]);
    }
}
