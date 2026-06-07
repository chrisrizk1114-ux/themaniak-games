<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class PresenceController extends Controller
{
    public function ping(): JsonResponse
    {
        $user = auth()->user();
        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        return response()->json([
            'online' => true,
            'last_seen_at' => $user->last_seen_at?->toIso8601String(),
        ]);
    }
}
