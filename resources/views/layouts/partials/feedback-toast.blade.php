@if (auth()->user()->isOwner() && ($unreadFeedbackCount ?? 0) > 0)
<div id="feedbackToast" class="friend-toast">
    <div class="friend-toast-inner" style="border-color:rgba(255,213,74,0.35);">
        <span class="friend-toast-icon">💬</span>
        <div class="friend-toast-text">
            <strong>New feedback{{ ($unreadFeedbackCount ?? 0) > 1 ? ' messages' : '' }}!</strong>
            <span>
                {{ ($unreadFeedbackCount ?? 0) === 1 ? 'A player sent new feedback.' : ($unreadFeedbackCount ?? 0).' unread feedback messages.' }}
            </span>
        </div>
        <a href="{{ route('owner.feedback') }}" class="friend-toast-btn">View</a>
        <button type="button" class="friend-toast-close" id="feedbackToastClose" aria-label="Dismiss">✕</button>
    </div>
</div>
@endif
