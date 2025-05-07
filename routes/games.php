<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EditGameController;
use App\Http\Controllers\IndexGameController;
use App\Http\Controllers\StoreGameController;
use App\Http\Controllers\StoreGameQuestionController;
use App\Http\Controllers\UpdateGameQuestionController;
use App\Http\Controllers\DestroyGameQuestionController;

Route::middleware(['auth', 'verified'])->prefix('/games')->group(function () {
  Route::get('/', IndexGameController::class)->name('games.index');
  Route::post('/{show}/games', StoreGameController::class)->name('games.store');
  Route::get('/{game}/edit', EditGameController::class)->name('games.edit');
  Route::post('/{game}/questions', StoreGameQuestionController::class)->name('games.questions.store');
  Route::put('/{game}/questions/{question}', UpdateGameQuestionController::class)->name('games.questions.update');
  Route::delete('/{game}/questions/{question}', DestroyGameQuestionController::class)->name('games.questions.destroy');
});
