@once
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/game-leaderboard.css') }}?v=20260608">
        <script src="{{ asset('js/game-leaderboard.js') }}?v=20260608" defer></script>
    @endpush
@endonce

<div class="game-lb {{ $class ?? '' }}" id="{{ $id ?? 'gameLeaderboard' }}" data-game="{{ $game }}"></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mountLeaderboard = () => {
            if (typeof GameLeaderboard === 'undefined') return;
            GameLeaderboard.mount('#{{ $id ?? 'gameLeaderboard' }}', @json($game));
        };
        mountLeaderboard();
        if (typeof GameLeaderboard === 'undefined') {
            document.querySelector('script[src*="game-leaderboard.js"]')?.addEventListener('load', mountLeaderboard, { once: true });
        }
    });
</script>
@endpush
