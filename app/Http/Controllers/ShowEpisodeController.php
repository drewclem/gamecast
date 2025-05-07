<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\View\View;

class ShowEpisodeController extends Controller
{
  public function __invoke(Game $game): View
  {
    $game->load(['questions', 'watchers']);

    $watcher = null;
    if (session()->has('watcher_id')) {
      $watcher = $game->watchers()->find(session('watcher_id'));
    }

    return view('games.show', compact('game', 'watcher'));
  }
}
