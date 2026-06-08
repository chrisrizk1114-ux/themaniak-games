<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChessGameMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chess_game_id',
        'user_id',
        'body',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(ChessGame::class, 'chess_game_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
