@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.friends-page) {
        max-width: none;
        padding: clamp(1.5rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem);
    }

    .friends-page {
        --cyan: #00f0ff;
        --pink: #ff2d6a;
        --purple: #a855f7;
        --gold: #ffd54a;
        max-width: 920px;
        margin: 0 auto;
        position: relative;
    }

    .friends-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse at 15% 10%, rgba(0,240,255,0.12) 0%, transparent 42%),
            radial-gradient(ellipse at 85% 20%, rgba(168,85,247,0.1) 0%, transparent 40%);
    }

    .friends-header {
        position: relative;
        z-index: 1;
        margin-bottom: 1.75rem;
    }

    .friends-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        background: rgba(0,240,255,0.08);
        border: 1px solid rgba(0,240,255,0.22);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--cyan);
        margin-bottom: 0.75rem;
    }

    .friends-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.8rem, 4vw, 2.4rem);
        font-weight: 800;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }

    .friends-subtitle {
        color: rgba(255,255,255,0.55);
        font-size: 1.05rem;
    }

    .friends-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1.25rem;
    }

    .friends-card {
        padding: 1.35rem 1.5rem;
        border-radius: 20px;
        background: rgba(8, 12, 28, 0.88);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 16px 50px rgba(0,0,0,0.35);
        backdrop-filter: blur(14px);
    }

    .friends-card-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.45);
        margin-bottom: 1rem;
    }

    .friends-add-form {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .friends-input {
        flex: 1 1 220px;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: #fff;
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .friends-input:focus {
        outline: none;
        border-color: rgba(0,240,255,0.45);
        box-shadow: 0 0 0 3px rgba(0,240,255,0.12);
    }

    .friends-btn {
        padding: 0.85rem 1.25rem;
        border: none;
        border-radius: 12px;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .friends-btn--primary {
        color: #fff;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        box-shadow: 0 6px 20px rgba(255,45,106,0.3);
    }

    .friends-btn--primary:hover {
        transform: translateY(-1px);
    }

    .friends-btn--accept {
        color: #052e16;
        background: #4ade80;
    }

    .friends-btn--danger {
        color: #fff;
        background: rgba(248,113,113,0.18);
        border: 1px solid rgba(248,113,113,0.35);
    }

    .friends-list {
        display: grid;
        gap: 0.75rem;
    }

    .friends-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
    }

    .friends-item-info {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 0;
    }

    .friends-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Orbitron', sans-serif;
        font-size: 1rem;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, var(--cyan), var(--purple));
        flex-shrink: 0;
    }

    .friends-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
    }

    .friends-email {
        font-size: 0.88rem;
        color: rgba(255,255,255,0.45);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .friends-item-actions,
    .friends-actions {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-shrink: 0;
    }

    .friends-empty {
        padding: 1.5rem;
        text-align: center;
        color: rgba(255,255,255,0.45);
        font-size: 1rem;
        border-radius: 14px;
        border: 1px dashed rgba(255,255,255,0.12);
    }

    .friends-alert {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .friends-alert--success {
        background: rgba(74,222,128,0.12);
        border: 1px solid rgba(74,222,128,0.28);
        color: #bbf7d0;
    }

    .friends-alert--error {
        background: rgba(248,113,113,0.12);
        border: 1px solid rgba(248,113,113,0.28);
        color: #fecaca;
    }

    .friends-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5rem;
        height: 1.5rem;
        padding: 0 0.45rem;
        margin-left: 0.45rem;
        border-radius: 999px;
        background: rgba(255,45,106,0.2);
        color: var(--pink);
        font-size: 0.75rem;
        font-weight: 800;
    }

    .friends-tag {
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .friends-tag--friends {
        background: rgba(74,222,128,0.12);
        border: 1px solid rgba(74,222,128,0.28);
        color: #bbf7d0;
    }

    .friends-tag--pending {
        background: rgba(255,213,74,0.12);
        border: 1px solid rgba(255,213,74,0.28);
        color: #fde68a;
    }

    .friends-stats {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .friends-stat {
        padding: 0.85rem 0.75rem;
        border-radius: 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        text-align: center;
    }

    .friends-stat-value {
        display: block;
        font-family: 'Orbitron', sans-serif;
        font-size: 1.35rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
    }

    .friends-stat-label {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.4);
    }

    .friends-stat--pending .friends-stat-value { color: var(--gold); }
    .friends-stat--sent .friends-stat-value { color: var(--cyan); }

    .friends-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .friends-avatar-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid rgba(8, 12, 28, 0.95);
    }

    .friends-avatar-dot--online {
        background: #22c55e;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
    }

    .friends-avatar-dot--offline {
        background: #64748b;
    }

    .friends-btn--chat {
        color: #031018;
        background: linear-gradient(135deg, var(--cyan), #38bdf8);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .friends-btn--chat:hover {
        transform: translateY(-1px);
    }

    .friends-card--requests {
        border-color: rgba(255,213,74,0.22);
        box-shadow: 0 16px 50px rgba(255,213,74,0.08);
    }

    .friends-card-title-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .friends-card-icon {
        font-size: 1.1rem;
    }

    @media (max-width: 640px) {
        .friends-header-top {
            flex-direction: column;
            align-items: center !important;
            text-align: center;
        }

        .friends-stats {
            grid-template-columns: 1fr;
        }

        .friends-stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            padding: 0.75rem 1rem;
        }

        .friends-stat-value {
            font-size: 1.15rem;
        }

        .friends-stat-label {
            margin-top: 0;
        }

        .friends-add-form {
            flex-direction: column;
        }

        .friends-input,
        .friends-btn {
            width: 100%;
        }

        .friends-item {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
            gap: 0.85rem;
            padding: 1rem;
        }

        .friends-item-info {
            flex-direction: column;
            align-items: center;
        }

        .friends-email {
            white-space: normal;
            word-break: break-word;
        }

        .friends-item-actions,
        .friends-actions {
            width: 100%;
            flex-wrap: wrap;
            justify-content: center;
        }

        .friends-item-actions form,
        .friends-actions form {
            flex: 1 1 auto;
            min-width: 120px;
        }

        .friends-item-actions .friends-btn,
        .friends-actions .friends-btn,
        .friends-item-actions .friends-tag,
        .friends-item-actions .status-pill {
            width: 100%;
            justify-content: center;
        }

        .friends-item-actions > form .friends-btn {
            width: 100%;
        }
    }

    @media (max-width: 860px), (pointer: coarse) {
        .friends-card {
            backdrop-filter: none;
            background: rgba(8, 12, 28, 0.96);
        }
    }
</style>

<div class="friends-page">
    <div class="friends-header">
        <span class="friends-badge">👥 Squad</span>
        <div class="friends-header-top" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
            <div>
                <h1 class="friends-title">Friends</h1>
                <p class="friends-subtitle">Find players, accept requests, and chat with your squad.</p>
            </div>
            @include('layouts.partials.status-pill', ['user' => auth()->user(), 'self' => true])
        </div>
    </div>

    @if (session('success'))
        <div class="friends-alert friends-alert--success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="friends-alert friends-alert--error">{{ $errors->first() }}</div>
    @endif

    <div class="friends-stats">
        <div class="friends-stat">
            <span class="friends-stat-value">{{ $acceptedFriendships->count() }}</span>
            <span class="friends-stat-label">Friends</span>
        </div>
        <div class="friends-stat friends-stat--pending">
            <span class="friends-stat-value">{{ $incoming->count() }}</span>
            <span class="friends-stat-label">Requests</span>
        </div>
        <div class="friends-stat friends-stat--sent">
            <span class="friends-stat-value">{{ $outgoing->count() }}</span>
            <span class="friends-stat-label">Sent</span>
        </div>
    </div>

    <div class="friends-grid">
        <section class="friends-card">
            <h2 class="friends-card-title friends-card-title-row">
                <span class="friends-card-icon">🔍</span> Add a friend
            </h2>
            <form class="friends-add-form" method="POST" action="{{ route('friends.search') }}">
                @csrf
                <input
                    class="friends-input"
                    type="text"
                    name="name"
                    value="{{ old('name', $searchQuery) }}"
                    placeholder="Search by player name"
                    required
                    autocomplete="off"
                >
                <button type="submit" class="friends-btn friends-btn--primary">Search</button>
            </form>
        </section>

        @if ($searchResults->isNotEmpty())
        <section class="friends-card">
            <h2 class="friends-card-title friends-card-title-row">
                <span class="friends-card-icon">📋</span> Search results
                <span class="friends-count">{{ $searchResults->count() }}</span>
            </h2>
            <div class="friends-list">
                @foreach ($searchResults as $result)
                <div class="friends-item">
                    <div class="friends-item-info">
                        <span class="friends-avatar-wrap">
                            <span class="friends-avatar">{{ strtoupper(substr($result->name, 0, 1)) }}</span>
                            <span class="friends-avatar-dot {{ $result->isOnline() ? 'friends-avatar-dot--online' : 'friends-avatar-dot--offline' }}"></span>
                        </span>
                        <div>
                            <div class="friends-name">{{ $result->name }}</div>
                            <div class="friends-email">{{ $result->email }}</div>
                        </div>
                    </div>
                    <div class="friends-item-actions">
                        @include('layouts.partials.status-pill', ['user' => $result, 'self' => false])
                        @if (auth()->user()->isFriendsWith($result))
                            <span class="friends-tag friends-tag--friends">Already friends</span>
                        @elseif (auth()->user()->hasPendingWith($result))
                            <span class="friends-tag friends-tag--pending">Request pending</span>
                        @else
                            <form method="POST" action="{{ route('friends.store') }}">
                                @csrf
                                <input type="hidden" name="friend_id" value="{{ $result->id }}">
                                <button type="submit" class="friends-btn friends-btn--primary">Send Request</button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        @if ($incoming->isNotEmpty())
        <section class="friends-card friends-card--requests">
            <h2 class="friends-card-title friends-card-title-row">
                <span class="friends-card-icon">📬</span> Friend requests
                <span class="friends-count">{{ $incoming->count() }}</span>
            </h2>
            <div class="friends-list">
                @foreach ($incoming as $request)
                <div class="friends-item">
                    <div class="friends-item-info">
                        <span class="friends-avatar-wrap">
                            <span class="friends-avatar">{{ strtoupper(substr($request->sender->name, 0, 1)) }}</span>
                            <span class="friends-avatar-dot {{ $request->sender->isOnline() ? 'friends-avatar-dot--online' : 'friends-avatar-dot--offline' }}"></span>
                        </span>
                        <div>
                            <div class="friends-name">{{ $request->sender->name }}</div>
                            <div class="friends-email">{{ $request->sender->email }}</div>
                        </div>
                    </div>
                    <div class="friends-actions">
                        <form method="POST" action="{{ route('friends.accept', $request) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="friends-btn friends-btn--accept">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('friends.destroy', $request) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="friends-btn friends-btn--danger">Decline</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <section class="friends-card">
            <h2 class="friends-card-title friends-card-title-row">
                <span class="friends-card-icon">🤝</span> Your friends ({{ $acceptedFriendships->count() }})
            </h2>
            @if ($acceptedFriendships->isEmpty())
                <div class="friends-empty">No friends yet — search by player name above to send a request.</div>
            @else
                <div class="friends-list">
                    @foreach ($acceptedFriendships as $friendship)
                    @php
                        $friend = $friendship->user_id === auth()->id()
                            ? $friendship->recipient
                            : $friendship->sender;
                    @endphp
                    <div class="friends-item">
                        <div class="friends-item-info">
                            <span class="friends-avatar-wrap">
                                <span class="friends-avatar">{{ strtoupper(substr($friend->name, 0, 1)) }}</span>
                                <span class="friends-avatar-dot {{ $friend->isOnline() ? 'friends-avatar-dot--online' : 'friends-avatar-dot--offline' }}"></span>
                            </span>
                            <div>
                                <div class="friends-name">{{ $friend->name }}</div>
                                <div class="friends-email">{{ $friend->email }}</div>
                            </div>
                        </div>
                        <div class="friends-item-actions">
                            @include('layouts.partials.status-pill', ['user' => $friend, 'self' => false])
                            <a href="{{ route('chat.index', ['friend' => $friend->id]) }}" class="friends-btn friends-btn--chat">💬 Chat</a>
                            <form method="POST" action="{{ route('friends.destroy', $friendship) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="friends-btn friends-btn--danger">Remove</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($outgoing->isNotEmpty())
        <section class="friends-card">
            <h2 class="friends-card-title friends-card-title-row">
                <span class="friends-card-icon">📤</span> Sent requests
            </h2>
            <div class="friends-list">
                @foreach ($outgoing as $request)
                <div class="friends-item">
                    <div class="friends-item-info">
                        <span class="friends-avatar-wrap">
                            <span class="friends-avatar">{{ strtoupper(substr($request->recipient->name, 0, 1)) }}</span>
                            <span class="friends-avatar-dot {{ $request->recipient->isOnline() ? 'friends-avatar-dot--online' : 'friends-avatar-dot--offline' }}"></span>
                        </span>
                        <div>
                            <div class="friends-name">{{ $request->recipient->name }}</div>
                            <div class="friends-email">{{ $request->recipient->email }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('friends.destroy', $request) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="friends-btn friends-btn--danger">Cancel</button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>
@endsection
