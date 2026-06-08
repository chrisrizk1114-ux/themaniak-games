<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if ($request->user()) {
            $user = $request->user();
            $lastSeen = $user->last_seen_at;

            if (! $lastSeen || $lastSeen->diffInSeconds(now()) >= 15) {
                $user->forceFill([
                    'last_seen_at' => now(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        if (! $request->user()) {
            return true;
        }

        if ($request->is('up', 'health', 'presence/*', 'chess/games/pending', 'chess/games/invites/check', 'chat/poll')) {
            return true;
        }

        if ($request->is('chess/games/*/sync')) {
            return true;
        }

        return $request->expectsJson() && $request->is('chess/games/*', 'owner/feedback/check');
    }
}
