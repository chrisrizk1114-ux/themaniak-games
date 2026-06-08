@if (($chessInviteCount ?? 0) > 0)
<div id="chessInviteToast" class="friend-toast chess-toast" role="alert">
    <div class="friend-toast-inner">
        <span class="friend-toast-icon">♟</span>
        <div class="friend-toast-text">
            <strong>Chess invite{{ ($chessInviteCount ?? 0) > 1 ? 's' : '' }}!</strong>
            <span>
                @if(($chessInviteCount ?? 0) === 1 && ($chessInviteNotifications ?? collect())->first())
                    {{ ($chessInviteNotifications ?? collect())->first()->whitePlayer->name }} wants to play Royal Chess with you.
                @else
                    {{ $chessInviteCount }} friend{{ ($chessInviteCount ?? 0) > 1 ? 's' : '' }} invited you to Royal Chess.
                @endif
            </span>
        </div>
        <a href="{{ url('/chess') }}" class="friend-toast-btn">Play</a>
        <button type="button" class="friend-toast-close" id="chessInviteToastClose" aria-label="Dismiss">✕</button>
    </div>
</div>
@endif
