<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteDailyStat extends Model
{
    protected $fillable = [
        'date',
        'page_views',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'page_views' => 'integer',
        ];
    }

    public static function recordPageView(): void
    {
        $stat = static::query()->firstOrCreate(
            ['date' => today()->toDateString()],
            ['page_views' => 0]
        );

        $stat->increment('page_views');
    }

    public static function viewsForDate(\DateTimeInterface|string $date): int
    {
        return (int) static::query()
            ->whereDate('date', $date)
            ->value('page_views');
    }

    public static function totalViews(): int
    {
        return (int) static::query()->sum('page_views');
    }
}
