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

        if ($this->app->environment('production') && ! $this->app->runningInConsole()) {
            $host = request()->getHost();

            if (str_ends_with($host, 'themaniak.online')) {
                config(['session.domain' => '.themaniak.online']);
            } else {
                config(['session.domain' => null]);
            }
        }

        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $incoming = Auth::user()->pendingIncoming();

            $view->with([
                'friendRequestCount' => $incoming->count(),
                'friendRequestNotifications' => $incoming->take(5),
                'notificationCount' => $incoming->count(),
            ]);
        });
    }
}
