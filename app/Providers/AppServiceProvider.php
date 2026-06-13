<?php

namespace App\Providers;

use App\Models\ChessGame;
use App\Models\Feedback;
use App\Models\FriendMessage;
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
        if (! ($this->app->environment('production') || env('RENDER'))) {
            return;
        }

        config([
            'session.driver' => 'file',
            'session.encrypt' => false,
            'session.domain' => null,
            'session.secure' => true,
            'session.same_site' => 'lax',
            'cache.default' => 'file',
        ]);
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
            config(['session.domain' => null]);
        }

        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                return;
            }

            try {
                $userId = Auth::id();
                $cacheKey = 'nav_notifications_'.$userId;

                $data = Cache::remember($cacheKey, 60, function () use ($userId) {
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

                    $chatUnreadCount = FriendMessage::query()
                        ->where('recipient_id', $userId)
                        ->whereNull('read_at')
                        ->count();

                    $payload = [
                        'friendRequestCount' => $incoming->count(),
                        'friendRequestNotifications' => $incoming->take(5),
                        'unreadFeedbackCount' => 0,
                        'feedbackNotifications' => collect(),
                        'chessInviteCount' => $chessInviteCount,
                        'chessInviteNotifications' => $chessInvites,
                        'chatUnreadCount' => $chatUnreadCount,
                        'notificationCount' => $incoming->count() + $chessInviteCount + $chatUnreadCount,
                    ];

                    if ($user->isOwner()) {
                        $feedback = Feedback::query()->unread()->latest()->take(5)->get();
                        $unreadFeedbackCount = Feedback::query()->unread()->count();

                        $payload['unreadFeedbackCount'] = $unreadFeedbackCount;
                        $payload['feedbackNotifications'] = $feedback;
                        $payload['notificationCount'] = $incoming->count() + $unreadFeedbackCount + $chessInviteCount + $chatUnreadCount;
                    }

                    return $payload;
                });

                $view->with($data);
            } catch (\Throwable) {
                $view->with([
                    'friendRequestCount' => 0,
                    'friendRequestNotifications' => collect(),
                    'unreadFeedbackCount' => 0,
                    'feedbackNotifications' => collect(),
                    'chessInviteCount' => 0,
                    'chessInviteNotifications' => collect(),
                    'chatUnreadCount' => 0,
                    'notificationCount' => 0,
                ]);
            }
        });
    }
}
