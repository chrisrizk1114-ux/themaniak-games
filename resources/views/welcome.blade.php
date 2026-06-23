@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.home-page) {
        max-width: none;
        padding: 0;
        width: 100%;
    }

    .home-page {
        --pink: #ff2d6a;
        --cyan: #00f0ff;
        --gold: #ffd54a;
        --purple: #a855f7;
        font-family: 'Rajdhani', sans-serif;
        color: #fff;
        overflow-x: hidden;
    }

    .home-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse at 15% 10%, rgba(255,45,106,0.18) 0%, transparent 42%),
            radial-gradient(ellipse at 85% 20%, rgba(0,240,255,0.14) 0%, transparent 40%),
            radial-gradient(ellipse at 50% 90%, rgba(168,85,247,0.12) 0%, transparent 45%),
            linear-gradient(165deg, #050510 0%, #0c0c22 40%, #080818 100%);
    }

    .home-stars {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image:
            radial-gradient(1px 1px at 8% 15%, rgba(255,255,255,0.55) 0%, transparent 100%),
            radial-gradient(1.5px 1.5px at 22% 68%, rgba(255,255,255,0.4) 0%, transparent 100%),
            radial-gradient(1px 1px at 45% 32%, rgba(255,255,255,0.45) 0%, transparent 100%),
            radial-gradient(1px 1px at 68% 12%, rgba(255,255,255,0.35) 0%, transparent 100%),
            radial-gradient(1.5px 1.5px at 82% 55%, rgba(255,255,255,0.5) 0%, transparent 100%),
            radial-gradient(1px 1px at 92% 78%, rgba(255,255,255,0.4) 0%, transparent 100%);
        animation: stars-pulse 5s ease-in-out infinite alternate;
    }

    @keyframes stars-pulse {
        from { opacity: 0.45; }
        to { opacity: 1; }
    }

    .home-inner {
        position: relative;
        z-index: 1;
        max-width: 1280px;
        margin: 0 auto;
        padding: clamp(1.5rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem) clamp(2.5rem, 5vw, 4rem);
    }

    /* ── Hero ── */
    .hero {
        text-align: center;
        margin-bottom: clamp(2rem, 5vw, 3.5rem);
    }

    .hero-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(2.2rem, 7vw, 4.2rem);
        font-weight: 800;
        letter-spacing: 0.06em;
        line-height: 1.1;
        margin: 0 0 1rem;
        background: linear-gradient(90deg, var(--cyan), #fff, var(--pink), var(--gold));
        background-size: 300% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: hero-shimmer 6s linear infinite;
        filter: drop-shadow(0 0 24px rgba(0,240,255,0.25));
    }

    @keyframes hero-shimmer {
        0% { background-position: 0% center; }
        100% { background-position: 300% center; }
    }

    .hero-stats {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: clamp(0.5rem, 2vw, 1rem);
    }

    .hero-stat {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 1.1rem;
        border-radius: 999px;
        background: rgba(8,12,28,0.75);
        border: 1px solid rgba(255,255,255,0.1);
        font-weight: 700;
        font-size: clamp(0.85rem, 1.8vw, 1rem);
        color: rgba(255,255,255,0.8);
    }

    .hero-stat strong {
        color: var(--gold);
        font-family: 'Orbitron', sans-serif;
    }

    /* ── Featured ── */
    .section-label {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(0.7rem, 1.4vw, 0.8rem);
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.4);
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(255,255,255,0.15), transparent);
    }

    .featured-card {
        display: block;
        position: relative;
        border-radius: 24px;
        padding: clamp(1.25rem, 3vw, 2rem);
        margin-bottom: clamp(2rem, 4vw, 3rem);
        text-decoration: none;
        color: inherit;
        overflow: hidden;
        border: 2px solid rgba(236,72,153,0.45);
        background:
            linear-gradient(135deg, rgba(236,72,153,0.12) 0%, rgba(34,211,238,0.08) 45%, rgba(251,191,36,0.08) 100%),
            rgba(8,12,28,0.85);
        box-shadow: 0 0 60px rgba(236,72,153,0.15), 0 24px 60px rgba(0,0,0,0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s;
    }

    .featured-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(236,72,153,0.2) 0%, transparent 70%);
        pointer-events: none;
        animation: featured-glow 4s ease-in-out infinite alternate;
    }

    @keyframes featured-glow {
        from { opacity: 0.5; transform: scale(1); }
        to { opacity: 1; transform: scale(1.05); }
    }

    .featured-card:hover {
        transform: translateY(-6px) scale(1.01);
        border-color: rgba(236,72,153,0.75);
        box-shadow: 0 0 80px rgba(236,72,153,0.3), 0 32px 70px rgba(0,0,0,0.5);
    }

    .featured-inner {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: clamp(1rem, 3vw, 2rem);
    }

    .featured-icon {
        font-size: clamp(3.5rem, 8vw, 5.5rem);
        filter: drop-shadow(0 0 20px rgba(236,72,153,0.5));
        animation: icon-float 3s ease-in-out infinite;
    }

    @keyframes icon-float {
        0%, 100% { transform: translateY(0) rotate(-3deg); }
        50% { transform: translateY(-10px) rotate(3deg); }
    }

    .featured-card:hover .featured-icon {
        animation: icon-float 1.5s ease-in-out infinite;
    }

    .featured-tag {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #f472b6;
        margin-bottom: 0.35rem;
    }

    .featured-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.5rem, 4vw, 2.2rem);
        font-weight: 800;
        margin: 0 0 0.5rem;
        color: #fff;
    }

    .featured-desc {
        color: rgba(255,255,255,0.65);
        font-size: clamp(0.95rem, 1.8vw, 1.1rem);
        font-weight: 600;
        max-width: 32rem;
        line-height: 1.45;
    }

    .featured-cta {
        margin-left: auto;
        padding: 0.75rem 1.75rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        border: 2px solid rgba(255,255,255,0.15);
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(0.75rem, 1.5vw, 0.9rem);
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #fff;
        white-space: nowrap;
        box-shadow: 0 4px 24px rgba(236,72,153,0.4);
        transition: transform 0.25s, box-shadow 0.25s;
    }

    .featured-card:hover .featured-cta {
        transform: scale(1.05);
        box-shadow: 0 8px 32px rgba(236,72,153,0.55);
    }

    /* ── Game grid ── */
    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
        gap: clamp(1rem, 2.5vw, 1.35rem);
    }

    .game-card {
        position: relative;
        display: flex;
        flex-direction: column;
        padding: clamp(1.1rem, 2.5vw, 1.4rem);
        border-radius: 20px;
        text-decoration: none;
        color: inherit;
        overflow: hidden;
        background: rgba(8,12,28,0.82);
        border: 2px solid rgba(255,255,255,0.08);
        transition: transform 0.28s ease, border-color 0.28s, box-shadow 0.28s;
    }

    .game-card::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.28s;
        pointer-events: none;
    }

    .game-card:hover {
        transform: translateY(-6px);
    }

    .game-card:hover::before { opacity: 1; }

    .game-card:hover .game-icon {
        animation: icon-bounce 0.8s ease infinite;
    }

    @keyframes icon-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .game-icon {
        font-size: clamp(2.2rem, 5vw, 2.8rem);
        margin-bottom: 0.75rem;
        display: block;
        transition: transform 0.2s;
    }

    .game-name {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(0.95rem, 2vw, 1.1rem);
        font-weight: 700;
        margin: 0 0 0.35rem;
        letter-spacing: 0.04em;
    }

    .game-desc {
        font-size: clamp(0.82rem, 1.6vw, 0.92rem);
        color: rgba(255,255,255,0.5);
        font-weight: 600;
        line-height: 1.4;
        flex: 1;
    }

    .game-tag {
        display: inline-block;
        margin-top: 0.85rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        border: 1px solid;
        width: fit-content;
    }

    /* Per-game accents */
    .game-card--runner::before { background: radial-gradient(ellipse at 50% 0%, rgba(34,211,238,0.15) 0%, transparent 70%); }
    .game-card--runner:hover { border-color: rgba(34,211,238,0.5); box-shadow: 0 0 40px rgba(34,211,238,0.15); }
    .game-card--runner .game-name { color: #67e8f9; }
    .game-card--runner .game-tag { color: #22d3ee; border-color: rgba(34,211,238,0.35); background: rgba(34,211,238,0.08); }

    .game-card--neon::before { background: radial-gradient(ellipse at 50% 0%, rgba(255,45,106,0.15) 0%, transparent 70%); }
    .game-card--neon:hover { border-color: rgba(255,45,106,0.5); box-shadow: 0 0 40px rgba(255,45,106,0.15); }
    .game-card--neon .game-name { color: #ff2d6a; }
    .game-card--neon .game-tag { color: #ff2d6a; border-color: rgba(255,45,106,0.35); background: rgba(255,45,106,0.08); }

    .game-card--snl::before { background: radial-gradient(ellipse at 50% 0%, rgba(74,222,128,0.15) 0%, transparent 70%); }
    .game-card--snl:hover { border-color: rgba(74,222,128,0.5); box-shadow: 0 0 40px rgba(74,222,128,0.15); }
    .game-card--snl .game-name { color: #4ade80; }
    .game-card--snl .game-tag { color: #4ade80; border-color: rgba(74,222,128,0.35); background: rgba(74,222,128,0.08); }

    .game-card--chess::before { background: radial-gradient(ellipse at 50% 0%, rgba(212,168,83,0.15) 0%, transparent 70%); }
    .game-card--chess:hover { border-color: rgba(212,168,83,0.5); box-shadow: 0 0 40px rgba(212,168,83,0.15); }
    .game-card--chess .game-name { color: #d4a853; }
    .game-card--chess .game-tag { color: #d4a853; border-color: rgba(212,168,83,0.35); background: rgba(212,168,83,0.08); }

    .game-card--mole::before { background: radial-gradient(ellipse at 50% 0%, rgba(251,191,36,0.15) 0%, transparent 70%); }
    .game-card--mole:hover { border-color: rgba(251,191,36,0.5); box-shadow: 0 0 40px rgba(251,191,36,0.15); }
    .game-card--mole .game-name { color: #fbbf24; }
    .game-card--mole .game-tag { color: #fbbf24; border-color: rgba(251,191,36,0.35); background: rgba(251,191,36,0.08); }

    .game-card--bowling::before { background: radial-gradient(ellipse at 50% 0%, rgba(236,72,153,0.15) 0%, transparent 70%); }
    .game-card--bowling:hover { border-color: rgba(236,72,153,0.5); box-shadow: 0 0 40px rgba(236,72,153,0.15); }
    .game-card--bowling .game-name { color: #f472b6; }
    .game-card--bowling .game-tag { color: #f472b6; border-color: rgba(236,72,153,0.35); background: rgba(236,72,153,0.08); }

    .game-card--uno::before { background: radial-gradient(ellipse at 50% 0%, rgba(239,68,68,0.15) 0%, transparent 70%); }
    .game-card--uno:hover { border-color: rgba(239,68,68,0.5); box-shadow: 0 0 40px rgba(239,68,68,0.15); }
    .game-card--uno .game-name { color: #f87171; }
    .game-card--uno .game-tag { color: #f87171; border-color: rgba(239,68,68,0.35); background: rgba(239,68,68,0.08); }

    /* ── Footer ── */
    .home-footer {
        text-align: center;
        margin-top: clamp(2.5rem, 5vw, 4rem);
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.06);
    }

    .home-footer p {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.35);
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-weight: 600;
    }

    .home-footer strong {
        color: var(--gold);
        font-family: 'Orbitron', sans-serif;
    }

    @media (max-width: 640px) {
        .featured-cta {
            margin-left: 0;
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 860px), (pointer: coarse) {
        .home-stars,
        .hero-title,
        .featured-card,
        .featured-card .game-icon,
        .game-card:hover .game-icon {
            animation: none !important;
        }
    }
</style>

<div class="home-page">
    <div class="home-stars"></div>

    <div class="home-inner">
        <!-- Hero -->
        <section class="hero">
            <h1 class="hero-title">Mini Games</h1>
            <div class="hero-stats">
                <span class="hero-stat">🃏 <strong>7</strong> Games</span>
                <span class="hero-stat">🔊 <strong>Sound</strong> FX</span>
                <span class="hero-stat">📱 <strong>Play</strong> Instantly</span>
                <span class="hero-stat">🏆 <strong>High</strong> Scores</span>
            </div>
        </section>

        <!-- Featured -->
        <p class="section-label">Spotlight</p>
        <a href="{{ url('/galaxy-bowling') }}" class="featured-card">
            <div class="featured-inner">
                <span class="featured-icon">🎳</span>
                <div class="flex-1 min-w-[180px]">
                    <p class="featured-tag">★ Featured Game</p>
                    <h2 class="featured-title">Galaxy Bowling</h2>
                    <p class="featured-desc">3D cosmic lanes, ball skins, live scoreboard, leaderboard &amp; pinsetter machine. Fullscreen arcade bowling!</p>
                </div>
                <span class="featured-cta">Play Now →</span>
            </div>
        </a>

        <!-- All games -->
        <p class="section-label" id="games">All Games</p>
        <div class="games-grid">

            <a href="{{ url('/platformer') }}" class="game-card game-card--runner">
                <span class="game-icon">🎮</span>
                <h3 class="game-name">Sky Runner</h3>
                <p class="game-desc">Leap across floating islands, grab crystals, double-jump through the sky. How far can you run?</p>
                <span class="game-tag">Action</span>
            </a>

            <a href="{{ url('/tic-tac-toe') }}" class="game-card game-card--neon">
                <span class="game-icon">✕○</span>
                <h3 class="game-name">Neon Grid</h3>
                <p class="game-desc">Tic Tac Toe arena with neon glow, win streaks, score tracking &amp; animated victories.</p>
                <span class="game-tag">Puzzle</span>
            </a>

            <a href="{{ url('/board-game') }}" class="game-card game-card--snl">
                <span class="game-icon">🎲</span>
                <h3 class="game-name">Snakes &amp; Ladders 3D</h3>
                <p class="game-desc">Roll the dice on a 3D perspective board. Climb ladders, dodge snakes — first to 100 wins!</p>
                <span class="game-tag">Board</span>
            </a>

            <a href="{{ url('/chess') }}" class="game-card game-card--chess">
                <span class="game-icon">♟️</span>
                <h3 class="game-name">Royal Chess</h3>
                <p class="game-desc">Luxury marble board, play vs AI or a friend. Hints, timers &amp; a royal burgundy-gold theme.</p>
                <span class="game-tag">Strategy</span>
            </a>

            <a href="{{ url('/whack-a-mole') }}" class="game-card game-card--mole">
                <span class="game-icon">🔨</span>
                <h3 class="game-name">Mole Mayhem</h3>
                <p class="game-desc">Whack-a-Mole carnival! Build combos, beat the clock &amp; chase your high score with the hammer.</p>
                <span class="game-tag">Arcade</span>
            </a>

            <a href="{{ url('/galaxy-bowling') }}" class="game-card game-card--bowling">
                <span class="game-icon">🌌</span>
                <h3 class="game-name">Galaxy Bowling</h3>
                <p class="game-desc">Cosmic 3D bowling with Stella Strike, ball picker, frame scoreboard &amp; fullscreen lanes.</p>
                <span class="game-tag">Sports</span>
            </a>

            <a href="{{ url('/uno') }}" class="game-card game-card--uno">
                <span class="game-icon">🃏</span>
                <h3 class="game-name">Cosmic UNO</h3>
                <p class="game-desc">Fullscreen UNO vs 3 bots! Wild cards, +2/+4, Skip, Reverse &amp; the UNO button.</p>
                <span class="game-tag">Cards</span>
            </a>

        </div>

        <footer class="home-footer">
            <p><strong>7</strong> games ready to play · Pick one and jump in</p>
        </footer>
    </div>
</div>
@endsection
