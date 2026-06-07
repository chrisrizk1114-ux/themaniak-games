@if (($friendRequestCount ?? 0) > 0)
<div id="friendRequestToast" class="friend-toast" role="alert">
    <div class="friend-toast-inner">
        <span class="friend-toast-icon">👥</span>
        <div class="friend-toast-text">
            <strong>New friend request{{ ($friendRequestCount ?? 0) > 1 ? 's' : '' }}!</strong>
            <span>
                {{ ($friendRequestCount ?? 0) === 1 ? 'Someone wants to join your squad.' : ($friendRequestCount ?? 0).' players want to join your squad.' }}
            </span>
        </div>
        <a href="{{ route('friends.index') }}" class="friend-toast-btn">View</a>
        <button type="button" class="friend-toast-close" id="friendRequestToastClose" aria-label="Dismiss">✕</button>
    </div>
</div>
@endif
