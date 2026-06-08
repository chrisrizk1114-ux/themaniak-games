<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PresenceController extends Controller
{
    public function ping(): JsonResponse
    {
        $user = auth()->user();

        if (! $user->last_seen_at || $user->last_seen_at->diffInSeconds(now()) >= 45) {
            $user->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        return response()->json([
            'online' => true,
            'last_seen_at' => $user->last_seen_at?->toIso8601String(),
        ]);
    }

    public function friends(): JsonResponse
    {
        $user = auth()->user();

        $friends = Cache::remember('friends_presence_'.$user->id, 3, function () use ($user) {
            return $user->friends()->map(fn ($friend) => [
                'id' => $friend->id,
                'name' => $friend->name,
                'online' => $friend->isOnline(2),
            ])->values();
        });

        return response()->json(['friends' => $friends]);
    }
}
