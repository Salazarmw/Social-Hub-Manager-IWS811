<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TwitterService;
use App\Services\RedditService;
use App\Services\SocialServiceInterface;

class SocialServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar TwitterService
        $this->app->bind(TwitterService::class, function ($app) {
            return new TwitterService();
        });

        // Registrar RedditService
        $this->app->bind(RedditService::class, function ($app) {
            return new RedditService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
