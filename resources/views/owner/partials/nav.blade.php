<nav class="owner-nav">
    <a href="{{ route('owner.index') }}" class="owner-nav-link {{ request()->routeIs('owner.index') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('owner.users') }}" class="owner-nav-link {{ request()->routeIs('owner.users*') ? 'active' : '' }}">Users</a>
    <a href="{{ route('owner.friendships') }}" class="owner-nav-link {{ request()->routeIs('owner.friendships*') ? 'active' : '' }}">Friendships</a>
    <a href="{{ route('owner.feedback') }}" class="owner-nav-link {{ request()->routeIs('owner.feedback*') ? 'active' : '' }}">
        Feedback
        @if (($unreadFeedbackCount ?? 0) > 0)
            ({{ ($unreadFeedbackCount ?? 0) > 99 ? '99+' : ($unreadFeedbackCount ?? 0) }})
        @endif
    </a>
</nav>
