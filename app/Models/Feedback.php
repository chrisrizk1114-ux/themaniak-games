<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'category',
        'game',
        'subject',
        'message',
        'special_details',
        'player_info',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'player_info' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'bug' => 'Bug report',
            'game_issue' => 'Game issue',
            'suggestion' => 'Suggestion',
            'compliment' => 'Compliment',
            'other' => 'Other',
            default => ucfirst((string) $this->category),
        };
    }

    public function gameLabel(): ?string
    {
        if (! $this->game) {
            return null;
        }

        return match ($this->game) {
            'general' => 'General / Site',
            'galaxy-bowling' => 'Galaxy Bowling',
            'platformer' => 'Sky Runner',
            'tic-tac-toe' => 'Neon Grid',
            'board-game' => 'Snakes & Ladders',
            'chess' => 'Royal Chess',
            'whack-a-mole' => 'Mole Mayhem',
            'uno' => 'Cosmic UNO',
            default => $this->game,
        };
    }
}
