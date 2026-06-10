<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    public const ROLE_USER = 'user';

    public const ROLE_OWNER = 'owner';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'google_id',
        'password',
        'role',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return asset('storage/'.$this->avatar_path);
    }

    public function avatarInitial(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function isOnline(int $minutes = 2): bool
    {
        if (! $this->last_seen_at) {
            return false;
        }

        return $this->last_seen_at->gte(now()->subMinutes($minutes));
    }

    public function sentFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    public function receivedFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    public function friends(): Collection
    {
        $accepted = Friendship::query()
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->where(function ($query) {
                $query->where('user_id', $this->id)
                    ->orWhere('friend_id', $this->id);
            })
            ->with(['sender', 'recipient'])
            ->get();

        return $accepted->map(function (Friendship $friendship) {
            return $friendship->user_id === $this->id
                ? $friendship->recipient
                : $friendship->sender;
        })->filter()->values();
    }

    public function pendingIncoming(): Collection
    {
        return Friendship::query()
            ->where('friend_id', $this->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->with('sender')
            ->latest()
            ->get();
    }

    public function pendingOutgoing(): Collection
    {
        return Friendship::query()
            ->where('user_id', $this->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->with('recipient')
            ->latest()
            ->get();
    }

    public function isFriendsWith(User $user): bool
    {
        return Friendship::query()
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->where(function ($query) use ($user) {
                $query->where(function ($pair) use ($user) {
                    $pair->where('user_id', $this->id)->where('friend_id', $user->id);
                })->orWhere(function ($pair) use ($user) {
                    $pair->where('user_id', $user->id)->where('friend_id', $this->id);
                });
            })
            ->exists();
    }

    public function hasPendingWith(User $user): bool
    {
        return Friendship::query()
            ->where('status', Friendship::STATUS_PENDING)
            ->where(function ($query) use ($user) {
                $query->where(function ($pair) use ($user) {
                    $pair->where('user_id', $this->id)->where('friend_id', $user->id);
                })->orWhere(function ($pair) use ($user) {
                    $pair->where('user_id', $user->id)->where('friend_id', $this->id);
                });
            })
            ->exists();
    }
}
