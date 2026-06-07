@extends('layouts.app')

@section('content')
@include('owner.partials.styles')

<div class="owner-page">
    <header class="owner-header">
        <div class="owner-badge">👑 Owner Panel</div>
        <h1 class="owner-title">Feedback</h1>
        <p class="owner-subtitle">Messages from logged-in players about The Maniak.</p>
    </header>

    @include('owner.partials.nav')

    @if (session('success'))
        <div class="owner-alert owner-alert--success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="owner-alert owner-alert--error">{{ $errors->first() }}</div>
    @endif

    <section class="owner-card">
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <p style="color:rgba(255,255,255,0.5);margin:0;">
                {{ $feedbacks->total() }} total · {{ ($unreadFeedbackCount ?? 0) }} unread
            </p>
            @if (($unreadFeedbackCount ?? 0) > 0)
            <form method="POST" action="{{ route('owner.feedback.read-all') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="owner-btn owner-btn--gold">Mark all as read</button>
            </form>
            @endif
        </div>

        @forelse ($feedbacks as $feedback)
        <article style="padding:1rem 0;border-bottom:1px solid rgba(255,255,255,0.08);{{ $feedback->isUnread() ? 'background:rgba(255,213,74,0.04);margin:0 -1rem;padding-left:1rem;padding-right:1rem;border-radius:12px;' : '' }}">
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;">
                <div>
                    <strong style="color:#fff;font-size:1.05rem;">{{ $feedback->name }}</strong>
                    @if ($feedback->isUnread())
                        <span class="owner-role-badge owner-role-badge--owner" style="margin-left:0.35rem;">New</span>
                    @endif
                    <div style="color:rgba(255,255,255,0.45);font-size:0.9rem;margin-top:0.15rem;">
                        {{ $feedback->email }}
                        · Player #{{ $feedback->user_id ?? ($feedback->player_info['user_id'] ?? '?') }}
                        · {{ $feedback->created_at->format('M j, Y g:i A') }}
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:0.35rem;margin-top:0.45rem;">
                        <span class="owner-role-badge owner-role-badge--user">{{ $feedback->categoryLabel() }}</span>
                        @if ($feedback->gameLabel())
                            <span class="owner-role-badge owner-role-badge--owner">{{ $feedback->gameLabel() }}</span>
                        @endif
                    </div>
                </div>
                <div class="owner-actions">
                    @if ($feedback->isUnread())
                    <form method="POST" action="{{ route('owner.feedback.read', $feedback) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="owner-btn owner-btn--cyan">Mark read</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('owner.feedback.destroy', $feedback) }}" onsubmit="return confirm('Delete this feedback?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="owner-btn owner-btn--danger">Delete</button>
                    </form>
                </div>
            </div>

            @if ($feedback->subject)
                <p style="font-weight:700;color:var(--gold);margin-bottom:0.35rem;">{{ $feedback->subject }}</p>
            @endif

            <p style="color:rgba(255,255,255,0.85);white-space:pre-wrap;line-height:1.5;margin-bottom:0.75rem;">{{ $feedback->message }}</p>

            @if ($feedback->special_details)
                <div style="padding:0.85rem 1rem;border-radius:12px;background:rgba(255,213,74,0.06);border:1px solid rgba(255,213,74,0.2);margin-bottom:0.75rem;">
                    <p style="font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--gold);margin-bottom:0.35rem;">Special details</p>
                    <p style="color:rgba(255,255,255,0.85);white-space:pre-wrap;line-height:1.5;margin:0;">{{ $feedback->special_details }}</p>
                </div>
            @endif

            @if ($feedback->player_info)
                <p style="font-size:0.82rem;color:rgba(255,255,255,0.4);">
                    Account: joined {{ $feedback->player_info['member_since'] ?? '?' }}
                    · {{ $feedback->player_info['friends_count'] ?? 0 }} friends
                    · {{ !empty($feedback->player_info['online']) ? 'was online' : 'was offline' }} at submit
                </p>
            @endif
        </article>
        @empty
        <p style="color:rgba(255,255,255,0.45);">No feedback yet. Share the feedback page with your players!</p>
        @endforelse

        <div class="owner-pagination">
            {{ $feedbacks->links() }}
        </div>
    </section>
</div>
@endsection
