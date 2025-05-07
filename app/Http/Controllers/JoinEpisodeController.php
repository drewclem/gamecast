<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinEpisodeRequest;
use App\Models\Episode;
use App\Models\Watcher;
use Illuminate\Http\RedirectResponse;

class JoinEpisodeController extends Controller
{
  public function __invoke(JoinEpisodeRequest $request, Episode $episode): RedirectResponse
  {
    if ($episode->access_password !== $request->access_password) {
      return back()->withErrors(['access_password' => 'Invalid access password.']);
    }

    $watcher = Watcher::create([
      'episode_id' => $episode->id,
      'email' => $request->email,
      'nickname' => $request->nickname,
    ]);

    session(['watcher_id' => $watcher->id]);

    return redirect()->route('episodes.show', $episode)
      ->with('success', 'Successfully joined the episode.');
  }
}
