<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Show;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\IndexGameRequest;

class IndexGameController extends Controller
{
  public function __invoke(IndexGameRequest $request)
  {
    $user = $request->user();
    $show = $user->hosts()->first()?->show;

    return Inertia::render('Games/IndexGames', [
      'games' => $this->buildSearchQuery($request, $show)->get(),
      'show' => $show,
    ]);
  }

  private function buildSearchQuery($request, ?Show $show): QueryBuilder
  {
    return QueryBuilder::for(Game::class)
      ->when($show, function ($query, $show) {
        return $query->where('show_id', $show->id);
      })
      ->allowedFilters([
        AllowedFilter::scope('search', 'search'),
        AllowedFilter::exact('status'),
      ])
      ->defaultSort('-created_at');
  }
}
