<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreHostController;
use App\Http\Controllers\IndexHostsController;
use App\Http\Controllers\UpdateHostController;
use App\Http\Controllers\DestroyHostController;

Route::middleware(['auth', 'verified'])->prefix('/hosts')->group(function () {
  Route::get('/', IndexHostsController::class)->name('hosts.index');
  Route::post('/{show}', StoreHostController::class)->name('hosts.store');
  Route::post('/{show}/{host}', UpdateHostController::class)->name('hosts.update');
  Route::delete('/{host}', DestroyHostController::class)->name('hosts.destroy');
});
