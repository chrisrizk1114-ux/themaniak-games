<?php

namespace App\Providers;

use App\Models\Feedback;
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

            $data = [
                'friendRequestCount' => $incoming->count(),
                'friendRequestNotifications' => $incoming->take(5),
                'unreadFeedbackCount' => 0,
                'feedbackNotifications' => collect(),
                'notificationCount' => $incoming->count(),
            ];

            if (Auth::user()->isOwner()) {
                $feedback = Feedback::query()->unread()->latest()->take(5)->get();
                $unreadFeedbackCount = Feedback::query()->unread()->count();

                $data['unreadFeedbackCount'] = $unreadFeedbackCount;
                $data['feedbackNotifications'] = $feedback;
                $data['notificationCount'] = $incoming->count() + $unreadFeedbackCount;
            }

            $view->with($data);
        });
    }
}
