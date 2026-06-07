@extends('layouts.app')

@section('content')
@include('owner.partials.styles')

<div class="owner-page">
    <header class="owner-header">
        <div class="owner-badge">👑 Owner Panel</div>
        <h1 class="owner-title">Site Control</h1>
        <p class="owner-subtitle">Manage The Maniak — users, friendships, and live stats.</p>
    </header>

    @include('owner.partials.nav')

    <div class="owner-grid">
        <section class="owner-card">
            <h2 class="owner-card-title">Live stats</h2>
            <div class="owner-stats">
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['users'] }}</div>
                    <div class="owner-stat-label">Total users</div>
                </div>
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['online'] }}</div>
                    <div class="owner-stat-label">Online now</div>
                </div>
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['registered_today'] }}</div>
                    <div class="owner-stat-label">Joined today</div>
                </div>
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['unread_feedback'] }}</div>
                    <div class="owner-stat-label">Unread feedback</div>
                </div>
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['owners'] }}</div>
                    <div class="owner-stat-label">Owners</div>
                </div>
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['friendships'] }}</div>
                    <div class="owner-stat-label">Friendships</div>
                </div>
                <div class="owner-stat">
                    <div class="owner-stat-value">{{ $stats['pending_requests'] }}</div>
                    <div class="owner-stat-label">Pending requests</div>
                </div>
            </div>
            <p style="margin-top:0.85rem;font-size:0.85rem;color:rgba(255,255,255,0.4);">
                Online = active in the last {{ $onlineMinutes }} minutes.
            </p>
        </section>

        <section class="owner-card">
            <h2 class="owner-card-title">What you can do as owner</h2>
            <div class="owner-capabilities">
                <div class="owner-capability">
                    <span class="owner-capability-icon">📊</span>
                    <div>
                        <strong>View dashboard</strong>
                        <span>See total users, online players, new sign-ups, friendships, and pending friend requests.</span>
                    </div>
                </div>
                <div class="owner-capability">
                    <span class="owner-capability-icon">👥</span>
                    <div>
                        <strong>Manage users</strong>
                        <span>Search all accounts, promote players to owner, demote owners to regular users, or delete accounts.</span>
                    </div>
                </div>
                <div class="owner-capability">
                    <span class="owner-capability-icon">🤝</span>
                    <div>
                        <strong>Manage friendships</strong>
                        <span>View every friendship and pending request on the site and remove any connection.</span>
                    </div>
                </div>
                <div class="owner-capability">
                    <span class="owner-capability-icon">🎮</span>
                    <div>
                        <strong>Play all games</strong>
                        <span>Owner accounts are full player accounts — friends, online status, and every game work normally.</span>
                    </div>
                </div>
                <div class="owner-capability">
                    <span class="owner-capability-icon">💬</span>
                    <div>
                        <strong>Read player feedback</strong>
                        <span>View messages from players, mark them read, or delete them. You are notified when new feedback arrives.</span>
                    </div>
                </div>
                <div class="owner-capability">
                    <span class="owner-capability-icon">🔒</span>
                    <div>
                        <strong>Protected actions</strong>
                        <span>You cannot delete yourself or remove the last owner. Regular sign-up always creates a player account, never an owner.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="owner-card">
            <h2 class="owner-card-title">Recent sign-ups</h2>
            @if ($recentUsers->isEmpty())
                <p style="color:rgba(255,255,255,0.45);">No users yet.</p>
            @else
                <div class="owner-table-wrap">
                    <table class="owner-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="owner-role-badge owner-role-badge--{{ $user->isOwner() ? 'owner' : 'user' }}">
                                        {{ $user->isOwner() ? 'Owner' : 'User' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p style="margin-top:0.85rem;">
                    <a href="{{ route('owner.users') }}" class="owner-btn owner-btn--gold">View all users →</a>
                </p>
            @endif
        </section>
    </div>
</div>
@endsection
