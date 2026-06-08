<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChessGame extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'token',
        'room_code',
        'white_user_id',
        'black_user_id',
        'invited_by_user_id',
        'status',
        'state',
        'version',
        'game_over',
        'winner',
        'game_over_title',
        'game_over_msg',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'array',
            'game_over' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ChessGame $game) {
            if (! $game->token) {
                $game->token = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function whitePlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'white_user_id');
    }

    public function blackPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'black_user_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChessGameMessage::class);
    }

    public function involvesUser(User $user): bool
    {
        if ($this->white_user_id === $user->id) {
            return true;
        }

        return $this->black_user_id !== null && $this->black_user_id === $user->id;
    }

    public function isOpenRoom(): bool
    {
        return $this->room_code !== null
            && $this->status === self::STATUS_PENDING
            && $this->black_user_id === null;
    }

    public static function generateRoomCode(): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (self::query()->where('room_code', $code)->where('status', self::STATUS_PENDING)->exists());

        return $code;
    }

    public function colorForUser(User $user): ?string
    {
        if ($this->white_user_id === $user->id) {
            return 'white';
        }
        if ($this->black_user_id === $user->id) {
            return 'black';
        }

        return null;
    }

    public function opponentFor(User $user): ?User
    {
        if ($this->white_user_id === $user->id) {
            return $this->blackPlayer;
        }
        if ($this->black_user_id === $user->id) {
            return $this->whitePlayer;
        }

        return null;
    }

    public static function initialState(): array
    {
        return [
            'board' => [
                ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'],
                ['p', 'p', 'p', 'p', 'p', 'p', 'p', 'p'],
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P'],
                ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R'],
            ],
            'currentPlayer' => 'white',
            'castlingRights' => [
                'white' => ['kingSide' => true, 'queenSide' => true],
                'black' => ['kingSide' => true, 'queenSide' => true],
            ],
            'enPassantTarget' => null,
            'capturedByWhite' => [],
            'capturedByBlack' => [],
            'lastMove' => null,
            'whiteTime' => 600,
            'blackTime' => 600,
            'gameOver' => false,
            'statusText' => "White's turn",
        ];
    }

    public function toSyncPayload(User $viewer): array
    {
        $opponent = $this->opponentFor($viewer);

        return [
            'token' => $this->token,
            'room_code' => $this->room_code,
            'status' => $this->status,
            'version' => $this->version,
            'state' => $this->state,
            'game_over' => $this->game_over,
            'winner' => $this->winner,
            'game_over_title' => $this->game_over_title,
            'game_over_msg' => $this->game_over_msg,
            'my_color' => $this->colorForUser($viewer),
            'white' => [
                'id' => $this->white_user_id,
                'name' => $this->whitePlayer->name,
                'online' => $this->whitePlayer->isOnline(),
            ],
            'black' => $this->black_user_id ? [
                'id' => $this->black_user_id,
                'name' => $this->blackPlayer->name,
                'online' => $this->blackPlayer->isOnline(),
            ] : [
                'id' => null,
                'name' => 'Waiting for player…',
                'online' => false,
            ],
            'opponent' => $opponent ? [
                'id' => $opponent->id,
                'name' => $opponent->name,
                'online' => $opponent->isOnline(),
            ] : null,
            'play_url' => url('/chess?game='.$this->token),
        ];
    }
}
