<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\Question;
use App\Observers\GameObserver;
use App\Observers\QuestionObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Game::observe(GameObserver::class);
        Question::observe(QuestionObserver::class);
    }
}
