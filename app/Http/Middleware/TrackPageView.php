<?php

namespace App\Http\Middleware;

use App\Models\SiteDailyStat;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            try {
                SiteDailyStat::recordPageView();
            } catch (\Throwable) {
                // Never break the site if stats fail.
            }
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($response->getStatusCode() >= 400 || $response->isRedirection()) {
            return false;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return false;
        }

        if ($request->is(
            'up',
            'db-ping',
            'reset-session',
            'health',
            'leaderboard/*',
            'chat/poll',
            'chat/messages/*',
            'presence/ping',
            'presence/friends',
            'chess/games/*/sync',
            'chess/games/pending',
            'chess/games/invites/check',
            'owner/feedback/check',
        )) {
            return false;
        }

        if (str_contains($request->path(), '.')) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html') || $contentType === '';
    }
}
