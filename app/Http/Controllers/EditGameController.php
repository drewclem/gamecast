<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use App\Http\Resources\GameResource;
use App\Http\Requests\EditGameRequest;

class EditGameController extends Controller
{
  public function __invoke(EditGameRequest $request, Game $game)
  {
    $game->load('questions.host.user');

    return Inertia::render('Games/EditGame', [
      'game' => GameResource::make($game),
    ]);
  }
}
