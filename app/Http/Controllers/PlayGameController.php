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
        $game->load([
            'show.hosts',
            'activeQuestion.votes',
            'openQuestions.votes',
            'liveQuestions.votes',
            'openQuestions.host',
            'liveQuestions.host',
            'activeQuestion.host',
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

        $voteCounts = [];
        if ($game->activeQuestion) {
            $game->activeQuestion->refresh();

            $winners = $game->activeQuestion->getWinningHosts();
            $game->activeQuestion['winners'] = $winners;

            $voteCounts = $game->activeQuestion->getVoteCounts();
        } else {
            $voteCounts = [
                'byHost' => [],
                'total' => 0,
            ];
        }

        return Inertia::render('Games/PlayGame', [
            'hosts' => $game->votableHosts,
            'game' => GameResource::make($game),
            'watcher' => $watcher,
            'activeQuestion' => $game->activeQuestion,
            'activeWatchers' => $game->watchers->count(),
            'voteCounts' => $voteCounts,
        ]);
    }
}
