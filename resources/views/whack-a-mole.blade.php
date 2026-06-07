@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@600;700;800&display=swap');

    body:has(.whack-page) {
        overflow: hidden;
    }

    .main-content:has(.whack-page) {
        max-width: none;
        padding: 0;
        width: 100%;
        overflow: hidden;
    }

    .whack-page {
        --grass: #2d6a2e;
        --grass-light: #4ade80;
        --dirt: #5c3d1e;
        --dirt-dark: #3d2817;
        --hay: #fbbf24;
        --barn-red: #dc2626;
        --nav-h: 76px;
        --hole: min(calc((100svh - var(--nav-h) - 100px) / 3), calc((100vw - 32px) / 3));
        font-family: 'Nunito', sans-serif;
        height: calc(100svh - var(--nav-h));
        min-height: calc(100svh - var(--nav-h));
        max-height: calc(100svh - var(--nav-h));
        width: 100%;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 20% 0%, rgba(251,191,36,0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 100%, rgba(74,222,128,0.12) 0%, transparent 45%),
            linear-gradient(180deg, #87ceeb 0%, #b8e0f0 18%, #4ade80 35%, #2d6a2e 55%, #1a4d1c 100%);
        position: relative;
    }

    .whack-page::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(
            90deg,
            transparent,
            transparent 48px,
            rgba(0,0,0,0.02) 48px,
            rgba(0,0,0,0.02) 50px
        );
        pointer-events: none;
    }

    .whack-shell {
        position: relative;
        z-index: 1;
        height: 100%;
        max-height: 100%;
        width: 100%;
    }

    .whack-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 10;
        text-align: center;
        width: 100%;
        padding: clamp(0.35rem, 1vh, 0.65rem) clamp(0.5rem, 2vw, 1rem) 0;
        pointer-events: none;
    }

    .whack-header > * {
        pointer-events: auto;
    }

    .whack-title {
        font-family: 'Bangers', cursive;
        font-size: clamp(1.5rem, 3.5vw, 2.4rem);
        letter-spacing: 0.06em;
        margin: 0;
        color: #fff;
        text-shadow:
            3px 3px 0 var(--barn-red),
            -1px -1px 0 #1a1a1a,
            0 0 30px rgba(251,191,36,0.4);
        transform: rotate(-1deg);
    }

    .whack-subtitle {
        font-size: clamp(0.7rem, 1.4vw, 0.9rem);
        color: rgba(255,255,255,0.75);
        letter-spacing: 0.18em;
        text-transform: uppercase;
        font-weight: 700;
        margin-top: 0.15rem;
        text-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }

    .hud-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: clamp(0.4rem, 1.5vw, 0.85rem);
        margin-top: clamp(0.5rem, 1.2vh, 0.85rem);
    }

    .hud-pill {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 1rem;
        border-radius: 999px;
        background: rgba(30,20,10,0.75);
        backdrop-filter: blur(8px);
        border: 2px solid rgba(255,255,255,0.12);
        font-weight: 800;
        font-size: clamp(0.85rem, 1.8vw, 1.05rem);
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    .hud-pill strong {
        color: var(--hay);
        font-family: 'Bangers', cursive;
        font-size: 1.15em;
        letter-spacing: 0.04em;
    }

    .hud-pill.timer-urgent {
        border-color: var(--barn-red);
        animation: timer-pulse 0.5s ease-in-out infinite alternate;
    }

    @keyframes timer-pulse {
        from { box-shadow: 0 0 0 rgba(220,38,38,0); }
        to { box-shadow: 0 0 20px rgba(220,38,38,0.5); }
    }

    .timer-bar-wrap {
        width: min(420px, 90vw);
        height: 8px;
        background: rgba(0,0,0,0.3);
        border-radius: 999px;
        overflow: hidden;
        margin: 0.4rem auto 0;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .timer-bar {
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, var(--grass-light), var(--hay));
        border-radius: 999px;
        transition: width 1s linear, background 0.3s;
    }

    .timer-bar.urgent {
        background: linear-gradient(90deg, #f97316, var(--barn-red));
    }

    .combo-display {
        min-height: 1.6rem;
        font-family: 'Bangers', cursive;
        font-size: clamp(1.1rem, 2.5vw, 1.5rem);
        color: var(--hay);
        letter-spacing: 0.05em;
        text-shadow: 0 0 12px rgba(251,191,36,0.6);
        animation: combo-pop 0.25s ease;
    }

    .combo-display:empty { visibility: hidden; }

    @keyframes combo-pop {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .whack-arena {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: clamp(5.5rem, 14vh, 7rem) clamp(0.5rem, 2vw, 1rem) clamp(4rem, 10vh, 5.5rem);
    }

    .board-frame {
        padding: clamp(0.5rem, 1.2vw, 1rem);
        border-radius: 28px;
        background: linear-gradient(145deg, rgba(92,61,30,0.9), rgba(61,40,23,0.95));
        border: 4px solid rgba(251,191,36,0.35);
        box-shadow:
            0 0 0 2px rgba(0,0,0,0.3),
            0 20px 50px rgba(0,0,0,0.4),
            inset 0 2px 0 rgba(255,255,255,0.08);
        position: relative;
    }

    .board-frame::before {
        content: '🔨 MOLE MAYHEM 🔨';
        position: absolute;
        top: -0.65rem;
        left: 50%;
        transform: translateX(-50%);
        font-family: 'Bangers', cursive;
        font-size: clamp(0.75rem, 1.5vw, 0.95rem);
        color: var(--hay);
        background: var(--dirt-dark);
        padding: 0.15rem 0.85rem;
        border-radius: 999px;
        border: 2px solid rgba(251,191,36,0.4);
        white-space: nowrap;
        letter-spacing: 0.08em;
    }

    .game-board {
        display: grid;
        grid-template-columns: repeat(3, var(--hole));
        grid-template-rows: repeat(3, var(--hole));
        gap: clamp(10px, 2vw, 18px);
    }

    .hole {
        width: var(--hole);
        height: var(--hole);
        position: relative;
        cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Ctext y='24' font-size='24'%3E🔨%3C/text%3E%3C/svg%3E") 16 16, pointer;
        border-radius: 50%;
        -webkit-tap-highlight-color: transparent;
        touch-action: none;
    }

    .hole-pit {
        position: absolute;
        inset: 10%;
        border-radius: 50%;
        overflow: hidden;
        z-index: 1;
        box-shadow: inset 0 10px 24px rgba(0,0,0,0.65);
    }

    .hole-dirt {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: radial-gradient(ellipse at 50% 15%, #5a3a22 0%, var(--dirt-dark) 55%, #120a04 100%);
    }

    .hole-rim {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: radial-gradient(circle at 50% 40%,
            transparent 0%,
            transparent 34%,
            #6b8f3a 38%,
            #4a7028 48%,
            #3d5c20 58%,
            #2d4a18 68%,
            #1a3010 76%);
        box-shadow:
            0 6px 0 #142408,
            0 10px 24px rgba(0,0,0,0.35);
        z-index: 3;
        pointer-events: none;
    }

    .mole {
        position: absolute;
        bottom: -120%;
        left: 50%;
        transform: translateX(-50%);
        transition: bottom 0.2s cubic-bezier(0.34, 1.45, 0.64, 1), transform 0.15s ease;
        z-index: 2;
        pointer-events: none;
    }

    .mole.up {
        bottom: 2%;
    }

    .mole.whacked {
        transition: bottom 0.08s ease-in, transform 0.08s ease;
        bottom: -30% !important;
        transform: translateX(-50%) scale(0.55) rotate(18deg);
    }

    .mole-face {
        display: block;
        font-size: calc(var(--hole) * 0.48);
        line-height: 1;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
        transition: transform 0.1s;
        user-select: none;
    }

    .bonk-ring {
        position: absolute;
        inset: 20%;
        border-radius: 50%;
        border: 4px solid var(--hay);
        opacity: 0;
        transform: scale(0.5);
        pointer-events: none;
        z-index: 4;
    }

    .bonk-ring.show {
        animation: bonk 0.35s ease-out forwards;
    }

    @keyframes bonk {
        0% { opacity: 1; transform: scale(0.6); }
        100% { opacity: 0; transform: scale(1.4); }
    }

    .hole:hover .hole-rim {
        box-shadow:
            0 6px 0 #1a3010,
            0 8px 20px rgba(0,0,0,0.35),
            inset 0 -8px 16px rgba(0,0,0,0.25),
            0 0 0 3px rgba(251,191,36,0.35);
    }

    .score-pop {
        position: absolute;
        font-family: 'Bangers', cursive;
        font-size: clamp(1.2rem, 3vw, 1.8rem);
        color: var(--hay);
        text-shadow: 2px 2px 0 #1a1a1a;
        pointer-events: none;
        animation: score-float 0.7s ease-out forwards;
        z-index: 20;
    }

    @keyframes score-float {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-50px) scale(1.2); }
    }

    .whack-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: clamp(0.5rem, 2vw, 1rem);
        flex-wrap: wrap;
        padding: clamp(0.35rem, 1vh, 0.75rem) clamp(0.5rem, 2vw, 1rem);
        pointer-events: none;
    }

    .whack-footer > * {
        pointer-events: auto;
    }

    .whack-btn {
        font-family: 'Nunito', sans-serif;
        font-weight: 800;
        font-size: clamp(0.85rem, 1.7vw, 1rem);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.6rem 1.6rem;
        border-radius: 999px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.25s;
    }

    .whack-btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none !important;
    }

    .whack-btn-primary {
        background: linear-gradient(135deg, var(--barn-red), #f97316);
        color: #fff;
        border-color: rgba(255,255,255,0.2);
        box-shadow: 0 4px 16px rgba(220,38,38,0.35);
    }

    .whack-btn-primary:hover:not(:disabled) {
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 8px 24px rgba(220,38,38,0.45);
    }

    .whack-btn-ghost {
        background: rgba(30,20,10,0.75);
        color: #fff;
        border-color: rgba(255,255,255,0.15);
    }

    .whack-btn-ghost:hover {
        border-color: var(--hay);
        color: var(--hay);
    }

    .high-score-note {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .high-score-note strong { color: var(--hay); }

    .overlay {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(10,20,8,0.8);
        backdrop-filter: blur(6px);
        animation: fade-in 0.3s ease;
    }

    .overlay.hidden { display: none; }

    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .overlay-card {
        text-align: center;
        padding: clamp(1.5rem, 4vw, 2.5rem) clamp(2rem, 6vw, 3.5rem);
        border-radius: 24px;
        background: linear-gradient(145deg, rgba(92,61,30,0.95), rgba(45,30,15,0.98));
        border: 3px solid rgba(251,191,36,0.45);
        box-shadow: 0 0 50px rgba(251,191,36,0.2), 0 30px 80px rgba(0,0,0,0.5);
        animation: card-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-width: 90vw;
    }

    @keyframes card-pop {
        0% { transform: scale(0.85); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .overlay-card h2 {
        font-family: 'Bangers', cursive;
        font-size: clamp(1.8rem, 5vw, 2.8rem);
        margin: 0 0 0.5rem;
        color: #fff;
        text-shadow: 2px 2px 0 var(--barn-red);
        letter-spacing: 0.05em;
    }

    .overlay-card .big-score {
        font-family: 'Bangers', cursive;
        font-size: clamp(2.5rem, 7vw, 4rem);
        color: var(--hay);
        text-shadow: 3px 3px 0 #1a1a1a;
        margin: 0.25rem 0;
    }

    .overlay-card p {
        color: rgba(255,255,255,0.7);
        font-size: 1rem;
        margin-bottom: 1.25rem;
    }

    .new-record {
        color: var(--grass-light) !important;
        font-weight: 800;
        font-size: 1.1rem !important;
        animation: combo-pop 0.4s ease;
    }

    @media (max-width: 600px) {
        .whack-page {
            --hole: min(calc((100svh - var(--nav-h) - 200px) / 3), calc((100vw - 24px) / 3));
        }
        .whack-arena {
            padding: 5.5rem 0.35rem 4.5rem;
        }
        .board-frame::before { display: none; }
        .whack-subtitle { display: none; }
    }
</style>

<div class="whack-page">
    <div class="whack-shell">
        <header class="whack-header">
            <h1 class="whack-title">Mole Mayhem</h1>
            <p class="whack-subtitle">Whack-a-Mole Carnival</p>

            <div class="hud-row">
                <span class="hud-pill">🔨 Score: <strong id="score">0</strong></span>
                <span class="hud-pill" id="timerPill">⏱️ <strong id="timeLeft">30</strong>s</span>
                <span class="hud-pill">🔥 Combo: <strong id="bestCombo">0</strong>x</span>
                <span class="hud-pill">🏆 Best: <strong id="highScore">0</strong></span>
            </div>
            <div class="timer-bar-wrap">
                <div class="timer-bar" id="timerBar"></div>
            </div>
            <p class="combo-display" id="comboDisplay"></p>
        </header>

        <div class="whack-arena" id="whackArena">
            <div class="board-frame">
                <div class="game-board" id="gameBoard">
                    @for ($i = 0; $i < 9; $i++)
                    <div class="hole" data-hole="{{ $i }}">
                        <div class="hole-pit">
                            <div class="hole-dirt"></div>
                            <div class="mole">
                                <span class="mole-face">🐭</span>
                            </div>
                        </div>
                        <div class="hole-rim"></div>
                        <div class="bonk-ring"></div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <footer class="whack-footer">
            <button class="whack-btn whack-btn-primary" id="startBtn" type="button">🔨 Start Game!</button>
            <button class="whack-btn whack-btn-ghost" id="soundBtn" type="button" title="Toggle sound">🔊</button>
        </footer>
    </div>

    <div id="startOverlay" class="overlay">
        <div class="overlay-card">
            <h2>🔨 Mole Mayhem</h2>
            <p>Tap the mice as they pop up! Build combos for bonus points.</p>
            <button class="whack-btn whack-btn-primary" id="startOverlayBtn" type="button">Let's Whack! →</button>
        </div>
    </div>

    <div id="endOverlay" class="overlay hidden">
        <div class="overlay-card">
            <h2>Time's Up! ⏰</h2>
            <p class="big-score" id="finalScore">0</p>
            <p>Best Combo: <strong id="finalCombo">0</strong>x</p>
            <p id="newRecord" class="new-record hidden">🎉 New High Score!</p>
            <button class="whack-btn whack-btn-primary" id="playAgainBtn" type="button">Play Again!</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const scoreEl = document.getElementById('score');
    const timeEl = document.getElementById('timeLeft');
    const timerPill = document.getElementById('timerPill');
    const timerBar = document.getElementById('timerBar');
    const startBtn = document.getElementById('startBtn');
    const soundBtn = document.getElementById('soundBtn');
    const comboEl = document.getElementById('comboDisplay');
    const bestComboEl = document.getElementById('bestCombo');
    const highScoreEl = document.getElementById('highScore');
    const endOverlay = document.getElementById('endOverlay');
    const startOverlay = document.getElementById('startOverlay');
    const startOverlayBtn = document.getElementById('startOverlayBtn');
    const playAgainBtn = document.getElementById('playAgainBtn');
    const finalScoreEl = document.getElementById('finalScore');
    const finalComboEl = document.getElementById('finalCombo');
    const newRecordEl = document.getElementById('newRecord');
    const whackArena = document.getElementById('whackArena');
    const holes = document.querySelectorAll('.hole');

    const BEST_KEY = 'mole_mayhem_best';
    const GAME_TIME = 30;

    let score = 0;
    let timeLeft = GAME_TIME;
    let gameRunning = false;
    let timerInterval = null;
    let lastHole = -1;
    let combo = 0;
    let bestCombo = 0;
    let highScore = parseInt(localStorage.getItem(BEST_KEY) || '0', 10);
    let activePeepTimeouts = [];

    highScoreEl.textContent = String(highScore);

    function clearPeepTimeouts() {
        activePeepTimeouts.forEach(id => clearTimeout(id));
        activePeepTimeouts = [];
    }

    function schedulePeep(delay = 0) {
        const id = setTimeout(() => {
            activePeepTimeouts = activePeepTimeouts.filter(t => t !== id);
            peep();
        }, delay);
        activePeepTimeouts.push(id);
    }

    function randomTime(min, max) {
        return Math.round(Math.random() * (max - min) + min);
    }

    function getPeepRange() {
        const progress = (GAME_TIME - timeLeft) / GAME_TIME;
        const min = Math.round(380 - progress * 220);
        const max = Math.round(950 - progress * 380);
        return [min, max];
    }

    function randomHole() {
        const open = [...holes].filter((h, i) => {
            const mole = h.querySelector('.mole');
            return !mole.classList.contains('up') && !mole.classList.contains('whacked');
        });
        if (!open.length) {
            return Math.floor(Math.random() * holes.length);
        }
        let holeEl = open[Math.floor(Math.random() * open.length)];
        let tries = 0;
        while (open.length > 1 && parseInt(holeEl.dataset.hole, 10) === lastHole && tries < 6) {
            holeEl = open[Math.floor(Math.random() * open.length)];
            tries++;
        }
        lastHole = parseInt(holeEl.dataset.hole, 10);
        return lastHole;
    }

    function clearMole(mole) {
        mole.classList.remove('up', 'whacked');
        mole.querySelector('.mole-face').textContent = '🐭';
    }

    function spawnMole(holeIndex) {
        const mole = holes[holeIndex].querySelector('.mole');
        if (mole.classList.contains('up')) return false;

        clearMole(mole);
        mole.classList.add('up');
        return true;
    }

    function peep() {
        if (!gameRunning) return;

        const [min, max] = getPeepRange();
        const stayTime = randomTime(min, max);
        const holeIndex = randomHole();
        if (!spawnMole(holeIndex)) {
            schedulePeep(120);
            return;
        }

        const mole = holes[holeIndex].querySelector('.mole');
        const id = setTimeout(() => {
            activePeepTimeouts = activePeepTimeouts.filter(t => t !== id);
            if (mole.classList.contains('up') && !mole.classList.contains('whacked')) {
                mole.classList.remove('up');
                clearMole(mole);
                combo = 0;
                comboEl.textContent = '';
            }
        }, stayTime);
        activePeepTimeouts.push(id);

        const progress = (GAME_TIME - timeLeft) / GAME_TIME;
        const burst = progress > 0.45 && Math.random() < 0.35 ? 1 : 0;
        for (let i = 0; i < burst; i++) schedulePeep(randomTime(80, 220));

        schedulePeep(randomTime(180, 420));
    }

    function spawnScorePop(hole, points) {
        const rect = hole.getBoundingClientRect();
        const arenaRect = whackArena.getBoundingClientRect();
        const pop = document.createElement('span');
        pop.className = 'score-pop';
        pop.textContent = '+' + points;
        pop.style.left = (rect.left + rect.width / 2 - arenaRect.left) + 'px';
        pop.style.top = (rect.top - arenaRect.top) + 'px';
        whackArena.appendChild(pop);
        setTimeout(() => pop.remove(), 700);
    }

    function showBonk(hole) {
        const ring = hole.querySelector('.bonk-ring');
        ring.classList.remove('show');
        void ring.offsetWidth;
        ring.classList.add('show');
    }

    function handleHoleClick(hole) {
        if (!gameRunning) return;
        GameSounds.init();

        const mole = hole.querySelector('.mole');
        if (mole.classList.contains('up') && !mole.classList.contains('whacked')) {
            combo++;
            if (combo > bestCombo) bestCombo = combo;
            const bonus = Math.min(combo - 1, 5);

            const points = 1 + bonus;
            score += points;
            scoreEl.textContent = score;
            bestComboEl.textContent = bestCombo;
            comboEl.textContent = combo > 1 ? `🔥 ${combo}x COMBO! (+${bonus} bonus)` : '';

            GameSounds.play('whack');
            mole.classList.add('whacked');
            mole.classList.remove('up');
            showBonk(hole);
            spawnScorePop(hole, points);

            setTimeout(() => clearMole(mole), 120);
        } else {
            combo = 0;
            comboEl.textContent = '';
            GameSounds.play('miss');
        }
    }

    function updateTimerUI() {
        timeEl.textContent = timeLeft;
        const pct = (timeLeft / GAME_TIME) * 100;
        timerBar.style.width = pct + '%';
        const urgent = timeLeft <= 5;
        timerBar.classList.toggle('urgent', urgent);
        timerPill.classList.toggle('timer-urgent', urgent);
    }

    function startGame() {
        GameSounds.init();
        GameSounds.play('start');

        clearPeepTimeouts();
        if (timerInterval) clearInterval(timerInterval);

        score = 0;
        timeLeft = GAME_TIME;
        combo = 0;
        bestCombo = 0;
        lastHole = -1;

        scoreEl.textContent = '0';
        bestComboEl.textContent = '0';
        comboEl.textContent = '';
        updateTimerUI();

        endOverlay.classList.add('hidden');
        startOverlay.classList.add('hidden');
        gameRunning = true;
        startBtn.disabled = true;
        startBtn.textContent = 'Playing...';

        holes.forEach(h => clearMole(h.querySelector('.mole')));

        schedulePeep(200);

        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerUI();
            if (timeLeft <= 5 && timeLeft > 0) GameSounds.play('tick');
            if (timeLeft <= 0) endGame();
        }, 1000);
    }

    function endGame() {
        gameRunning = false;
        clearInterval(timerInterval);
        clearPeepTimeouts();

        startBtn.disabled = false;
        startBtn.textContent = '🔨 Play Again!';

        holes.forEach(h => clearMole(h.querySelector('.mole')));
        comboEl.textContent = '';

        finalScoreEl.textContent = score;
        finalComboEl.textContent = bestCombo;

        if (score > highScore) {
            highScore = score;
            localStorage.setItem(BEST_KEY, String(highScore));
            highScoreEl.textContent = String(highScore);
            newRecordEl.classList.remove('hidden');
        } else {
            newRecordEl.classList.add('hidden');
        }

        endOverlay.classList.remove('hidden');
        GameSounds.play(score >= 20 ? 'celebrate' : 'timeUp');
    }

    startBtn.addEventListener('click', startGame);
    startOverlayBtn.addEventListener('click', startGame);
    playAgainBtn.addEventListener('click', startGame);
    holes.forEach(hole => {
        hole.addEventListener('pointerdown', e => {
            if (e.pointerType !== 'mouse') return;
            e.preventDefault();
            handleHoleClick(hole);
        });
    });
    soundBtn.addEventListener('click', () => {
        GameSounds.init();
        soundBtn.textContent = GameSounds.toggle() ? '🔊' : '🔇';
    });
});
</script>
@endsection
