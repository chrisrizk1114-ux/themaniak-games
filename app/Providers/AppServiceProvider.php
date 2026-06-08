<?php

namespace App\Providers;

use App\Models\ChessGame;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

            $userId = Auth::id();
            $cacheKey = 'nav_notifications_'.$userId;

            $data = Cache::remember($cacheKey, 5, function () use ($userId) {
                $user = Auth::user();
                $incoming = $user->pendingIncoming();

                $chessInvites = ChessGame::query()
                    ->where('status', ChessGame::STATUS_PENDING)
                    ->where('black_user_id', $userId)
                    ->with('whitePlayer')
                    ->latest()
                    ->take(5)
                    ->get();

                $chessInviteCount = ChessGame::query()
                    ->where('status', ChessGame::STATUS_PENDING)
                    ->where('black_user_id', $userId)
                    ->count();

                $payload = [
                    'friendRequestCount' => $incoming->count(),
                    'friendRequestNotifications' => $incoming->take(5),
                    'unreadFeedbackCount' => 0,
                    'feedbackNotifications' => collect(),
                    'chessInviteCount' => $chessInviteCount,
                    'chessInviteNotifications' => $chessInvites,
                    'notificationCount' => $incoming->count() + $chessInviteCount,
                ];

                if ($user->isOwner()) {
                    $feedback = Feedback::query()->unread()->latest()->take(5)->get();
                    $unreadFeedbackCount = Feedback::query()->unread()->count();

                    $payload['unreadFeedbackCount'] = $unreadFeedbackCount;
                    $payload['feedbackNotifications'] = $feedback;
                    $payload['notificationCount'] = $incoming->count() + $unreadFeedbackCount + $chessInviteCount;
                }

                return $payload;
            });

            $view->with($data);
        });
    }
}
