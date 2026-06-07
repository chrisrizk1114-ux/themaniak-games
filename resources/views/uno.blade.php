@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@400;600;700;800&display=swap');

    .main-content:has(.uno-page) {
        max-width: none;
        padding: 0;
        width: 100%;
        overflow: hidden;
    }

    body:has(.uno-page) { overflow: hidden; }

    .uno-page {
        --red: #ef4444;
        --green: #22c55e;
        --blue: #3b82f6;
        --yellow: #eab308;
        --nav-h: 76px;
        font-family: 'Outfit', sans-serif;
        height: calc(100svh - var(--nav-h));
        min-height: calc(100svh - var(--nav-h));
        max-height: calc(100svh - var(--nav-h));
        width: 100%;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 20% 10%, rgba(239,68,68,0.12) 0%, transparent 40%),
            radial-gradient(ellipse at 80% 90%, rgba(59,130,246,0.12) 0%, transparent 40%),
            radial-gradient(ellipse at 50% 50%, rgba(234,179,8,0.06) 0%, transparent 55%),
            linear-gradient(160deg, #1a0a2e 0%, #2d1b4e 35%, #1a1028 100%);
        position: relative;
    }

    .uno-page::before {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-conic-gradient(from 0deg at 50% 50%, transparent 0deg, rgba(255,255,255,0.015) 2deg, transparent 4deg);
        pointer-events: none;
    }

    .uno-table {
        position: relative;
        z-index: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: clamp(0.35rem, 1vh, 0.65rem) clamp(0.5rem, 2vw, 1rem);
    }

    .uno-hud {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .uno-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(1.6rem, 4vw, 2.4rem);
        letter-spacing: 0.12em;
        background: linear-gradient(90deg, var(--red), var(--yellow), var(--green), var(--blue));
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: uno-shimmer 4s linear infinite;
        filter: drop-shadow(0 0 12px rgba(234,179,8,0.3));
    }

    @keyframes uno-shimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .uno-status {
        font-weight: 700;
        font-size: clamp(0.85rem, 1.8vw, 1rem);
        color: rgba(255,255,255,0.75);
        text-align: center;
        flex: 1;
        min-width: 120px;
    }

    .uno-status strong { color: #fff; }

    .hud-btns { display: flex; gap: 0.4rem; flex-wrap: wrap; }

    .uno-btn {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.45rem 1rem;
        border-radius: 999px;
        border: 2px solid rgba(255,255,255,0.15);
        background: rgba(0,0,0,0.35);
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }

    .uno-btn:hover { border-color: rgba(255,255,255,0.35); transform: translateY(-1px); }
    .uno-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

    .uno-btn--uno {
        background: linear-gradient(135deg, var(--red), #dc2626);
        border-color: rgba(239,68,68,0.5);
        box-shadow: 0 0 20px rgba(239,68,68,0.35);
        animation: uno-pulse 1s ease-in-out infinite alternate;
    }

    @keyframes uno-pulse {
        from { box-shadow: 0 0 10px rgba(239,68,68,0.3); }
        to { box-shadow: 0 0 28px rgba(239,68,68,0.6); }
    }

    .uno-btn--draw {
        background: linear-gradient(135deg, var(--blue), #2563eb);
        border-color: rgba(59,130,246,0.4);
    }

    .uno-arena {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
        grid-template-rows: 1fr 1.2fr 1fr;
        grid-template-areas:
            ". top ."
            "left center right"
            ". bottom .";
        gap: 0.5rem;
        min-height: 0;
        align-items: center;
        justify-items: center;
    }

    .opponent { text-align: center; }
    .opponent--top { grid-area: top; }
    .opponent--left { grid-area: left; }
    .opponent--right { grid-area: right; }

    .opp-name {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1rem;
        letter-spacing: 0.1em;
        margin-bottom: 0.35rem;
        color: rgba(255,255,255,0.7);
    }

    .opp-name.active {
        color: #fff;
        text-shadow: 0 0 12px rgba(255,255,255,0.5);
    }

    .opp-cards {
        display: flex;
        justify-content: center;
        gap: -8px;
    }

    .card-back {
        width: clamp(28px, 4vw, 38px);
        height: clamp(40px, 5.5vw, 54px);
        border-radius: 6px;
        background: linear-gradient(135deg, #1e1b4b, #312e81);
        border: 2px solid rgba(255,255,255,0.2);
        box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        margin-left: -10px;
        position: relative;
    }

    .card-back:first-child { margin-left: 0; }

    .card-back::after {
        content: 'UNO';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 0.55rem;
        color: rgba(255,255,255,0.5);
        letter-spacing: 0.05em;
    }

    .opp-count {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.45);
        margin-top: 0.25rem;
        font-weight: 700;
    }

    .table-center {
        grid-area: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }

    .color-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        background: rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .color-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        box-shadow: 0 0 12px currentColor;
        animation: color-glow 1.5s ease-in-out infinite alternate;
    }

    @keyframes color-glow {
        from { transform: scale(1); }
        to { transform: scale(1.15); }
    }

    .color-dot.red { background: var(--red); color: var(--red); }
    .color-dot.green { background: var(--green); color: var(--green); }
    .color-dot.blue { background: var(--blue); color: var(--blue); }
    .color-dot.yellow { background: var(--yellow); color: var(--yellow); }

    .piles {
        display: flex;
        align-items: center;
        gap: clamp(1.5rem, 4vw, 3rem);
    }

    .pile {
        position: relative;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .pile:hover { transform: scale(1.05); }

    .pile-label {
        position: absolute;
        bottom: -1.2rem;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.35);
        white-space: nowrap;
    }

    .player-zone {
        grid-area: bottom;
        width: 100%;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        min-height: 0;
    }

    .player-hand {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: clamp(4px, 1vw, 8px);
        max-width: 100%;
        padding: 0.25rem;
    }

    /* ── UNO Cards ── */
    .uno-card {
        width: clamp(52px, 8vw, 72px);
        height: clamp(76px, 11vw, 104px);
        border-radius: 10px;
        border: 3px solid rgba(255,255,255,0.25);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(1.4rem, 3vw, 2rem);
        color: #fff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
        box-shadow: 0 6px 20px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.2);
        cursor: default;
        user-select: none;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        flex-shrink: 0;
    }

    .uno-card.red { background: linear-gradient(145deg, #f87171, var(--red), #b91c1c); }
    .uno-card.green { background: linear-gradient(145deg, #86efac, var(--green), #15803d); }
    .uno-card.blue { background: linear-gradient(145deg, #93c5fd, var(--blue), #1d4ed8); }
    .uno-card.yellow { background: linear-gradient(145deg, #fde047, var(--yellow), #ca8a04); color: #1a1a1a; text-shadow: none; }
    .uno-card.wild {
        background: linear-gradient(145deg, #1f2937, #111827);
        border-color: rgba(255,255,255,0.4);
        overflow: hidden;
    }

    .uno-card.wild::before {
        content: '';
        position: absolute;
        inset: 0;
        background: conic-gradient(var(--red), var(--yellow), var(--green), var(--blue), var(--red));
        opacity: 0.35;
    }

    .uno-card .card-val { position: relative; z-index: 1; line-height: 1; }
    .uno-card .card-sub {
        position: relative;
        z-index: 1;
        font-size: 0.45em;
        letter-spacing: 0.05em;
        opacity: 0.85;
        margin-top: 2px;
    }

    .uno-card.playable {
        cursor: pointer;
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.5), 0 0 20px rgba(255,255,255,0.15);
        border-color: #fff;
    }

    .uno-card.playable:hover {
        transform: translateY(-14px) scale(1.04);
    }

    .uno-card.discard-top {
        transform: rotate(-3deg) scale(1.08);
        cursor: default;
    }

    .uno-card.draw-pile {
        background: linear-gradient(135deg, #312e81, #1e1b4b);
        border-color: rgba(255,255,255,0.2);
    }

    .uno-card.draw-pile::after {
        content: 'DRAW';
        font-size: 0.55rem;
        letter-spacing: 0.1em;
        opacity: 0.6;
        margin-top: 4px;
    }

    .uno-card.deal-anim {
        animation: card-deal 0.35s cubic-bezier(0.34, 1.4, 0.64, 1);
    }

    @keyframes card-deal {
        from { transform: scale(0) rotate(-20deg); opacity: 0; }
        to { transform: scale(1) rotate(0); opacity: 1; }
    }

    .uno-card.play-anim {
        animation: card-play 0.4s ease forwards;
    }

    @keyframes card-play {
        0% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(1) rotate(-3deg); opacity: 1; }
    }

    /* Overlays */
    .uno-overlay {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(10,5,20,0.82);
        backdrop-filter: blur(8px);
    }

    .uno-overlay.hidden { display: none; }

    .overlay-card {
        text-align: center;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        border-radius: 24px;
        background: linear-gradient(145deg, rgba(45,27,78,0.95), rgba(26,16,40,0.98));
        border: 2px solid rgba(234,179,8,0.4);
        box-shadow: 0 0 60px rgba(234,179,8,0.15);
        max-width: 90vw;
        animation: pop-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes pop-in {
        from { transform: scale(0.85); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .overlay-card h2 {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(2rem, 6vw, 3rem);
        letter-spacing: 0.08em;
        margin-bottom: 0.5rem;
        background: linear-gradient(90deg, var(--red), var(--yellow), var(--green), var(--blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .overlay-card p {
        color: rgba(255,255,255,0.65);
        margin-bottom: 1.25rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .color-picker {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin: 1rem 0;
    }

    .color-pick-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.3);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .color-pick-btn:hover {
        transform: scale(1.12);
        box-shadow: 0 0 24px currentColor;
    }

    .color-pick-btn.red { background: var(--red); color: var(--red); }
    .color-pick-btn.green { background: var(--green); color: var(--green); }
    .color-pick-btn.blue { background: var(--blue); color: var(--blue); }
    .color-pick-btn.yellow { background: var(--yellow); color: var(--yellow); }

    .toast {
        position: fixed;
        top: calc(var(--nav-h) + 12px);
        left: 50%;
        transform: translateX(-50%);
        padding: 0.6rem 1.4rem;
        border-radius: 999px;
        background: rgba(0,0,0,0.75);
        border: 1px solid rgba(255,255,255,0.15);
        font-weight: 700;
        z-index: 90;
        animation: toast-in 0.3s ease;
        pointer-events: none;
    }

    .toast.hidden { display: none; }

    @keyframes toast-in {
        from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    @media (max-width: 600px) {
        .uno-arena {
            grid-template-columns: 1fr;
            grid-template-rows: auto auto 1fr auto;
            grid-template-areas:
                "top"
                "center"
                "bottom"
                ".";
        }
        .opponent--left, .opponent--right { display: none; }
        .uno-hud { flex-direction: column; align-items: stretch; }
        .uno-status { order: 2; }
        .hud-btns { justify-content: center; }
        .uno-btn { min-height: 44px; padding: 0.5rem 0.85rem; }
        .player-hand {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            justify-content: flex-start;
            width: 100%;
            max-width: 100vw;
            scroll-snap-type: x proximity;
            padding: 0.35rem 0.25rem 0.5rem;
        }
        .player-hand .uno-card { scroll-snap-align: center; }
        .uno-card {
            width: clamp(56px, 14vw, 68px);
            height: clamp(80px, 20vw, 96px);
            min-width: 56px;
        }
        .uno-card.playable:hover { transform: translateY(-6px); }
        .table-center { padding: 0.25rem; }
        .piles { gap: 0.75rem; }
    }
</style>

<div class="uno-page">
    <div class="uno-table">
        <header class="uno-hud">
            <h1 class="uno-title">Cosmic UNO</h1>
            <p class="uno-status" id="statusText">Press Start to deal!</p>
            <div class="hud-btns">
                <button class="uno-btn uno-btn--uno hidden" id="unoBtn" type="button">🃏 UNO!</button>
                <button class="uno-btn uno-btn--draw" id="drawBtn" type="button" disabled>Draw</button>
                <button class="uno-btn" id="soundBtn" type="button">🔊</button>
                <button class="uno-btn" id="restartBtn" type="button">↻ New Game</button>
            </div>
        </header>

        <div class="uno-arena">
            <div class="opponent opponent--top" id="opp2">
                <div class="opp-name">Nova</div>
                <div class="opp-cards" id="opp2Cards"></div>
                <div class="opp-count" id="opp2Count">7 cards</div>
            </div>
            <div class="opponent opponent--left" id="opp1">
                <div class="opp-name">Blaze</div>
                <div class="opp-cards" id="opp1Cards"></div>
                <div class="opp-count" id="opp1Count">7 cards</div>
            </div>
            <div class="opponent opponent--right" id="opp3">
                <div class="opp-name">Zephyr</div>
                <div class="opp-cards" id="opp3Cards"></div>
                <div class="opp-count" id="opp3Count">7 cards</div>
            </div>

            <div class="table-center">
                <div class="color-indicator">
                    Color: <span class="color-dot red" id="colorDot"></span>
                    <span id="colorLabel">—</span>
                </div>
                <div class="piles">
                    <div class="pile" id="drawPile" title="Draw a card">
                        <div class="uno-card draw-pile" id="drawPileCard"></div>
                        <span class="pile-label">Draw (<span id="deckCount">0</span>)</span>
                    </div>
                    <div class="pile">
                        <div id="discardPile"></div>
                        <span class="pile-label">Discard</span>
                    </div>
                </div>
            </div>

            <div class="player-zone">
                <div class="opp-name active" id="playerLabel">You</div>
                <div class="player-hand" id="playerHand"></div>
            </div>
        </div>
    </div>

    <div id="startOverlay" class="uno-overlay">
        <div class="overlay-card">
            <h2>🃏 Cosmic UNO</h2>
            <p>Match color or number. Play Wilds to change the color.<br>Hit <strong>UNO!</strong> when you have one card left!<br>You vs 3 cosmic bots — first to empty their hand wins!</p>
            <button class="uno-btn uno-btn--draw" id="startBtn" type="button" style="font-size:1rem;padding:0.75rem 2rem;">Deal Cards →</button>
        </div>
    </div>

    <div id="winOverlay" class="uno-overlay hidden">
        <div class="overlay-card">
            <h2 id="winTitle">You Win!</h2>
            <p id="winMsg">Cosmic victory!</p>
            <button class="uno-btn uno-btn--draw" id="playAgainBtn" type="button" style="font-size:1rem;padding:0.75rem 2rem;">Play Again</button>
        </div>
    </div>

    <div id="colorOverlay" class="uno-overlay hidden">
        <div class="overlay-card">
            <h2>Pick a Color</h2>
            <div class="color-picker">
                <button class="color-pick-btn red" data-color="red" type="button"></button>
                <button class="color-pick-btn green" data-color="green" type="button"></button>
                <button class="color-pick-btn blue" data-color="blue" type="button"></button>
                <button class="color-pick-btn yellow" data-color="yellow" type="button"></button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const COLORS = ['red', 'green', 'blue', 'yellow'];
    const VALUES = ['0','1','2','3','4','5','6','7','8','9','skip','reverse','draw2'];
    const PLAYER_NAMES = ['You', 'Blaze', 'Nova', 'Zephyr'];
    const WINS_KEY = 'cosmic_uno_wins';
    const AI_TURN_DELAY = 1500;
    const HUMAN_TURN_DELAY = 80;
    const AI_ACTION_DELAY = 200;

    let deck = [], discard = [], hands = [[],[],[],[]];
    let currentPlayer = 0, direction = 1, activeColor = 'red';
    let gameActive = false, pendingDraw = 0, humanUnoCalled = false;
    let pendingWildCard = null, wins = parseInt(localStorage.getItem(WINS_KEY) || '0', 10);
    let turnTimer = null, turnGen = 0, watchdogTimer = null;

    const statusEl = document.getElementById('statusText');
    const playerHandEl = document.getElementById('playerHand');
    const discardEl = document.getElementById('discardPile');
    const drawPileEl = document.getElementById('drawPile');
    const deckCountEl = document.getElementById('deckCount');
    const colorDot = document.getElementById('colorDot');
    const colorLabel = document.getElementById('colorLabel');
    const drawBtn = document.getElementById('drawBtn');
    const unoBtn = document.getElementById('unoBtn');
    const startOverlay = document.getElementById('startOverlay');
    const winOverlay = document.getElementById('winOverlay');
    const colorOverlay = document.getElementById('colorOverlay');
    const toastEl = document.getElementById('toast');

    function clearTurnTimer() {
        if (turnTimer) clearTimeout(turnTimer);
        turnTimer = null;
        if (watchdogTimer) clearTimeout(watchdogTimer);
        watchdogTimer = null;
    }

    function scheduleTurn(fn, delay = 0) {
        clearTurnTimer();
        const gen = turnGen;
        turnTimer = setTimeout(() => {
            turnTimer = null;
            if (!gameActive || gen !== turnGen) return;
            fn();
        }, delay);
    }

    function bumpTurnGen() {
        turnGen++;
        clearTurnTimer();
    }

    function scheduleWatchdog() {
        if (watchdogTimer) clearTimeout(watchdogTimer);
        const gen = turnGen;
        watchdogTimer = setTimeout(() => {
            watchdogTimer = null;
            if (!gameActive || gen !== turnGen || currentPlayer === 0) return;
            aiTurn();
        }, AI_TURN_DELAY + 2500);
    }

    function showToast(msg, ms = 1800) {
        toastEl.textContent = msg;
        toastEl.classList.remove('hidden');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(() => toastEl.classList.add('hidden'), ms);
    }

    function cardLabel(c) {
        if (!c) return '';
        if (c.value === 'skip') return '⊘';
        if (c.value === 'reverse') return '⇄';
        if (c.value === 'draw2') return '+2';
        if (c.value === 'wild') return '★';
        if (c.value === 'wild4') return '+4';
        return c.value;
    }

    function cardSub(c) {
        if (c.value === 'wild') return 'WILD';
        if (c.value === 'wild4') return 'WILD';
        return '';
    }

    function buildDeck() {
        const d = [];
        let id = 0;
        COLORS.forEach(color => {
            d.push({ id: id++, color, value: '0' });
            ['1','2','3','4','5','6','7','8','9','skip','reverse','draw2'].forEach(v => {
                d.push({ id: id++, color, value: v });
                d.push({ id: id++, color, value: v });
            });
        });
        for (let i = 0; i < 4; i++) {
            d.push({ id: id++, color: 'wild', value: 'wild' });
            d.push({ id: id++, color: 'wild', value: 'wild4' });
        }
        return d;
    }

    function shuffle(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    }

    function topCard() { return discard[discard.length - 1]; }

    function canPlay(card, top, color) {
        if (!gameActive) return false;
        if (card.color === 'wild') return true;
        if (card.color === color) return true;
        if (card.value === top.value) return true;
        return false;
    }

    function drawFromDeck(n = 1) {
        const drawn = [];
        for (let i = 0; i < n; i++) {
            if (deck.length === 0) {
                if (discard.length <= 1) break;
                const top = discard.pop();
                deck = shuffle(discard);
                discard = [top];
            }
            if (deck.length) drawn.push(deck.pop());
        }
        return drawn;
    }

    function createCardEl(c, opts = {}) {
        const el = document.createElement('div');
        el.className = `uno-card ${c.color}${opts.playable ? ' playable' : ''}${opts.discard ? ' discard-top' : ''}${opts.anim ? ' deal-anim' : ''}`;
        el.innerHTML = `<span class="card-val">${cardLabel(c)}</span>${cardSub(c) ? `<span class="card-sub">${cardSub(c)}</span>` : ''}`;
        if (opts.cardId != null) el.dataset.id = c.id;
        return el;
    }

    function updateColorUI() {
        colorDot.className = `color-dot ${activeColor}`;
        colorLabel.textContent = activeColor;
    }

    function renderOpponent(i) {
        const cardsEl = document.getElementById(`opp${i}Cards`);
        const countEl = document.getElementById(`opp${i}Count`);
        const nameEl = document.querySelector(`#opp${i} .opp-name`);
        cardsEl.innerHTML = '';
        const n = Math.min(hands[i].length, 7);
        for (let j = 0; j < n; j++) {
            const b = document.createElement('div');
            b.className = 'card-back';
            cardsEl.appendChild(b);
        }
        countEl.textContent = `${hands[i].length} card${hands[i].length !== 1 ? 's' : ''}`;
        nameEl.classList.toggle('active', currentPlayer === i);
    }

    function renderAll() {
        deckCountEl.textContent = deck.length;
        updateColorUI();

        [1,2,3].forEach(renderOpponent);
        document.getElementById('playerLabel').classList.toggle('active', currentPlayer === 0);

        const top = topCard();
        discardEl.innerHTML = '';
        if (top) {
            const dc = createCardEl(top, { discard: true });
            dc.classList.add('play-anim');
            discardEl.appendChild(dc);
        }

        playerHandEl.innerHTML = '';
        hands[0].forEach(card => {
            const playable = currentPlayer === 0 && gameActive && !pendingDraw && canPlay(card, top, activeColor);
            const el = createCardEl(card, { playable, cardId: card.id });
            if (playable) {
                el.addEventListener('click', () => humanPlay(card.id));
            }
            playerHandEl.appendChild(el);
        });

        unoBtn.classList.toggle('hidden', hands[0].length !== 1 || currentPlayer !== 0 || !gameActive);
        drawBtn.disabled = !(gameActive && currentPlayer === 0);
        drawPileEl.style.pointerEvents = (gameActive && currentPlayer === 0) ? 'auto' : 'none';
    }

    function setStatus(msg) {
        statusEl.innerHTML = msg;
    }

    function nextPlayer(steps = 1) {
        currentPlayer = (currentPlayer + direction * steps + 400) % 4;
    }

    function applyCardEffect(card) {
        if (card.value === 'reverse') {
            direction *= -1;
            showToast('Reverse!');
            GameSounds.play('click');
        } else if (card.value === 'draw2') {
            pendingDraw += 2;
            showToast('+2!');
            GameSounds.play('coin');
        } else if (card.value === 'wild4') {
            pendingDraw += 4;
            showToast('+4!');
            GameSounds.play('celebrate');
        }
    }

    function stepsAfterPlay(card) {
        if (card.value === 'skip') {
            showToast('Skip!');
            GameSounds.play('tick');
            return 2;
        }
        if (card.value === 'reverse' && hands.filter(h => h.length > 0).length === 2) return 2;
        return 1;
    }

    function pickAiColor(hand) {
        const counts = { red: 0, green: 0, blue: 0, yellow: 0 };
        hand.forEach(c => { if (counts[c.color] != null) counts[c.color]++; });
        return COLORS.reduce((a, b) => counts[a] >= counts[b] ? a : b);
    }

    function playCard(player, card, chosenColor) {
        const idx = hands[player].findIndex(c => c.id === card.id);
        if (idx === -1) return false;
        hands[player].splice(idx, 1);
        discard.push(card);
        GameSounds.play('placeO');

        if (card.color === 'wild') {
            activeColor = chosenColor || pickAiColor(hands[player]);
        } else {
            activeColor = card.color;
        }

        if (player === 0 && hands[0].length === 1 && !humanUnoCalled) {
            const penalty = drawFromDeck(2);
            hands[0].push(...penalty);
            showToast('Forgot UNO! Draw 2 penalty');
            humanUnoCalled = false;
        }

        if (hands[player].length === 0) {
            endGame(player);
            return true;
        }

        applyCardEffect(card);
        humanUnoCalled = false;
        renderAll();
        return false;
    }

    function endGame(winner) {
        gameActive = false;
        if (winner === 0) {
            wins++;
            localStorage.setItem(WINS_KEY, String(wins));
            GameSounds.play('celebrate');
            document.getElementById('winTitle').textContent = '🎉 You Win!';
            document.getElementById('winMsg').textContent = `Cosmic UNO champion! Total wins: ${wins}`;
        } else {
            GameSounds.play('gameOver');
            document.getElementById('winTitle').textContent = `${PLAYER_NAMES[winner]} Wins!`;
            document.getElementById('winMsg').textContent = 'Better luck next round!';
        }
        winOverlay.classList.remove('hidden');
        setStatus(`<strong>${PLAYER_NAMES[winner]}</strong> wins the round!`);
    }

    function beginTurn() {
        if (!gameActive) return;

        if (pendingDraw > 0) {
            const p = currentPlayer;
            const n = pendingDraw;
            const drawn = drawFromDeck(n);
            hands[p].push(...drawn);
            showToast(`${PLAYER_NAMES[p]} draws ${n}!`);
            pendingDraw = 0;
            renderAll();
            nextPlayer();
            scheduleTurn(beginTurn, HUMAN_TURN_DELAY);
            return;
        }

        setStatus(currentPlayer === 0
            ? '<strong>Your turn</strong> — play a card or draw'
            : `<strong>${PLAYER_NAMES[currentPlayer]}</strong> is thinking…`);

        renderAll();

        if (currentPlayer === 0) return;

        scheduleTurn(aiTurn, AI_TURN_DELAY);
        scheduleWatchdog();
    }

    function afterPlay(card) {
        if (!gameActive) return;
        nextPlayer(stepsAfterPlay(card));
        scheduleTurn(beginTurn, HUMAN_TURN_DELAY);
    }

    function aiTurn() {
        if (!gameActive || currentPlayer === 0) return;

        const hand = hands[currentPlayer];
        const top = topCard();
        const playable = hand.filter(c => canPlay(c, top, activeColor));

        if (playable.length) {
            playable.sort((a, b) => {
                const score = c => (c.value === 'wild4' ? 5 : c.value === 'wild' ? 4 : c.value === 'draw2' ? 3 : 0);
                return score(b) - score(a);
            });
            const card = playable[0];
            if (card.color === 'wild') playCard(currentPlayer, card, pickAiColor(hand));
            else playCard(currentPlayer, card);
            if (!gameActive) return;
            afterPlay(card);
            return;
        }

        const drawn = drawFromDeck(1);
        if (drawn.length) {
            hands[currentPlayer].push(drawn[0]);
            GameSounds.play('dice');
            renderAll();
            const d = drawn[0];
            if (canPlay(d, top, activeColor)) {
                scheduleTurn(() => {
                    if (!gameActive || currentPlayer === 0) return;
                    if (d.color === 'wild') playCard(currentPlayer, d, pickAiColor(hands[currentPlayer]));
                    else playCard(currentPlayer, d);
                    if (!gameActive) return;
                    afterPlay(d);
                }, AI_ACTION_DELAY);
                return;
            }
        }
        nextPlayer();
        scheduleTurn(beginTurn, HUMAN_TURN_DELAY);
    }

    function humanPlay(cardId) {
        if (!gameActive || currentPlayer !== 0 || pendingDraw) return;
        const card = hands[0].find(c => c.id === cardId);
        const top = topCard();
        if (!card || !canPlay(card, top, activeColor)) return;

        if (card.color === 'wild') {
            pendingWildCard = card;
            colorOverlay.classList.remove('hidden');
            return;
        }

        if (playCard(0, card)) return;
        afterPlay(card);
    }

    function humanDraw() {
        if (!gameActive || currentPlayer !== 0) return;
        GameSounds.init();
        const drawn = drawFromDeck(1);
        if (!drawn.length) return;
        hands[0].push(drawn[0]);
        GameSounds.play('diceLand');
        renderAll();

        const top = topCard();
        if (canPlay(drawn[0], top, activeColor)) {
            showToast('You can play the card you drew!');
            return;
        }
        nextPlayer();
        scheduleTurn(beginTurn, HUMAN_TURN_DELAY);
    }

    function startGame() {
        bumpTurnGen();
        GameSounds.init();
        GameSounds.play('start');
        deck = shuffle(buildDeck());
        discard = [];
        hands = [[],[],[],[]];
        pendingDraw = 0;
        direction = 1;
        humanUnoCalled = false;
        gameActive = true;

        for (let r = 0; r < 7; r++) {
            for (let p = 0; p < 4; p++) hands[p].push(deck.pop());
        }

        let starter = deck.pop();
        while (starter.color === 'wild') {
            deck.push(starter);
            shuffle(deck);
            starter = deck.pop();
        }
        discard.push(starter);
        activeColor = starter.color;

        currentPlayer = Math.floor(Math.random() * 4);
        startOverlay.classList.add('hidden');
        winOverlay.classList.add('hidden');
        renderAll();
        beginTurn();
    }

    document.querySelectorAll('.color-pick-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!pendingWildCard) return;
            colorOverlay.classList.add('hidden');
            const color = btn.dataset.color;
            const card = pendingWildCard;
            pendingWildCard = null;
            if (playCard(0, card, color)) return;
            afterPlay(card);
        });
    });

    unoBtn.addEventListener('click', () => {
        if (hands[0].length === 1 && currentPlayer === 0) {
            humanUnoCalled = true;
            GameSounds.play('win');
            showToast('🃏 UNO!');
            unoBtn.classList.add('hidden');
        }
    });

    drawBtn.addEventListener('click', humanDraw);
    drawPileEl.addEventListener('click', humanDraw);
    startOverlay.querySelector('#startBtn').addEventListener('click', startGame);
    document.getElementById('playAgainBtn').addEventListener('click', startGame);
    document.getElementById('restartBtn').addEventListener('click', () => {
        if (gameActive) startGame();
        else { startOverlay.classList.remove('hidden'); winOverlay.classList.add('hidden'); }
    });
    document.getElementById('soundBtn').addEventListener('click', () => {
        GameSounds.init();
        document.getElementById('soundBtn').textContent = GameSounds.toggle() ? '🔊' : '🔇';
    });
});
</script>
@endsection
