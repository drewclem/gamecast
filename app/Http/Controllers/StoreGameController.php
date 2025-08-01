<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreGameRequest;

class StoreGameController extends Controller
{
  public function __invoke(StoreGameRequest $request, Show $show): RedirectResponse
  {
    $game = DB::transaction(function () use ($request, $show) {
      $game = $show->games()->create([
        'title' => $request->input('title'),
        'slug' => Str::slug($request->input('title')),
        'description' => $request->input('description'),
        'access_code' => $request->input('access_code'),
      ]);

      return $game;
    });

    return redirect()->route('games.edit', $game)
      ->with('success', 'Episode created successfully.');
  }
}
