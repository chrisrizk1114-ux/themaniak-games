<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
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
}
