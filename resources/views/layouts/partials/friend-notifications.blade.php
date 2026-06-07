<li class="nav-dropdown-wrap" id="notificationsDropdown">
    <button type="button" class="nav-link nav-notify-btn" id="notificationsToggle" aria-label="Notifications">
        🔔
        @if (($friendRequestCount ?? 0) > 0)
            <span class="nav-notify-badge">{{ $friendRequestCount > 9 ? '9+' : $friendRequestCount }}</span>
        @endif
    </button>
    <div class="nav-dropdown nav-notify-dropdown">
        <div class="nav-dropdown-panel nav-notify-panel">
            <p class="nav-dropdown-title">Notifications</p>

            @if (($friendRequestCount ?? 0) === 0)
                <div class="nav-notify-empty">No new notifications</div>
            @else
                <div class="nav-notify-list">
                    @foreach ($friendRequestNotifications ?? [] as $request)
                    <div class="nav-notify-item">
                        <span class="nav-notify-avatar">{{ strtoupper(substr($request->sender->name, 0, 1)) }}</span>
                        <div class="nav-notify-info">
                            <span class="nav-notify-name">{{ $request->sender->name }}</span>
                            <span class="nav-notify-msg">sent you a friend request</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('friends.index') }}" class="nav-notify-viewall">
                    View all requests ({{ $friendRequestCount }})
                </a>
            @endif
        </div>
    </div>
</li>
