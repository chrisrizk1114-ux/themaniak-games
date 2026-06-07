<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $incoming = Auth::user()->pendingIncoming();

            $view->with([
                'friendRequestCount' => $incoming->count(),
                'friendRequestNotifications' => $incoming->take(5),
            ]);
        });
    }
}
