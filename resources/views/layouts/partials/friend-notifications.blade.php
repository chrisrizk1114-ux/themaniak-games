<li class="nav-dropdown-wrap" id="notificationsDropdown">
    <button type="button" class="nav-link nav-notify-btn" id="notificationsToggle" aria-label="Notifications">
        🔔
        @if (($notificationCount ?? 0) > 0)
            <span class="nav-notify-badge" id="navNotifyBadge">{{ ($notificationCount ?? 0) > 9 ? '9+' : ($notificationCount ?? 0) }}</span>
        @else
            <span class="nav-notify-badge hidden" id="navNotifyBadge"></span>
        @endif
    </button>
    <div class="nav-dropdown nav-notify-dropdown">
        <div class="nav-dropdown-panel nav-notify-panel">
            <p class="nav-dropdown-title">Notifications</p>

            @if (($notificationCount ?? 0) === 0)
                <div class="nav-notify-empty" id="navNotifyEmpty">No new notifications</div>
            @else
                <div class="nav-notify-empty hidden" id="navNotifyEmpty">No new notifications</div>
            @endif

            <div class="nav-notify-list" id="navNotifyList">
                @foreach ($friendRequestNotifications ?? [] as $request)
                <div class="nav-notify-item">
                    <span class="nav-notify-avatar">{{ strtoupper(substr($request->sender->name, 0, 1)) }}</span>
                    <div class="nav-notify-info">
                        <span class="nav-notify-name">{{ $request->sender->name }}</span>
                        <span class="nav-notify-msg">sent you a friend request</span>
                    </div>
                </div>
                @endforeach

                @if (auth()->user()->isOwner())
                    @foreach ($feedbackNotifications ?? [] as $feedback)
                    <a href="{{ route('owner.feedback') }}" class="nav-notify-item" style="text-decoration:none;">
                        <span class="nav-notify-avatar">💬</span>
                        <div class="nav-notify-info">
                            <span class="nav-notify-name">{{ $feedback->name }}</span>
                            <span class="nav-notify-msg">
                                {{ $feedback->subject ? 'Feedback: '.$feedback->subject : 'sent new feedback' }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                @endif
            </div>

            @if (($friendRequestCount ?? 0) > 0)
                <a href="{{ route('friends.index') }}" class="nav-notify-viewall">
                    View friend requests ({{ $friendRequestCount }})
                </a>
            @endif

            @if (auth()->user()->isOwner() && ($unreadFeedbackCount ?? 0) > 0)
                <a href="{{ route('owner.feedback') }}" class="nav-notify-viewall" style="margin-top:0.35rem;">
                    View feedback ({{ $unreadFeedbackCount }})
                </a>
            @endif
        </div>
    </div>
</li>
