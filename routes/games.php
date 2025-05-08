<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EditGameController;
use App\Http\Controllers\JoinGameController;
use App\Http\Controllers\PlayGameController;
use App\Http\Controllers\EnterGameController;
use App\Http\Controllers\IndexGameController;
use App\Http\Controllers\StoreGameController;
use App\Http\Controllers\UpdateGameController;
use App\Http\Controllers\DestroyGameController;
use App\Http\Controllers\ControlPanelGameController;
use App\Http\Controllers\StoreGameQuestionController;
use App\Http\Controllers\UpdateGameQuestionController;
use App\Http\Controllers\DestroyGameQuestionController;

Route::middleware(['auth', 'verified'])->prefix('/games')->group(function () {
  Route::get('/', IndexGameController::class)->name('games.index');
  Route::post('/{show}/games', StoreGameController::class)->name('games.store');
  Route::get('/{game}/edit', EditGameController::class)->name('games.edit');
  Route::put('/{game}/update', UpdateGameController::class)->name('games.update');
  Route::post('/{game}/questions', StoreGameQuestionController::class)->name('games.questions.store');
  Route::put('/{game}/questions/{question}', UpdateGameQuestionController::class)->name('games.questions.update');
  Route::delete('/{game}/questions/{question}', DestroyGameQuestionController::class)->name('games.questions.destroy');

  Route::get('/{game}/control-panel', ControlPanelGameController::class)->name('games.control-panel');
  Route::delete('/{game}/destroy', DestroyGameController::class)->name('games.destroy');

  Route::get('/{game}/join', JoinGameController::class)->name('games.join')->withoutMiddleware(['auth', 'verified']);
  Route::post('/{game}/enter', EnterGameController::class)->name('games.enter')->withoutMiddleware(['auth', 'verified']);
  Route::get('/{game}/play', PlayGameController::class)->name('games.play')->withoutMiddleware(['auth', 'verified']);
});
