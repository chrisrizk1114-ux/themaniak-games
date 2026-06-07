@extends('layouts.app')

@section('content')
<style>
    @keyframes title-glow {
        0%, 100% { filter: drop-shadow(0 0 8px rgba(251,191,36,0.4)); }
        50% { filter: drop-shadow(0 0 18px rgba(56,189,248,0.6)); }
    }
    .plat-title { animation: title-glow 3s ease-in-out infinite; }
    .hud-pill { background: rgba(15,23,42,0.82); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.12); }

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
    }
    .sky-runner-stage {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: calc(100svh - var(--nav-h));
        overflow: hidden;
    }
    #platform-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: block;
    }
    .sky-hud {
        pointer-events: none;
    }
    .sky-hud > * {
        pointer-events: auto;
    }
</style>

<div class="sky-runner-page text-white">
    <div class="sky-runner-stage">
        <canvas id="platform-canvas" width="980" height="520"></canvas>

        <div class="sky-hud absolute top-0 left-0 right-0 z-20 flex flex-wrap items-start justify-between gap-2 p-3 sm:p-4">
            <div class="min-w-0">
                <h1 class="plat-title text-xl sm:text-2xl font-bold bg-gradient-to-r from-cyan-300 via-sky-200 to-amber-300 bg-clip-text text-transparent">
                    Sky Runner ✨
                </h1>
                <p class="desktop-controls-hint hidden sm:block text-xs text-gray-300/90">← → / A D · Space / W / ↑ jump · Double-jump!</p>
                <p class="sm:hidden text-xs text-cyan-300/90">Use on-screen buttons to move & jump</p>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-gray-100">
                <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">💰 <strong id="coin-count">0</strong></span>
                <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">🏃 <strong id="distance">0</strong>m</span>
                <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">❤️ <strong id="lives">3</strong></span>
                <span class="hud-pill rounded-full px-2.5 sm:px-3 py-1">🏆 <strong id="best-score">0</strong></span>
                <button id="sound-btn" type="button" class="hud-pill rounded-full px-2.5 sm:px-3 py-1 hover:bg-gray-700" title="Toggle sound">🔊</button>
            </div>
        </div>

        <div id="start-overlay" class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-sm">
            <p class="text-4xl sm:text-5xl font-extrabold mb-2 bg-gradient-to-r from-cyan-400 to-amber-300 bg-clip-text text-transparent">Sky Runner</p>
            <p class="text-gray-300 mb-6 text-center max-w-sm px-4">Leap across floating islands, grab crystals, and run as far as you can!</p>
            <button id="start-btn" type="button" class="rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 px-8 py-3 font-bold text-lg hover:scale-105 transition-transform shadow-lg shadow-cyan-500/30">
                Start Run →
            </button>
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
                <button type="button" class="touch-btn" id="touch-left" aria-label="Move left">←</button>
                <button type="button" class="touch-btn" id="touch-right" aria-label="Move right">→</button>
            </div>
            <button type="button" class="touch-btn touch-jump" id="touch-jump" aria-label="Jump">↑</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('platform-canvas');
    const stage = document.querySelector('.sky-runner-stage');
    const ctx = canvas.getContext('2d');
    let W = canvas.width;
    let H = canvas.height;

    function resizeCanvas() {
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

    const GRAVITY = 0.52;
    const MOVE_SPEED = 4.8;
    const JUMP_POWER = -12.2;
    const COYOTE_FRAMES = 8;
    const WORLD_MAX = 50000;
    const BEST_KEY = 'sky_runner_best';
    const GEN_AHEAD = 900;

    const keys = {};
    let jumpPressed = false;
    let canDoubleJump = false;
    let coyoteTimer = 0;
    let animFrame = 0;

    let player, platforms, coins, particles, clouds, stars;
    let score, lives, cameraX, gameOver, paused, started;
    let lastPlatformX, furthestX, checkpoint;
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
        particles = [];
        clouds = [];
        stars = [];
        score = 0;
        lives = 3;
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
            const type = Math.random() < 0.12 ? 'spring' : 'normal';
            platforms.push({ x, y, width: w, height: 18, type });

            if (Math.random() < 0.75) {
                const count = 1 + Math.floor(Math.random() * 3);
                for (let c = 0; c < count; c++) {
                    coins.push({
                        x: x + 20 + c * 28,
                        y: y - 32 - (c % 2) * 18,
                        radius: 9,
                        collected: false,
                        spin: Math.random() * Math.PI * 2,
                        kind: Math.random() < 0.15 ? 'gem' : 'coin',
                    });
                }
            }
            lastPlatformX = x + w;
        }
    }

    function pruneWorld() {
        const cut = cameraX - 400;
        platforms = platforms.filter(p => p.x + p.width > cut);
        coins = coins.filter(c => !c.collected && c.x > cut - 50);
    }

    function updateHud() {
        coinCountEl.textContent = String(score);
        distanceEl.textContent = String(Math.floor(furthestX / 10));
        livesEl.textContent = String(lives);
    }

    function tryJump() {
        if (gameOver || paused || !started) return;
        const canJump = player.onGround || coyoteTimer > 0;
        if (canJump && player.jumpsUsed === 0) {
            player.vy = JUMP_POWER;
            player.onGround = false;
            player.jumpsUsed = 1;
            coyoteTimer = 0;
            canDoubleJump = true;
            GameSounds.play('jump');
            spawnDust(player.x + player.width / 2 - cameraX, player.y + player.height);
        } else if (canDoubleJump && player.jumpsUsed === 1) {
            player.vy = JUMP_POWER * 0.88;
            player.jumpsUsed = 2;
            canDoubleJump = false;
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
        }
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

        if (keys['ArrowLeft'] || keys['a'] || keys['A']) player.vx = -MOVE_SPEED;
        else if (keys['ArrowRight'] || keys['d'] || keys['D']) player.vx = MOVE_SPEED;
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
                    coyoteTimer = 0;
                    GameSounds.play('jump');
                    spawnDust(player.x + player.width / 2 - cameraX, player.y + player.height);
                } else {
                    player.vy = 0;
                    player.onGround = true;
                    player.jumpsUsed = 0;
                    canDoubleJump = false;
                    coyoteTimer = COYOTE_FRAMES;
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
    }

    function updatePhysics() {
        if (gameOver || paused || !started) return;

        player.vy += GRAVITY;
        player.x += player.vx;
        player.y += player.vy;

        if (player.x < 0) player.x = 0;
        if (player.x > WORLD_MAX) player.x = WORLD_MAX;

        collidePlatforms();

        if (player.y > H + 100) {
            loseLife();
        }

        furthestX = Math.max(furthestX, player.x);
        if (player.x + GEN_AHEAD > lastPlatformX) extendLevel(player.x + GEN_AHEAD);
        pruneWorld();

        if (player.x > cameraX + W * 0.55) cameraX = player.x - W * 0.55;
        if (player.x < cameraX + W * 0.2) cameraX = Math.max(0, player.x - W * 0.2);

        coins.forEach(coin => {
            if (coin.collected) return;
            coin.spin += 0.08;
            const cx = coin.x + coin.radius;
            const cy = coin.y + coin.radius;
            const dx = (player.x + player.width / 2) - cx;
            const dy = (player.y + player.height / 2) - cy;
            if (Math.hypot(dx, dy) < coin.radius + 20) {
                coin.collected = true;
                score += coin.kind === 'gem' ? 5 : 1;
                GameSounds.play('coin');
                spawnCoinBurst(cx - cameraX, cy, coin.kind === 'gem' ? '#c084fc' : '#facc15');
                updateHud();
            }
        });

        particles = particles.filter(p => {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.15;
            p.life -= 0.035;
            return p.life > 0;
        });

        clouds.forEach(c => {
            c.x -= c.s;
            if (c.x + c.w < cameraX * 0.1 - 100) c.x = cameraX * 0.1 + W + Math.random() * 200;
        });

        updateHud();
    }

    function loseLife() {
        GameSounds.play('hurt');
        lives -= 1;
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
        updateHud();
    }

    function endRun() {
        gameOver = true;
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
            const topColor = p.type === 'spring' ? '#a78bfa' : p.type === 'ground' ? '#365314' : '#4ade80';
            const sideColor = p.type === 'spring' ? '#7c3aed' : p.type === 'ground' ? '#1a2e05' : '#16a34a';

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
            }
        });
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
        drawPlatforms();
        drawCoins();
        drawParticles();
        drawPlayer();

        requestAnimationFrame(gameLoop);
    }

    function startRun() {
        GameSounds.init();
        GameSounds.play('start');
        started = true;
        startOverlay.classList.add('hidden');
        resetRun();
    }

    startBtn.addEventListener('click', startRun);
    retryBtn.addEventListener('click', () => { GameSounds.play('click'); started = true; resetRun(); });
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
            if ([' ', 'ArrowUp', 'w', 'W'].includes(e.key)) tryJump();
        }
    });

    window.addEventListener('keyup', e => { keys[e.key] = false; });

    function bindTouchBtn(el, onDown, onUp) {
        if (!el) return;
        const down = (e) => {
            e.preventDefault();
            el.classList.add('active');
            if (el.setPointerCapture) el.setPointerCapture(e.pointerId);
            onDown();
        };
        const up = () => {
            el.classList.remove('active');
            onUp();
        };
        el.addEventListener('pointerdown', down);
        el.addEventListener('pointerup', up);
        el.addEventListener('pointercancel', up);
        el.addEventListener('pointerleave', up);
    }

    bindTouchBtn(document.getElementById('touch-left'), () => { keys['ArrowLeft'] = true; }, () => { keys['ArrowLeft'] = false; });
    bindTouchBtn(document.getElementById('touch-right'), () => { keys['ArrowRight'] = true; }, () => { keys['ArrowRight'] = false; });
    bindTouchBtn(document.getElementById('touch-jump'), () => tryJump(), () => {});

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => resizeCanvas(), 100);
    });

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
