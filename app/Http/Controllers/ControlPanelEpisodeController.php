<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\View\View;

class ControlPanelEpisodeController extends Controller
{
  public function __invoke(Episode $episode): View
  {
    $episode->load(['questions', 'watchers']);

    return view('episodes.control-panel', compact('episode'));
  }
}
