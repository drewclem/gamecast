<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreGameRequest;

class StoreGameController extends Controller
{
  public function __invoke(StoreGameRequest $request, Show $show): RedirectResponse
  {
    $game = $show->games()->create([
      'title' => $request->input('title'),
      'slug' => Str::slug($request->input('title')),
      'description' => $request->input('description'),
      'access_password' => $request->input('access_password'),
    ]);

    return redirect()->route('games.edit', $game)
      ->with('success', 'Episode created successfully.');
  }
}
