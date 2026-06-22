@extends('layouts.app')

@section('content')
<style>
    @keyframes title-glow {
        0%, 100% { filter: drop-shadow(0 0 8px rgba(251,191,36,0.4)); }
        50% { filter: drop-shadow(0 0 18px rgba(56,189,248,0.6)); }
    }
    .plat-title { animation: title-glow 3s ease-in-out infinite; }
    .hud-pill {
        background: rgba(15, 23, 42, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        white-space: nowrap;
        flex-shrink: 0;
    }
    .hud-pill strong {
        font-variant-numeric: tabular-nums;
        display: inline-block;
        min-width: 2ch;
        text-align: center;
    }
    .hud-pill.hud-combo {
        border-color: rgba(251, 191, 36, 0.45);
        background: rgba(120, 53, 15, 0.88);
    }
    .hud-pill.hud-combo.is-off {
        opacity: 0.28;
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(15, 23, 42, 0.65);
    }
    .hud-pill.hud-combo.is-off strong {
        visibility: hidden;
    }
    .hud-pill.hud-speed {
        border-color: rgba(34, 211, 238, 0.35);
        background: rgba(8, 47, 73, 0.88);
    }
    .hud-pill.hud-combo.is-hot {
        animation: combo-pulse 0.45s ease-in-out infinite alternate;
    }
    @keyframes combo-pulse {
        from { box-shadow: 0 0 0 rgba(251, 191, 36, 0); }
        to { box-shadow: 0 0 12px rgba(251, 191, 36, 0.55); }
    }

    .sky-hud {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 20;
        pointer-events: none;
        contain: strict;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    .sky-hud.playing {
        position: fixed;
        top: var(--nav-h);
        left: 0;
        right: 0;
        height: 3.25rem;
        z-index: 40;
        background: rgba(8, 8, 18, 0.94);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
    }
    .sky-hud-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.35rem;
        padding: 0.65rem 0.75rem;
        height: 100%;
    }
    .sky-hud.playing .sky-hud-inner {
        flex-wrap: nowrap;
        padding: 0 0.4rem;
        align-items: stretch;
    }
    .sky-hud-stats {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.35rem;
        max-width: 100%;
        overflow: hidden;
    }
    .sky-hud.playing .sky-hud-title-wrap {
        display: none;
    }
    .sky-hud.playing .sky-hud-stats {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.15fr) minmax(0, 0.9fr) minmax(0, 0.85fr) minmax(0, 0.85fr) auto;
        width: 100%;
        height: 100%;
        align-items: center;
        gap: 0.2rem;
        padding: 0.35rem 0;
        overflow: visible;
    }
    .sky-hud.playing .hud-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 2rem;
        border-radius: 8px !important;
        margin: 0;
        box-shadow: none;
    }
    .sky-hud.playing #distance {
        min-width: 2.5ch;
    }
    .sky-hud:not(.playing) #speed-pill,
    .sky-hud:not(.playing) #combo-pill {
        display: none !important;
    }
    @media (max-width: 900px), (hover: none) and (pointer: coarse) {
        .hud-pill {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            font-size: 0.72rem;
            padding: 0.28rem 0.5rem;
            min-width: 2.65rem;
        }
        .sky-hud-inner {
            padding: 0.45rem 0.5rem;
        }
        .sky-hud.playing .sky-hud-stats {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 0.85fr) minmax(0, 0.75fr) minmax(0, 0.75fr) auto;
            gap: 0.15rem;
            padding: 0.3rem 0.15rem;
        }
        .sky-hud.playing {
            height: 2.85rem;
        }
        .sky-hud.playing .hud-pill {
            font-size: 0.68rem;
            padding: 0.22rem 0.25rem;
            min-height: 1.85rem;
        }
        .sky-hud.playing .sky-hud-best {
            display: none !important;
        }
        .sky-runner-page {
            height: calc(100dvh - var(--nav-h));
            min-height: calc(100dvh - var(--nav-h));
        }
        .sky-runner-stage {
            min-height: calc(100dvh - var(--nav-h));
        }
    }

    .main-content:has(.sky-runner-page) {
        max-width: none;
        padding: 0;
        width: 100%;
    }
    .sky-runner-page {
        --nav-h: 76px;
        height: calc(100svh - var(--nav-h));
        min-height: calc(100svh - var(--nav-h));
        width: 100%;
        overflow: hidden;
        background: #080812;
        -webkit-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
    }
    .sky-runner-stage {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: calc(100svh - var(--nav-h));
        overflow: hidden;
        -webkit-user-select: none;
        user-select: none;
        touch-action: none;
    }
    #platform-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: block;
        touch-action: none;
        -webkit-user-select: none;
        user-select: none;
    }
    .sky-hud {
        pointer-events: none;
    }
    .sky-hud .hud-pill,
    .sky-hud button {
        pointer-events: auto;
    }
</style>

<div class="sky-runner-page text-white">
    <div class="sky-runner-stage">
        <canvas id="platform-canvas" width="980" height="520"></canvas>

        <div class="sky-hud" id="skyHud">
            <div class="sky-hud-inner">
                <div class="sky-hud-title-wrap min-w-0">
                    <h1 class="plat-title sky-hud-title text-xl sm:text-2xl font-bold bg-gradient-to-r from-cyan-300 via-sky-200 to-amber-300 bg-clip-text text-transparent">
                        Sky Runner ✨
                    </h1>
                    <p class="desktop-controls-hint hidden sm:block text-xs text-gray-300/90">← → / A D · Space / W / ↑ jump · Double-jump!</p>
                    <p class="sm:hidden text-xs text-cyan-300/90">Use on-screen buttons · grab power-ups!</p>
                </div>
                <div class="sky-hud-stats">
                    <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">💰 <strong id="coin-count">0</strong></span>
                    <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">🏃 <strong id="distance">0</strong>m</span>
                    <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">❤️ <strong id="lives">3</strong></span>
                    <span class="hud-pill hud-combo hud-combo-slot is-off rounded-full px-2.5 py-1" id="combo-pill">🔥 <strong id="combo-count">0</strong></span>
                    <span class="hud-pill hud-speed rounded-full px-2.5 py-1" id="speed-pill">⚡ <strong id="speed-tier">1.0×</strong></span>
                    <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1 sky-hud-best">🏆 <strong id="best-score">0</strong></span>
                    <button id="sound-btn" type="button" class="hud-pill rounded-full px-2.5 sm:px-3 py-1 hover:bg-gray-700" title="Toggle sound">🔊</button>
                </div>
            </div>
        </div>

        <div id="start-overlay" class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-sm">
            <p class="text-4xl sm:text-5xl font-extrabold mb-2 bg-gradient-to-r from-cyan-400 to-amber-300 bg-clip-text text-transparent">Sky Runner</p>
            <p class="text-gray-300 mb-6 text-center max-w-sm px-4">Leap across floating islands, grab crystals, power-ups & combos — run as far as you can!</p>
            <button id="start-btn" type="button" class="rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 px-8 py-3 font-bold text-lg hover:scale-105 transition-transform shadow-lg shadow-cyan-500/30">
                Start Run →
            </button>
            <div class="mt-4 w-full max-w-xs px-4">
                @include('partials.game-leaderboard', ['game' => 'platformer', 'id' => 'platformerLeaderboard'])
            </div>
        </div>
        <div id="game-over" class="hidden absolute inset-0 z-30 flex flex-col items-center justify-center bg-red-950/85 backdrop-blur-sm">
            <p class="text-3xl font-bold text-red-200">Run Over!</p>
            <p class="text-gray-200 mt-2">💰 <span id="final-coins">0</span> crystals · 🏃 <span id="final-distance">0</span>m</p>
            <p id="new-record" class="hidden text-amber-300 font-semibold mt-1">🎉 New best distance!</p>
            <button id="retry-btn" type="button" class="mt-5 rounded-full bg-gradient-to-r from-pink-500 to-rose-600 px-8 py-3 font-bold hover:scale-105 transition-transform">Run Again</button>
        </div>
        <div id="pause-overlay" class="hidden absolute inset-0 z-30 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <p class="text-xl sm:text-2xl font-bold text-white px-4 text-center">Paused · Press P or tap Resume</p>
        </div>

        <div class="mobile-touch-controls" id="touchControls" aria-hidden="true">
            <div class="touch-cluster">
                <button type="button" class="touch-btn" id="touch-left" aria-label="Move left" unselectable="on">◀</button>
                <button type="button" class="touch-btn" id="touch-right" aria-label="Move right" unselectable="on">▶</button>
            </div>
            <button type="button" class="touch-btn touch-jump" id="touch-jump" aria-label="Jump" unselectable="on">⤒</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('platform-canvas');
    const stage = document.querySelector('.sky-runner-stage');
    const skyHud = document.getElementById('skyHud');
    const ctx = canvas.getContext('2d');
    let W = canvas.width;
    let H = canvas.height;
    let lockedStageSize = null;

    function lockStageSize() {
        const rect = stage.getBoundingClientRect();
        const vh = window.visualViewport?.height ?? window.innerHeight;
        lockedStageSize = {
            w: Math.max(320, Math.floor(rect.width) || window.innerWidth),
            h: Math.max(400, Math.floor(rect.height) || Math.round(vh - 76)),
        };
    }

    function resizeCanvas(force = false) {
        if (started && !gameOver && lockedStageSize && !force) {
            const w = lockedStageSize.w;
            const h = lockedStageSize.h;
            if (w === W && h === H) return false;
            canvas.width = w;
            canvas.height = h;
            W = w;
            H = h;
            return true;
        }
        const rect = stage.getBoundingClientRect();
        const w = Math.max(320, Math.floor(rect.width) || window.innerWidth);
        const h = Math.max(400, Math.floor(rect.height) || (window.innerHeight - 76));
        if (w === W && h === H) return false;
        canvas.width = w;
        canvas.height = h;
        W = w;
        H = h;
        return true;
    }

    const coinCountEl = document.getElementById('coin-count');
    const distanceEl = document.getElementById('distance');
    const livesEl = document.getElementById('lives');
    const bestScoreEl = document.getElementById('best-score');
    const gameOverEl = document.getElementById('game-over');
    const startOverlay = document.getElementById('start-overlay');
    const pauseOverlay = document.getElementById('pause-overlay');
    const finalCoinsEl = document.getElementById('final-coins');
    const finalDistanceEl = document.getElementById('final-distance');
    const newRecordEl = document.getElementById('new-record');
    const retryBtn = document.getElementById('retry-btn');
    const startBtn = document.getElementById('start-btn');
    const soundBtn = document.getElementById('sound-btn');
    const comboPill = document.getElementById('combo-pill');
    const comboCountEl = document.getElementById('combo-count');
    const speedTierEl = document.getElementById('speed-tier');

    const GRAVITY = 0.52;
    const MOVE_SPEED = 4.8;
    const SPEED_PER_100M = 0.08;
    const MAX_SPEED_TIER = 12;
    const JUMP_POWER = -12.2;
    const COYOTE_FRAMES_DESKTOP = 8;
    const COYOTE_FRAMES_MOBILE = 14;
    const JUMP_BUFFER_DESKTOP = 10;
    const JUMP_BUFFER_MOBILE = 20;
    const DOUBLE_JUMP_BUFFER_DESKTOP = 12;
    const DOUBLE_JUMP_BUFFER_MOBILE = 18;
    const WORLD_MAX = 50000;
    const BEST_KEY = 'sky_runner_best';
    const GEN_AHEAD = 900;
    const POWERUP_TYPES = ['shield', 'magnet', 'rocket', 'heart'];
    const MILESTONES = [100, 250, 500, 1000, 2000];

    const keys = {};
    let jumpPressed = false;
    let canDoubleJump = false;
    let coyoteTimer = 0;
    let animFrame = 0;

    let player, platforms, coins, powerups, particles, clouds, stars, shootingStars;
    let score, lives, cameraX, gameOver, paused, started;
    let lastPlatformX, furthestX, checkpoint;
    let shieldHits = 0, magnetTimer = 0, rocketTimer = 0;
    let combo = 0, comboTimer = 0, lastMilestone = 0, lastSpeedTier = 0;
    let canvasToast = { text: '', timer: 0, color: '#fff' };
    let hudCache = { score: -1, dist: -1, lives: -1, combo: -1, speed: '' };
    let jumpBuffer = 0;
    let doubleJumpBuffer = 0;
    let groundedStreak = 0;
    let lastJumpEventMs = 0;
    let bestDistance = parseInt(localStorage.getItem(BEST_KEY) || '0', 10);
    bestScoreEl.textContent = String(bestDistance);

    function resetRun() {
        resizeCanvas();
        const startY = H - Math.round(H * 0.35);
        player = {
            x: 80, y: startY, width: 28, height: 42,
            vx: 0, vy: 0, onGround: false, facing: 1,
            jumpsUsed: 0, runAnim: 0,
        };
        platforms = [];
        coins = [];
        powerups = [];
        particles = [];
        clouds = [];
        stars = [];
        shootingStars = [];
        score = 0;
        lives = 3;
        shieldHits = 0;
        magnetTimer = 0;
        rocketTimer = 0;
        combo = 0;
        comboTimer = 0;
        lastMilestone = 0;
        lastSpeedTier = 0;
        jumpBuffer = 0;
        doubleJumpBuffer = 0;
        groundedStreak = 0;
        lastJumpEventMs = 0;
        canvasToast = { text: '', timer: 0, color: '#fff' };
        hudCache = { score: -1, dist: -1, lives: -1, combo: -1, speed: '' };
        cameraX = 0;
        gameOver = false;
        paused = false;
        lastPlatformX = 0;
        furthestX = 0;
        checkpoint = { x: 80, y: startY };
        coyoteTimer = 0;
        canDoubleJump = false;
        jumpPressed = false;

        for (let i = 0; i < 40; i++) {
            stars.push({ x: Math.random() * W * 3, y: Math.random() * H * 0.55, r: Math.random() * 1.5 + 0.4, tw: Math.random() * Math.PI * 2 });
        }
        for (let i = 0; i < 12; i++) {
            clouds.push({ x: Math.random() * W * 2, y: 40 + Math.random() * 120, w: 60 + Math.random() * 80, s: 0.15 + Math.random() * 0.25 });
        }

        platforms.push({ x: -20, y: H - 60, width: 320, height: 60, type: 'ground' });
        lastPlatformX = 300;
        extendLevel(1200);

        gameOverEl.classList.add('hidden');
        pauseOverlay.classList.add('hidden');
        updateHud();
    }

    function extendLevel(targetX) {
        while (lastPlatformX < targetX) {
            const gap = 70 + Math.random() * 90;
            const w = 90 + Math.random() * 100;
            const rise = (Math.random() - 0.45) * 90;
            let y = platforms[platforms.length - 1].y + rise;
            y = Math.max(180, Math.min(H - 120, y));

            const x = lastPlatformX + gap;
            const roll = Math.random();
            let type = 'normal';
            if (roll < 0.1) type = 'spring';
            else if (roll < 0.14) type = 'cloud';
            else if (roll < 0.17 && furthestX > 400) type = 'rainbow';
            platforms.push({ x, y, width: w, height: 18, type, rainbowPhase: 0 });

            if (Math.random() < 0.75) {
                const count = 1 + Math.floor(Math.random() * 3);
                for (let c = 0; c < count; c++) {
                    coins.push({
                        x: x + 20 + c * 28,
                        y: y - 32 - (c % 2) * 18,
                        radius: 9,
                        collected: false,
                        spin: Math.random() * Math.PI * 2,
                        kind: Math.random() < 0.12 ? 'gem' : 'coin',
                    });
                }
            }
            if (Math.random() < 0.09 && furthestX > 150) {
                const kind = POWERUP_TYPES[Math.floor(Math.random() * POWERUP_TYPES.length)];
                powerups.push({
                    x: x + w * 0.5,
                    y: y - 48,
                    radius: 11,
                    kind,
                    spin: Math.random() * Math.PI * 2,
                    collected: false,
                });
            }
            lastPlatformX = x + w;
        }
    }

    function pruneWorld() {
        const cut = cameraX - 400;
        platforms = platforms.filter(p => p.x + p.width > cut);
        coins = coins.filter(c => !c.collected && c.x > cut - 50);
        powerups = powerups.filter(p => !p.collected && p.x > cut - 50);
    }

    function showCanvasToast(text, color = '#fef08a') {
        canvasToast = { text, timer: 110, color };
    }

    function getDistanceM() {
        return Math.floor(furthestX / 10);
    }

    function getSpeedTier() {
        return Math.min(MAX_SPEED_TIER, Math.floor(getDistanceM() / 100));
    }

    function getSpeedMultiplier() {
        return 1 + getSpeedTier() * SPEED_PER_100M;
    }

    function getMoveSpeed() {
        let speed = MOVE_SPEED * getSpeedMultiplier();
        if (rocketTimer > 0) speed *= 1.35;
        return speed;
    }

    function updateHud(force = false) {
        const dist = getDistanceM();
        const speedLabel = getSpeedMultiplier().toFixed(1) + '×';
        if (force || hudCache.score !== score) {
            coinCountEl.textContent = String(score);
            hudCache.score = score;
        }
        if (force || hudCache.dist !== dist) {
            distanceEl.textContent = String(dist);
            hudCache.dist = dist;
        }
        if (force || hudCache.lives !== lives) {
            livesEl.textContent = String(lives);
            hudCache.lives = lives;
        }
        if (force || hudCache.speed !== speedLabel) {
            if (speedTierEl) speedTierEl.textContent = speedLabel;
            hudCache.speed = speedLabel;
        }
        if (force || hudCache.combo !== combo) {
            if (combo >= 2) {
                comboPill.classList.remove('is-off');
                comboCountEl.textContent = String(combo);
                comboPill.classList.toggle('is-hot', combo >= 5);
            } else {
                comboPill.classList.add('is-off');
                comboPill.classList.remove('is-hot');
                comboCountEl.textContent = '0';
            }
            hudCache.combo = combo;
        }
    }

    function registerCombo() {
        combo += 1;
        comboTimer = 150;
        if (combo === 5) showCanvasToast('🔥 COMBO x5!', '#fb923c');
        if (combo === 10) showCanvasToast('⚡ MEGA COMBO!', '#f472b6');
    }

    function breakCombo() {
        combo = 0;
        comboTimer = 0;
    }

    function collectPowerup(pu) {
        pu.collected = true;
        const labels = {
            shield: '🛡️ Shield!',
            magnet: '🧲 Magnet!',
            rocket: '🚀 Rocket boost!',
            heart: '💖 Extra life!',
        };
        showCanvasToast(labels[pu.kind] || 'Power-up!', '#a5f3fc');
        GameSounds.play('coin');
        if (pu.kind === 'shield') shieldHits = Math.min(2, shieldHits + 1);
        if (pu.kind === 'magnet') magnetTimer = 300;
        if (pu.kind === 'rocket') rocketTimer = 240;
        if (pu.kind === 'heart') {
            lives = Math.min(5, lives + 1);
            for (let i = 0; i < 12; i++) {
                particles.push({
                    x: player.x - cameraX + player.width / 2,
                    y: player.y + player.height / 2,
                    vx: (Math.random() - 0.5) * 5,
                    vy: -Math.random() * 4,
                    life: 1.2, color: '#fb7185', size: 4,
                });
            }
        }
        updateHud(true);
    }

    function isMobilePlay() {
        return window.innerWidth <= 900
            || window.matchMedia('(hover: none) and (pointer: coarse)').matches;
    }

    function coyoteMax() {
        return isMobilePlay() ? COYOTE_FRAMES_MOBILE : COYOTE_FRAMES_DESKTOP;
    }

    function queueJump() {
        if (gameOver || paused || !started) return;

        const wantsAirJump = player.jumpsUsed === 1 && canDoubleJump;
        if (wantsAirJump) {
            doubleJumpBuffer = isMobilePlay() ? DOUBLE_JUMP_BUFFER_MOBILE : DOUBLE_JUMP_BUFFER_DESKTOP;
            tryJump();
            return;
        }

        jumpBuffer = isMobilePlay() ? JUMP_BUFFER_MOBILE : JUMP_BUFFER_DESKTOP;
        tryJump();
    }

    function processJumpBuffer() {
        if (doubleJumpBuffer > 0) {
            if (tryJump()) {
                doubleJumpBuffer = 0;
            } else {
                doubleJumpBuffer--;
            }
        }
        if (jumpBuffer <= 0) return;
        if (tryJump()) {
            if (player.jumpsUsed !== 1 || !canDoubleJump) {
                jumpBuffer = 0;
            }
            return;
        }
        jumpBuffer--;
    }

    function tryJump() {
        if (gameOver || paused || !started) return false;
        const canGroundJump = (player.onGround || coyoteTimer > 0) && player.jumpsUsed === 0;
        if (canGroundJump) {
            player.vy = JUMP_POWER;
            player.onGround = false;
            player.jumpsUsed = 1;
            coyoteTimer = 0;
            groundedStreak = 0;
            canDoubleJump = true;
            GameSounds.play('jump');
            spawnDust(player.x + player.width / 2 - cameraX, player.y + player.height);
            return true;
        }
        const canAirJump = canDoubleJump && player.jumpsUsed === 1 && !player.onGround;
        if (canAirJump) {
            player.vy = JUMP_POWER * 0.88;
            player.jumpsUsed = 2;
            canDoubleJump = false;
            doubleJumpBuffer = 0;
            GameSounds.play('jump');
            for (let i = 0; i < 6; i++) {
                particles.push({
                    x: player.x - cameraX + player.width / 2,
                    y: player.y + player.height / 2,
                    vx: (Math.random() - 0.5) * 4,
                    vy: Math.random() * 2,
                    life: 1, color: '#67e8f9', size: 4,
                });
            }
            return true;
        }
        return false;
    }

    function spawnDust(wx, wy) {
        for (let i = 0; i < 5; i++) {
            particles.push({
                x: wx, y: wy,
                vx: (Math.random() - 0.5) * 3,
                vy: -Math.random() * 2,
                life: 0.8, color: '#94a3b8', size: 3,
            });
        }
    }

    function spawnCoinBurst(wx, wy, color) {
        for (let i = 0; i < 10; i++) {
            particles.push({
                x: wx, y: wy,
                vx: (Math.random() - 0.5) * 7,
                vy: -Math.random() * 6 - 1,
                life: 1, color, size: 3 + Math.random() * 2,
            });
        }
    }

    function handleInput() {
        if (gameOver || paused || !started) return;

        const speed = getMoveSpeed();
        if (keys['ArrowLeft'] || keys['a'] || keys['A']) player.vx = -speed;
        else if (keys['ArrowRight'] || keys['d'] || keys['D']) player.vx = speed;
        else player.vx *= 0.72;

        if (player.vx !== 0) player.facing = player.vx > 0 ? 1 : -1;
        if (player.onGround && Math.abs(player.vx) > 0.5) player.runAnim += 0.25;
    }

    function collidePlatforms() {
        player.onGround = false;

        for (const p of platforms) {
            if (player.x + player.width <= p.x || player.x >= p.x + p.width ||
                player.y + player.height <= p.y || player.y >= p.y + p.height) continue;

            const overlapTop = (player.y + player.height) - p.y;
            const overlapBottom = (p.y + p.height) - player.y;
            const overlapLeft = (player.x + player.width) - p.x;
            const overlapRight = (p.x + p.width) - player.x;
            const minOverlap = Math.min(overlapTop, overlapBottom, overlapLeft, overlapRight);

            if (minOverlap === overlapTop && player.vy >= 0) {
                player.y = p.y - player.height;
                checkpoint = { x: Math.max(checkpoint.x, player.x), y: p.y - player.height };

                if (p.type === 'spring') {
                    player.vy = JUMP_POWER * 1.35;
                    player.onGround = false;
                    player.jumpsUsed = 1;
                    canDoubleJump = true;
                    groundedStreak = 0;
                    coyoteTimer = 0;
                    GameSounds.play('jump');
                    spawnDust(player.x + player.width / 2 - cameraX, player.y + player.height);
                } else if (p.type === 'cloud') {
                    player.vy = JUMP_POWER * 0.55;
                    player.onGround = false;
                    player.jumpsUsed = 0;
                    canDoubleJump = true;
                    groundedStreak = 0;
                    coyoteTimer = coyoteMax();
                    GameSounds.play('jump');
                    spawnDust(player.x + player.width / 2 - cameraX, player.y + player.height);
                } else {
                    player.vy = 0;
                    player.onGround = true;
                    coyoteTimer = coyoteMax();
                }
            } else if (minOverlap === overlapBottom && player.vy < 0) {
                player.y = p.y + p.height;
                player.vy = 0;
            } else if (minOverlap === overlapLeft && player.vx > 0) {
                player.x = p.x - player.width;
                player.vx = 0;
            } else if (minOverlap === overlapRight && player.vx < 0) {
                player.x = p.x + p.width;
                player.vx = 0;
            }
        }

        if (!player.onGround && coyoteTimer > 0) coyoteTimer--;

        if (player.onGround) {
            groundedStreak++;
            if (groundedStreak >= 2) {
                player.jumpsUsed = 0;
                canDoubleJump = false;
                doubleJumpBuffer = 0;
            }
        } else {
            groundedStreak = 0;
        }
    }

    function updatePhysics() {
        if (gameOver || paused || !started) return;

        if (magnetTimer > 0) magnetTimer--;
        if (rocketTimer > 0) rocketTimer--;
        if (comboTimer > 0) {
            comboTimer--;
            if (comboTimer <= 0) breakCombo();
        }
        if (canvasToast.timer > 0) canvasToast.timer--;

        player.vy += GRAVITY;
        player.x += player.vx;
        player.y += player.vy;

        if (player.x < 0) player.x = 0;
        if (player.x > WORLD_MAX) player.x = WORLD_MAX;

        collidePlatforms();
        processJumpBuffer();

        platforms.forEach(p => {
            if (p.type === 'rainbow') {
                p.rainbowPhase = (p.rainbowPhase || 0) + 0.04;
                if (p.rainbowPhase > Math.PI * 2) p.rainbowPhase = 0;
            }
        });

        if (player.y > H + 100) {
            loseLife();
        }

        const prevFurthest = furthestX;
        furthestX = Math.max(furthestX, player.x);
        const distM = getDistanceM();
        const speedTier = getSpeedTier();
        if (speedTier > lastSpeedTier) {
            lastSpeedTier = speedTier;
            showCanvasToast(`⚡ ${speedTier * 100}m — Speed ${getSpeedMultiplier().toFixed(1)}×!`, '#67e8f9');
            GameSounds.play('coin');
            updateHud();
        }
        if (distM > lastMilestone) {
            const hit = MILESTONES.find(m => m > lastMilestone && m <= distM);
            if (hit) {
                lastMilestone = hit;
                showCanvasToast(`🌟 ${hit}m milestone!`, '#fde047');
                GameSounds.play('coin');
            }
        }
        const pace = getSpeedMultiplier();
        if (furthestX - prevFurthest > 0.5 && Math.random() < 0.008 * pace) {
            shootingStars.push({
                x: cameraX + W + 40,
                y: 30 + Math.random() * H * 0.4,
                len: 40 + Math.random() * 60,
                speed: (12 + Math.random() * 8) * pace,
                life: 1,
            });
        }

        if (player.x + GEN_AHEAD > lastPlatformX) extendLevel(player.x + GEN_AHEAD);
        pruneWorld();

        const camLead = 0.55 + Math.min(0.1, speedTier * 0.008);
        if (player.x > cameraX + W * camLead) cameraX = player.x - W * camLead;
        if (player.x < cameraX + W * 0.2) cameraX = Math.max(0, player.x - W * 0.2);

        if (magnetTimer > 0) {
            coins.forEach(coin => {
                if (coin.collected) return;
                const cx = coin.x + coin.radius;
                const cy = coin.y + coin.radius;
                const px = player.x + player.width / 2;
                const py = player.y + player.height / 2;
                const dx = px - cx;
                const dy = py - cy;
                const dist = Math.hypot(dx, dy);
                if (dist < 220 && dist > 4) {
                    coin.x += (dx / dist) * 5;
                    coin.y += (dy / dist) * 5;
                }
            });
        }

        let collectedThisFrame = false;
        coins.forEach(coin => {
            if (coin.collected) return;
            coin.spin += 0.08;
            const cx = coin.x + coin.radius;
            const cy = coin.y + coin.radius;
            const dx = (player.x + player.width / 2) - cx;
            const dy = (player.y + player.height / 2) - cy;
            if (Math.hypot(dx, dy) < coin.radius + 20) {
                coin.collected = true;
                const mult = Math.max(1, combo);
                const gain = (coin.kind === 'gem' ? 5 : 1) * mult;
                score += gain;
                collectedThisFrame = true;
                registerCombo();
                GameSounds.play('coin');
                spawnCoinBurst(cx - cameraX, cy, coin.kind === 'gem' ? '#c084fc' : '#facc15');
            }
        });
        if (collectedThisFrame) updateHud();

        powerups.forEach(pu => {
            if (pu.collected) return;
            pu.spin += 0.06;
            const dx = (player.x + player.width / 2) - pu.x;
            const dy = (player.y + player.height / 2) - pu.y;
            if (Math.hypot(dx, dy) < pu.radius + 22) {
                collectPowerup(pu);
            }
        });

        if (rocketTimer > 0 && animFrame % 3 === 0) {
            particles.push({
                x: player.x - cameraX + player.width / 2,
                y: player.y + player.height - 4,
                vx: (Math.random() - 0.5) * 2,
                vy: Math.random() * 2,
                life: 0.5, color: '#22d3ee', size: 3,
            });
        }

        particles = particles.filter(p => {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.15;
            p.life -= 0.035;
            return p.life > 0;
        });

        shootingStars = shootingStars.filter(s => {
            s.x -= s.speed;
            s.y += s.speed * 0.35;
            s.life -= 0.02;
            return s.life > 0 && s.x > cameraX - 80;
        });

        clouds.forEach(c => {
            c.x -= c.s * pace;
            if (c.x + c.w < cameraX * 0.1 - 100) c.x = cameraX * 0.1 + W + Math.random() * 200;
        });

        if (hudCache.dist !== distM || hudCache.speed !== getSpeedMultiplier().toFixed(1) + '×') {
            updateHud();
        }
    }

    function loseLife() {
        if (shieldHits > 0) {
            shieldHits--;
            showCanvasToast('🛡️ Shield saved you!', '#7dd3fc');
            GameSounds.play('click');
            player.x = checkpoint.x;
            player.y = checkpoint.y;
            player.vx = 0;
            player.vy = JUMP_POWER * 0.7;
            player.jumpsUsed = 1;
            canDoubleJump = true;
            groundedStreak = 0;
            cameraX = Math.max(0, player.x - W * 0.35);
            breakCombo();
            return;
        }
        GameSounds.play('hurt');
        lives -= 1;
        breakCombo();
        if (lives <= 0) {
            endRun();
            return;
        }
        player.x = checkpoint.x;
        player.y = checkpoint.y;
        player.vx = 0;
        player.vy = 0;
        player.jumpsUsed = 0;
        cameraX = Math.max(0, player.x - W * 0.35);
        updateHud(true);
    }

    function endRun() {
        gameOver = true;
        lockedStageSize = null;
        skyHud?.classList.remove('playing');
        GameSounds.play('gameOver');
        const dist = Math.floor(furthestX / 10);
        finalCoinsEl.textContent = String(score);
        finalDistanceEl.textContent = String(dist);
        if (dist > bestDistance) {
            bestDistance = dist;
            localStorage.setItem(BEST_KEY, String(bestDistance));
            bestScoreEl.textContent = String(bestDistance);
            newRecordEl.classList.remove('hidden');
        } else {
            newRecordEl.classList.add('hidden');
        }
        @auth
        if (typeof GameLeaderboard !== 'undefined') {
            GameLeaderboard.submit('platformer', bestDistance).catch(() => {});
        }
        @endauth
        gameOverEl.classList.remove('hidden');
    }

    /* ── DRAW ── */
    function drawBackground() {
        const sky = ctx.createLinearGradient(0, 0, 0, H);
        sky.addColorStop(0, '#0c1445');
        sky.addColorStop(0.4, '#1e3a5f');
        sky.addColorStop(0.75, '#312e81');
        sky.addColorStop(1, '#4c1d95');
        ctx.fillStyle = sky;
        ctx.fillRect(0, 0, W, H);

        stars.forEach(s => {
            const sx = ((s.x - cameraX * 0.05) % (W + 40) + W + 40) % (W + 40) - 20;
            ctx.globalAlpha = 0.4 + Math.sin(animFrame * 0.05 + s.tw) * 0.3;
            ctx.fillStyle = '#e0f2fe';
            ctx.beginPath();
            ctx.arc(sx, s.y, s.r, 0, Math.PI * 2);
            ctx.fill();
        });
        ctx.globalAlpha = 1;

        ctx.fillStyle = 'rgba(30,58,138,0.35)';
        for (let i = 0; i < 5; i++) {
            const mx = ((i * 280 - cameraX * 0.12) % (W + 200) + W + 200) % (W + 200) - 100;
            ctx.beginPath();
            ctx.moveTo(mx, H);
            ctx.lineTo(mx + 120, H - 80 - i * 15);
            ctx.lineTo(mx + 260, H);
            ctx.fill();
        }

        clouds.forEach(c => {
            const cx = c.x - cameraX * c.s * 0.4;
            ctx.fillStyle = 'rgba(255,255,255,0.12)';
            ctx.beginPath();
            ctx.ellipse(cx, c.y, c.w * 0.5, 22, 0, 0, Math.PI * 2);
            ctx.ellipse(cx + c.w * 0.3, c.y - 8, c.w * 0.35, 18, 0, 0, Math.PI * 2);
            ctx.ellipse(cx - c.w * 0.25, c.y - 4, c.w * 0.3, 16, 0, 0, Math.PI * 2);
            ctx.fill();
        });
    }

    function drawPlatforms() {
        platforms.forEach(p => {
            const sx = p.x - cameraX;
            if (sx + p.width < -20 || sx > W + 20) return;

            const depth = p.type === 'ground' ? 50 : 12;
            let topColor = '#4ade80';
            let sideColor = '#16a34a';
            if (p.type === 'spring') { topColor = '#a78bfa'; sideColor = '#7c3aed'; }
            else if (p.type === 'ground') { topColor = '#365314'; sideColor = '#1a2e05'; }
            else if (p.type === 'cloud') { topColor = '#e0f2fe'; sideColor = '#7dd3fc'; }
            else if (p.type === 'rainbow') {
                const hue = ((p.rainbowPhase || 0) * 57) % 360;
                topColor = `hsl(${hue}, 85%, 65%)`;
                sideColor = `hsl(${(hue + 40) % 360}, 75%, 45%)`;
            }

            ctx.fillStyle = sideColor;
            ctx.fillRect(sx + 4, p.y + 4, p.width, depth);

            const grad = ctx.createLinearGradient(sx, p.y, sx, p.y + p.height);
            grad.addColorStop(0, topColor);
            grad.addColorStop(1, sideColor);
            ctx.fillStyle = grad;
            ctx.fillRect(sx, p.y, p.width, p.height);

            if (p.type !== 'ground') {
                ctx.fillStyle = '#86efac';
                ctx.fillRect(sx, p.y, p.width, 5);
            }
            if (p.type === 'spring') {
                ctx.fillStyle = '#ede9fe';
                ctx.font = 'bold 11px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('⬆ BOOST', sx + p.width / 2, p.y + p.height / 2 + 4);
            } else if (p.type === 'cloud') {
                ctx.fillStyle = 'rgba(255,255,255,0.85)';
                ctx.font = 'bold 10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('☁ SOFT', sx + p.width / 2, p.y + p.height / 2 + 4);
            } else if (p.type === 'rainbow') {
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('✦ RARE', sx + p.width / 2, p.y + p.height / 2 + 4);
            }
        });
    }

    function drawPowerups() {
        const icons = { shield: '🛡', magnet: '🧲', rocket: '🚀', heart: '💖' };
        const colors = { shield: '#38bdf8', magnet: '#a78bfa', rocket: '#fb923c', heart: '#fb7185' };
        powerups.forEach(pu => {
            if (pu.collected) return;
            const sx = pu.x - cameraX;
            if (sx < -30 || sx > W + 30) return;
            const bob = Math.sin(pu.spin) * 4;
            ctx.save();
            ctx.shadowColor = colors[pu.kind] || '#fff';
            ctx.shadowBlur = 14;
            ctx.fillStyle = colors[pu.kind] || '#fff';
            ctx.beginPath();
            ctx.arc(sx, pu.y + bob, pu.radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0;
            ctx.font = `${pu.radius + 4}px Arial`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(icons[pu.kind] || '★', sx, pu.y + bob);
            ctx.restore();
        });
    }

    function drawShootingStars() {
        shootingStars.forEach(s => {
            const sx = s.x - cameraX;
            if (sx < -100 || sx > W + 100) return;
            ctx.save();
            ctx.globalAlpha = s.life * 0.9;
            ctx.strokeStyle = '#fef9c3';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(sx, s.y);
            ctx.lineTo(sx + s.len, s.y - s.len * 0.35);
            ctx.stroke();
            ctx.restore();
        });
    }

    function drawCanvasToast() {
        if (canvasToast.timer <= 0 || !canvasToast.text) return;
        ctx.save();
        ctx.globalAlpha = Math.min(1, canvasToast.timer / 30);
        ctx.font = 'bold 18px Orbitron, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillStyle = 'rgba(0,0,0,0.45)';
        const tw = ctx.measureText(canvasToast.text).width + 28;
        const tx = W / 2;
        const ty = H * 0.22;
        ctx.beginPath();
        ctx.roundRect(tx - tw / 2, ty - 16, tw, 32, 16);
        ctx.fill();
        ctx.fillStyle = canvasToast.color;
        ctx.fillText(canvasToast.text, tx, ty + 6);
        ctx.restore();
    }

    function drawActiveBuffs() {
        const items = [];
        if (shieldHits > 0) items.push(`🛡×${shieldHits}`);
        if (magnetTimer > 0) items.push('🧲');
        if (rocketTimer > 0) items.push('🚀');
        if (!items.length) return;
        ctx.save();
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'left';
        ctx.fillStyle = 'rgba(15,23,42,0.75)';
        ctx.fillRect(10, H - 38, items.length * 34 + 8, 28);
        items.forEach((label, i) => {
            ctx.fillStyle = '#e0f2fe';
            ctx.fillText(label, 18 + i * 34, H - 18);
        });
        ctx.restore();
    }

    function drawCoins() {
        coins.forEach(coin => {
            if (coin.collected) return;
            const sx = coin.x - cameraX;
            if (sx < -30 || sx > W + 30) return;
            const cx = sx + coin.radius;
            const cy = coin.y + coin.radius;
            const bob = Math.sin(coin.spin) * 3;

            ctx.save();
            ctx.shadowColor = coin.kind === 'gem' ? '#c084fc' : '#fbbf24';
            ctx.shadowBlur = 12;
            ctx.translate(cx, cy + bob);
            ctx.rotate(Math.sin(coin.spin) * 0.3);

            if (coin.kind === 'gem') {
                ctx.fillStyle = '#a855f7';
                ctx.beginPath();
                ctx.moveTo(0, -coin.radius);
                ctx.lineTo(coin.radius * 0.8, 0);
                ctx.lineTo(0, coin.radius);
                ctx.lineTo(-coin.radius * 0.8, 0);
                ctx.closePath();
                ctx.fill();
                ctx.fillStyle = '#e9d5ff';
                ctx.fillRect(-2, -4, 4, 8);
            } else {
                ctx.fillStyle = '#fbbf24';
                ctx.beginPath();
                ctx.arc(0, 0, coin.radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = '#fef08a';
                ctx.fillRect(-3, -2, 6, 4);
            }
            ctx.restore();
        });
    }

    function drawParticles() {
        particles.forEach(p => {
            ctx.globalAlpha = p.life;
            ctx.fillStyle = p.color;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size || 3, 0, Math.PI * 2);
            ctx.fill();
        });
        ctx.globalAlpha = 1;
    }

    function drawPlayer() {
        const sx = player.x - cameraX;
        const sy = player.y;
        ctx.save();
        ctx.translate(sx + player.width / 2, sy);

        if (player.facing < 0) ctx.scale(-1, 1);

        const bob = player.onGround ? Math.sin(player.runAnim) * 2 : 0;
        ctx.translate(0, bob);

        ctx.fillStyle = 'rgba(0,0,0,0.25)';
        ctx.beginPath();
        ctx.ellipse(0, player.height + 2, 14, 5, 0, 0, Math.PI * 2);
        ctx.fill();

        const bodyGrad = ctx.createLinearGradient(-14, 0, 14, player.height);
        bodyGrad.addColorStop(0, '#38bdf8');
        bodyGrad.addColorStop(1, '#0284c7');
        ctx.fillStyle = bodyGrad;
        ctx.beginPath();
        ctx.roundRect(-13, 8, 26, 28, 8);
        ctx.fill();

        ctx.fillStyle = '#f8fafc';
        ctx.beginPath();
        ctx.arc(0, 2, 13, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#0f172a';
        ctx.beginPath();
        ctx.arc(5, 0, 3, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#fff';
        ctx.beginPath();
        ctx.arc(6, -1, 1, 0, Math.PI * 2);
        ctx.fill();

        const legSwing = player.onGround ? Math.sin(player.runAnim) * 6 : 0;
        ctx.fillStyle = '#0369a1';
        ctx.fillRect(-8, 34, 6, 10 + legSwing * 0.3);
        ctx.fillRect(2, 34, 6, 10 - legSwing * 0.3);

        if (shieldHits > 0) {
            ctx.globalAlpha = 0.35 + Math.sin(animFrame * 0.12) * 0.15;
            ctx.strokeStyle = '#38bdf8';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.arc(0, player.height / 2, 24, 0, Math.PI * 2);
            ctx.stroke();
            ctx.globalAlpha = 1;
        }
        if (rocketTimer > 0) {
            ctx.globalAlpha = 0.6;
            ctx.fillStyle = '#22d3ee';
            ctx.beginPath();
            ctx.moveTo(-6, player.height + 2);
            ctx.lineTo(0, player.height + 14 + Math.sin(animFrame * 0.4) * 4);
            ctx.lineTo(6, player.height + 2);
            ctx.fill();
            ctx.globalAlpha = 1;
        }

        if (!player.onGround && player.jumpsUsed >= 2) {
            ctx.globalAlpha = 0.5 + Math.sin(animFrame * 0.3) * 0.3;
            ctx.fillStyle = '#67e8f9';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('✦', 0, -10);
            ctx.globalAlpha = 1;
        }

        ctx.restore();
    }

    function gameLoop() {
        animFrame++;
        handleInput();
        updatePhysics();

        drawBackground();
        drawShootingStars();
        drawPlatforms();
        drawCoins();
        drawPowerups();
        drawParticles();
        drawPlayer();
        drawActiveBuffs();
        drawCanvasToast();

        requestAnimationFrame(gameLoop);
    }

    function startRun() {
        GameSounds.init();
        GameSounds.play('start');
        lockStageSize();
        resizeCanvas(true);
        started = true;
        skyHud?.classList.add('playing');
        startOverlay.classList.add('hidden');
        resetRun();
    }

    startBtn.addEventListener('click', startRun);
    retryBtn.addEventListener('click', () => {
        GameSounds.play('click');
        lockStageSize();
        resizeCanvas(true);
        started = true;
        skyHud?.classList.add('playing');
        resetRun();
    });
    soundBtn.addEventListener('click', () => {
        GameSounds.init();
        soundBtn.textContent = GameSounds.toggle() ? '🔊' : '🔇';
    });

    window.addEventListener('keydown', e => {
        GameSounds.init();
        if (e.key === 'p' || e.key === 'P') {
            if (started && !gameOver) {
                paused = !paused;
                pauseOverlay.classList.toggle('hidden', !paused);
                if (!paused) GameSounds.play('click');
            }
            return;
        }
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', ' ', 'w', 'a', 's', 'd'].includes(e.key)) e.preventDefault();
        if (!keys[e.key]) {
            keys[e.key] = true;
            if ([' ', 'ArrowUp', 'w', 'W'].includes(e.key)) queueJump();
        }
    });

    window.addEventListener('keyup', e => { keys[e.key] = false; });

    function bindTouchBtn(el, onDown, onUp, releaseOnLeave = true) {
        if (!el) return;
        const blockSelect = (e) => e.preventDefault();
        el.addEventListener('selectstart', blockSelect);
        el.addEventListener('contextmenu', blockSelect);
        let active = false;
        const down = (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (active) return;
            active = true;
            el.classList.add('active');
            if (el.setPointerCapture && e.pointerId !== undefined) {
                try { el.setPointerCapture(e.pointerId); } catch (_) {}
            }
            onDown();
        };
        const up = (e) => {
            if (!active) return;
            if (e) e.preventDefault();
            active = false;
            el.classList.remove('active');
            onUp();
        };
        el.addEventListener('pointerdown', down, { passive: false });
        el.addEventListener('pointerup', up, { passive: false });
        el.addEventListener('pointercancel', up, { passive: false });
        if (releaseOnLeave) {
            el.addEventListener('pointerleave', up, { passive: false });
        }
    }

    function bindJumpBtn(el) {
        if (!el) return;
        const blockSelect = (e) => e.preventDefault();
        el.addEventListener('selectstart', blockSelect);
        el.addEventListener('contextmenu', blockSelect);
        let pressed = false;

        const onPress = (e) => {
            e.preventDefault();
            e.stopPropagation();
            const now = performance.now();
            if (now - lastJumpEventMs < 32) return;
            lastJumpEventMs = now;
            if (pressed) return;
            pressed = true;
            el.classList.add('active');
            queueJump();
        };
        const onRelease = (e) => {
            if (e) e.preventDefault();
            pressed = false;
            el.classList.remove('active');
        };

        el.addEventListener('pointerdown', onPress, { passive: false });
        el.addEventListener('pointerup', onRelease, { passive: false });
        el.addEventListener('pointercancel', onRelease, { passive: false });
        el.addEventListener('touchend', onRelease, { passive: false });
        el.addEventListener('touchcancel', onRelease, { passive: false });
    }

    const touchControls = document.getElementById('touchControls');
    if (touchControls) {
        touchControls.addEventListener('contextmenu', (e) => e.preventDefault());
        touchControls.addEventListener('selectstart', (e) => e.preventDefault());
    }
    stage.addEventListener('contextmenu', (e) => e.preventDefault());

    bindTouchBtn(document.getElementById('touch-left'), () => { keys['ArrowLeft'] = true; }, () => { keys['ArrowLeft'] = false; });
    bindTouchBtn(document.getElementById('touch-right'), () => { keys['ArrowRight'] = true; }, () => { keys['ArrowRight'] = false; });
    bindJumpBtn(document.getElementById('touch-jump'));

    let resizeTimer;
    window.addEventListener('resize', () => {
        if (started && !gameOver) return;
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => resizeCanvas(), 100);
    });
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            if (started && !gameOver) return;
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => resizeCanvas(), 100);
        });
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            resetRun();
            started = false;
            gameLoop();
        });
    });
});
</script>
@endsection
