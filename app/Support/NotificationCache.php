<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class NotificationCache
{
    public static function clearForUser(int $userId): void
    {
        Cache::forget('nav_notifications_'.$userId);
    }
}
