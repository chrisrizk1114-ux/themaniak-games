@extends('layouts.app')

@section('content')
<style>
    body:has(.snl-page) {
        overflow: hidden;
    }

    .main-content:has(.snl-page) {
        max-width: none;
        padding: 0;
        width: 100%;
        overflow: hidden;
    }

    .snl-page {
        --nav-h: 76px;
        font-family: Arial, sans-serif;
        height: calc(100svh - var(--nav-h));
        min-height: calc(100svh - var(--nav-h));
        max-height: calc(100svh - var(--nav-h));
        width: 100%;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 20% 0%, rgba(74,222,128,0.12) 0%, transparent 45%),
            radial-gradient(ellipse at 80% 100%, rgba(251,191,36,0.1) 0%, transparent 45%),
            linear-gradient(165deg, #0a1628 0%, #111827 45%, #030712 100%);
    }

    .snl-shell {
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
    }

    .snl-shell:has(#gameArea.active) {
        justify-content: flex-start;
        align-items: stretch;
        height: 100%;
        padding: clamp(0.35rem, 1vh, 0.65rem) clamp(0.5rem, 2vw, 1rem);
        gap: clamp(0.35rem, 1vh, 0.5rem);
    }

    .player-select {
        background: rgba(15,23,42,0.85);
        backdrop-filter: blur(8px);
        padding: clamp(1.25rem, 3vw, 2rem);
        border-radius: 20px;
        border: 2px solid rgba(255,255,255,0.12);
        margin: 0 auto;
        max-width: 440px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .player-select h2 {
        margin-bottom: 1rem;
        color: #ffd700;
        font-size: 1.3rem;
    }
    .player-count-btns {
        display: flex;
        gap: 0.8rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .player-count-btn {
        background: linear-gradient(90deg, #e94560, #ff6b6b);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        font-size: 1rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .player-count-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(233, 69, 96, 0.4);
    }
    #gameArea {
        display: none;
        flex-direction: column;
        width: 100%;
        height: 100%;
        min-height: 0;
        flex: 1;
    }

    #gameArea.active {
        display: flex;
    }

    .snl-hud {
        flex-shrink: 0;
        width: 100%;
    }

    .board-scene {
        position: relative;
        flex: 1 1 auto;
        width: 100%;
        min-height: 0;
        border-radius: clamp(12px, 2vw, 20px);
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,0.55), 0 0 80px rgba(34,197,94,0.08);
        border: 2px solid rgba(255,255,255,0.1);
    }

    #board3d {
        position: absolute;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
        background: radial-gradient(ellipse at 50% 20%, #1e3a5f 0%, #0f172a 55%, #020617 100%);
        cursor: default;
    }

    .snl-footer {
        flex-shrink: 0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: clamp(0.5rem, 2vw, 1.25rem);
        width: 100%;
        padding-top: clamp(0.25rem, 0.8vh, 0.5rem);
    }

    .controls-row {
        display: flex;
        gap: 0.8rem;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    .dice-display {
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        line-height: 1;
        transition: all 0.2s;
        filter: drop-shadow(0 8px 16px rgba(0,0,0,0.4));
    }
    .dice-display.rolling {
        animation: diceRoll 0.1s infinite;
    }
    @keyframes diceRoll {
        0%, 100% { transform: rotate(0deg) scale(1); }
        25% { transform: rotate(90deg) scale(1.1); }
        50% { transform: rotate(180deg) scale(1); }
        75% { transform: rotate(270deg) scale(1.1); }
    }
    .roll-btn {
        background: linear-gradient(90deg, #e94560, #ff6b6b);
        color: white;
        border: none;
        padding: 0.6rem 2rem;
        font-size: 1rem;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .roll-btn:hover { transform: scale(1.05); box-shadow: 0 10px 30px rgba(233, 69, 96, 0.4); }
    .roll-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .status {
        font-size: clamp(0.95rem, 2vw, 1.15rem);
        margin: 0 0 0.35rem;
        color: #ffd700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .game-title {
        font-size: clamp(1.4rem, 3.5vw, 2rem);
        margin: 0 0 0.75rem;
        background: linear-gradient(90deg, #4ade80, #fbbf24, #f472b6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: clamp(0.4rem, 1.5vw, 0.85rem);
        flex-wrap: wrap;
        width: 100%;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: clamp(0.72rem, 2vw, 0.88rem);
        letter-spacing: 0.02em;
        border: 2px solid transparent;
    }
    .legend-item--snake {
        color: #fecaca;
        background: rgba(239, 68, 68, 0.22);
        border-color: rgba(239, 68, 68, 0.55);
    }
    .legend-item--ladder {
        color: #fef3c7;
        background: rgba(251, 191, 36, 0.22);
        border-color: rgba(251, 191, 36, 0.55);
    }
    .legend-item--tile {
        color: #bbf7d0;
        background: rgba(34, 197, 94, 0.18);
        border-color: rgba(74, 222, 128, 0.45);
    }
    .legend-box {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        border: 2px solid rgba(0,0,0,0.25);
        flex-shrink: 0;
    }
    .snl-rules {
        color: rgba(255,255,255,0.82);
        font-size: clamp(0.85rem, 2.2vw, 0.95rem);
        line-height: 1.45;
        margin: 0 0 1rem;
        padding: 0.65rem 0.85rem;
        border-radius: 12px;
        background: rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .snl-rules strong { color: #fbbf24; }

    @media (max-width: 600px) {
        .snl-shell:has(#gameArea.active) {
            padding: 0.25rem 0.35rem 0.35rem;
        }
        .snl-hud .game-title { font-size: 1.15rem; margin-bottom: 0.15rem; }
        .status { font-size: 0.88rem; line-height: 1.35; }
        .legend-item { font-size: 0.72rem; padding: 0.35rem 0.6rem; }
        .roll-btn, .player-count-btn, #soundBtn {
            min-height: 48px;
            min-width: 48px;
            font-size: 1rem;
        }
        .controls-row { flex-wrap: wrap; justify-content: center; }
        .dice-display { font-size: 2.75rem; }
    }
</style>

<div class="snl-page">
    <div class="snl-shell">
        <div class="player-select" id="playerSelect">
            <h1 class="game-title">Snakes &amp; Ladders 3D 🎲🐍🧗</h1>
            <p class="snl-rules"><strong>🐍 Red tiles</strong> = snake (slide down) · <strong>🧗 Gold tiles</strong> = ladder (climb up) · First to <strong>100</strong> wins!</p>
            <h2>How many players?</h2>
            <div class="player-count-btns">
                <button class="player-count-btn" onclick="startGame(1)">1 Player</button>
                <button class="player-count-btn" onclick="startGame(2)">2 Players</button>
                <button class="player-count-btn" onclick="startGame(3)">3 Players</button>
                <button class="player-count-btn" onclick="startGame(4)">4 Players</button>
            </div>
        </div>

        <div id="gameArea">
            <div class="snl-hud">
                <h1 class="game-title" style="margin-bottom:0.25rem;">Snakes &amp; Ladders 3D</h1>
                <div class="status" id="status">Click Roll Dice to start!</div>
            </div>

            <div class="board-scene" id="boardScene">
                <canvas id="board3d" width="920" height="620"></canvas>
            </div>

            <div class="snl-footer">
                <div class="dice-display" id="diceDisplay">🎲</div>
                <div class="controls-row">
                    <button class="roll-btn" id="rollBtn">Roll Dice!</button>
                    <button class="player-count-btn" id="soundBtn" type="button">🔊</button>
                </div>
                <div class="legend">
                    <div class="legend-item legend-item--tile">
                        <div class="legend-box" style="background:linear-gradient(135deg,#86efac,#22c55e);border-color:#15803d;">1</div>
                        <span>Normal tile</span>
                    </div>
                    <div class="legend-item legend-item--ladder">
                        <div class="legend-box" style="background:linear-gradient(135deg,#fde047,#ca8a04);border-color:#a16207;">🧗</div>
                        <span>Ladder ↑ climb</span>
                    </div>
                    <div class="legend-item legend-item--snake">
                        <div class="legend-box" style="background:linear-gradient(135deg,#f87171,#dc2626);border-color:#991b1b;">🐍</div>
                        <span>Snake ↓ slide</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById('board3d');
    const boardScene = document.getElementById('boardScene');
    const ctx = canvas.getContext('2d');
    const statusEl = document.getElementById('status');
    const diceDisplayEl = document.getElementById('diceDisplay');
    const rollBtn = document.getElementById('rollBtn');
    const playerSelectEl = document.getElementById('playerSelect');
    const gameAreaEl = document.getElementById('gameArea');

    function resizeCanvas() {
        const rect = boardScene.getBoundingClientRect();
        const w = Math.max(480, Math.floor(rect.width) || window.innerWidth - 32);
        const h = Math.max(320, Math.floor(rect.height) || (window.innerHeight - 220));
        if (canvas.width === w && canvas.height === h) return false;
        canvas.width = w;
        canvas.height = h;
        buildTileLayout();
        initStars();
        renderBoard();
        return true;
    }

    const playerColors = ['#e94560', '#4ade80', '#3b82f6', '#a855f7'];
    const diceEmojis = ['⚀', '⚁', '⚂', '⚃', '⚄', '⚅'];

    const snakes = {
        16: 6, 47: 26, 49: 11, 56: 53, 62: 19,
        64: 60, 87: 24, 93: 73, 95: 75, 98: 78
    };
    const ladders = {
        1: 38, 4: 14, 9: 31, 21: 42, 28: 84,
        36: 44, 51: 67, 71: 91, 80: 100
    };

    let players = [];
    let currentPlayer = 0;
    let isRolling = false;
    let animating = false;
    let highlightTiles = [];
    let boardTiles = [];
    let stars = [];

    function initStars() {
        stars = [];
        for (let i = 0; i < 80; i++) {
            stars.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height * 0.55,
                r: Math.random() * 1.2 + 0.3,
                a: Math.random() * 0.5 + 0.2,
            });
        }
    }

    function getTilePosition(tileNum) {
        const row = Math.floor((100 - tileNum) / 10);
        const col = (100 - tileNum) % 10;
        const adjustedCol = (row % 2 === 0) ? (9 - col) : col;
        return { row, col: adjustedCol };
    }

    function buildTileLayout() {
        boardTiles = [];
        const marginX = 48;
        const marginTop = 52;
        const boardW = canvas.width - marginX * 2;
        const boardH = canvas.height - marginTop - 72;
        const depth = 14;

        for (let num = 1; num <= 100; num++) {
            const { row, col } = getTilePosition(num);
            const t = row / 9;
            const scale = 0.82 + t * 0.18;
            const tileW = (boardW / 10) * scale;
            const tileH = (boardH / 10) * (0.88 + t * 0.12);
            const offsetX = (boardW - tileW * 10) / 2;
            const x = marginX + offsetX + col * tileW;
            const y = marginTop + row * tileH;
            const type = snakes[num] ? 'snake' : ladders[num] ? 'ladder' : 'normal';
            boardTiles[num] = { num, row, col, x, y, w: tileW, h: tileH, depth, type, cx: x + tileW / 2, cy: y + tileH / 2 };
        }
    }

    function tileRect(t) {
        return { x: t.x, y: t.y, w: t.w, h: t.h, cx: t.cx, cy: t.cy };
    }

    function drawBackground() {
        const g = ctx.createLinearGradient(0, 0, 0, canvas.height);
        g.addColorStop(0, '#0c1929');
        g.addColorStop(0.5, '#111827');
        g.addColorStop(1, '#030712');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        stars.forEach(s => {
            ctx.globalAlpha = s.a;
            ctx.fillStyle = '#fff';
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
            ctx.fill();
        });
        ctx.globalAlpha = 1;

        ctx.fillStyle = 'rgba(15,23,42,0.6)';
        ctx.beginPath();
        ctx.ellipse(canvas.width / 2, canvas.height - 30, canvas.width * 0.42, 40, 0, 0, Math.PI * 2);
        ctx.fill();
    }

    function drawTile3D(t, pulse = 0) {
        const d = t.depth + pulse * 4;
        const top = t.type === 'snake' ? ['#fca5a5', '#ef4444', '#b91c1c']
            : t.type === 'ladder' ? ['#fde68a', '#f59e0b', '#b45309']
            : (t.num % 2 === 0) ? ['#bbf7d0', '#4ade80', '#16a34a'] : ['#86efac', '#22c55e', '#15803d'];

        ctx.save();

        ctx.fillStyle = top[2];
        ctx.beginPath();
        ctx.moveTo(t.x + d * 0.35, t.y + t.h);
        ctx.lineTo(t.x + t.w + d * 0.35, t.y + t.h);
        ctx.lineTo(t.x + t.w, t.y + t.h + d);
        ctx.lineTo(t.x, t.y + t.h + d);
        ctx.closePath();
        ctx.fill();

        ctx.fillStyle = top[1];
        ctx.beginPath();
        ctx.moveTo(t.x + t.w, t.y);
        ctx.lineTo(t.x + t.w + d * 0.35, t.y + d * 0.2);
        ctx.lineTo(t.x + t.w + d * 0.35, t.y + t.h + d * 0.2);
        ctx.lineTo(t.x + t.w, t.y + t.h);
        ctx.closePath();
        ctx.fill();

        const tg = ctx.createLinearGradient(t.x, t.y, t.x + t.w, t.y + t.h);
        tg.addColorStop(0, top[0]);
        tg.addColorStop(1, top[1]);
        ctx.fillStyle = tg;
        ctx.fillRect(t.x, t.y, t.w, t.h);

        ctx.strokeStyle = 'rgba(0,0,0,0.25)';
        ctx.lineWidth = 1;
        ctx.strokeRect(t.x, t.y, t.w, t.h);

        if (highlightTiles.includes(t.num)) {
            ctx.strokeStyle = '#fbbf24';
            ctx.lineWidth = 3;
            ctx.strokeRect(t.x - 1, t.y - 1, t.w + 2, t.h + 2);
        }

        if (t.type === 'snake') {
            ctx.strokeStyle = 'rgba(239,68,68,0.9)';
            ctx.lineWidth = 2.5;
            ctx.strokeRect(t.x + 1, t.y + 1, t.w - 2, t.h - 2);
        } else if (t.type === 'ladder') {
            ctx.strokeStyle = 'rgba(251,191,36,0.95)';
            ctx.lineWidth = 2.5;
            ctx.strokeRect(t.x + 1, t.y + 1, t.w - 2, t.h - 2);
        }

        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.font = `bold ${Math.max(9, t.w * 0.22)}px Arial`;
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        ctx.fillText(t.num, t.x + 4, t.y + 3);

        if (t.type === 'snake') {
            ctx.font = `${Math.max(12, t.w * 0.34)}px Arial`;
            ctx.textAlign = 'center';
            ctx.fillText('🐍', t.cx, t.y + t.h * 0.36);
            ctx.font = `bold ${Math.max(9, t.w * 0.2)}px Arial`;
            ctx.fillStyle = '#fff';
            ctx.strokeStyle = '#7f1d1d';
            ctx.lineWidth = 3;
            ctx.strokeText(`↓${snakes[t.num]}`, t.cx, t.y + t.h * 0.72);
            ctx.fillText(`↓${snakes[t.num]}`, t.cx, t.y + t.h * 0.72);
        } else if (t.type === 'ladder') {
            ctx.font = `${Math.max(12, t.w * 0.34)}px Arial`;
            ctx.textAlign = 'center';
            ctx.fillText('🧗', t.cx, t.y + t.h * 0.36);
            ctx.font = `bold ${Math.max(9, t.w * 0.2)}px Arial`;
            ctx.fillStyle = '#fff';
            ctx.strokeStyle = '#78350f';
            ctx.lineWidth = 3;
            ctx.strokeText(`↑${ladders[t.num]}`, t.cx, t.y + t.h * 0.72);
            ctx.fillText(`↑${ladders[t.num]}`, t.cx, t.y + t.h * 0.72);
        }

        ctx.restore();
    }

    function drawLadder3D(fromNum, toNum) {
        const a = boardTiles[fromNum];
        const b = boardTiles[toNum];
        if (!a || !b) return;

        const thick = canvas.width < 520 ? 1.45 : 1;
        const ax = a.cx, ay = a.cy;
        const bx = b.cx, by = b.cy;
        const dx = bx - ax, dy = by - ay;
        const len = Math.hypot(dx, dy) || 1;
        const nx = -dy / len * 8;
        const ny = dx / len * 8;

        const railGrad = ctx.createLinearGradient(ax, ay, bx, by);
        railGrad.addColorStop(0, '#fcd34d');
        railGrad.addColorStop(1, '#92400e');

        ctx.lineCap = 'round';
        ctx.lineWidth = 5 * thick;
        ctx.strokeStyle = railGrad;
        ctx.shadowColor = 'rgba(0,0,0,0.4)';
        ctx.shadowBlur = 6;

        [[ax + nx, ay + ny, bx + nx, by + ny], [ax - nx, ay - ny, bx - nx, by - ny]].forEach(([x1, y1, x2, y2]) => {
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
        });

        ctx.shadowBlur = 0;
        const rungs = Math.max(4, Math.floor(len / 28));
        for (let i = 1; i < rungs; i++) {
            const t = i / rungs;
            const rx = ax + dx * t, ry = ay + dy * t;
            ctx.lineWidth = 4 * thick;
            ctx.strokeStyle = '#fbbf24';
            ctx.beginPath();
            ctx.moveTo(rx + nx, ry + ny);
            ctx.lineTo(rx - nx, ry - ny);
            ctx.stroke();
        }

        ctx.fillStyle = '#fef3c7';
        ctx.font = `bold ${Math.round(11 * thick)}px Arial`;
        ctx.textAlign = 'center';
        ctx.fillText('🧗 LADDER', (ax + bx) / 2, (ay + by) / 2 - 6);
    }

    function drawSnake3D(fromNum, toNum) {
        const a = boardTiles[fromNum];
        const b = boardTiles[toNum];
        if (!a || !b) return;

        const thick = canvas.width < 520 ? 1.45 : 1;
        const ax = a.cx, ay = a.cy;
        const bx = b.cx, by = b.cy;
        const mx = (ax + bx) / 2 + (by - ay) * 0.15;
        const my = (ay + by) / 2 + (ax - bx) * 0.12;

        ctx.save();
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        ctx.strokeStyle = 'rgba(0,0,0,0.35)';
        ctx.lineWidth = 16 * thick;
        ctx.beginPath();
        ctx.moveTo(ax, ay);
        ctx.quadraticCurveTo(mx, my, bx, by);
        ctx.stroke();

        const sg = ctx.createLinearGradient(ax, ay, bx, by);
        sg.addColorStop(0, '#ef4444');
        sg.addColorStop(0.5, '#22c55e');
        sg.addColorStop(1, '#dc2626');
        ctx.strokeStyle = sg;
        ctx.lineWidth = 12 * thick;
        ctx.beginPath();
        ctx.moveTo(ax, ay);
        ctx.quadraticCurveTo(mx, my, bx, by);
        ctx.stroke();

        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.lineWidth = 3;
        ctx.setLineDash([6, 10]);
        ctx.beginPath();
        ctx.moveTo(ax, ay);
        ctx.quadraticCurveTo(mx, my, bx, by);
        ctx.stroke();
        ctx.setLineDash([]);

        const headAngle = Math.atan2(ay - my, ax - mx);
        ctx.translate(ax, ay);
        ctx.rotate(headAngle);
        ctx.fillStyle = '#ef4444';
        ctx.beginPath();
        ctx.ellipse(0, 0, 14, 10, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#fff';
        ctx.beginPath();
        ctx.arc(5, -4, 3, 0, Math.PI * 2);
        ctx.arc(5, 4, 3, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#111';
        ctx.beginPath();
        ctx.arc(6, -4, 1.5, 0, Math.PI * 2);
        ctx.arc(6, 4, 1.5, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#dc2626';
        ctx.beginPath();
        ctx.moveTo(-12, 0);
        ctx.lineTo(-18, -5);
        ctx.lineTo(-18, 5);
        ctx.closePath();
        ctx.fill();

        ctx.restore();

        ctx.fillStyle = 'rgba(255,255,255,0.85)';
        ctx.font = `bold ${Math.round(11 * thick)}px Arial`;
        ctx.textAlign = 'center';
        ctx.fillText('🐍 SNAKE', mx, my - 8);
    }

    function drawPlayerToken(cx, cy, color, num, elevated = 0) {
        const by = cy - elevated;
        ctx.save();
        ctx.fillStyle = 'rgba(0,0,0,0.35)';
        ctx.beginPath();
        ctx.ellipse(cx, cy + 6, 10, 4, 0, 0, Math.PI * 2);
        ctx.fill();

        const g = ctx.createRadialGradient(cx - 3, by - 8, 2, cx, by - 4, 14);
        g.addColorStop(0, '#fff');
        g.addColorStop(0.35, color);
        g.addColorStop(1, shadeColor(color, -40));
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(cx, by - 4, 11, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = 'rgba(255,255,255,0.8)';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.fillStyle = '#fff';
        ctx.font = 'bold 10px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(num, cx, by - 4);
        ctx.restore();
    }

    function shadeColor(hex, amt) {
        const n = parseInt(hex.slice(1), 16);
        const r = Math.min(255, Math.max(0, (n >> 16) + amt));
        const g = Math.min(255, Math.max(0, ((n >> 8) & 255) + amt));
        const b = Math.min(255, Math.max(0, (n & 255) + amt));
        return `rgb(${r},${g},${b})`;
    }

    function renderBoard() {
        drawBackground();
        boardTiles.forEach(t => {
            if (t) drawTile3D(t, highlightTiles.includes(t.num) ? 1 : 0);
        });
        Object.entries(ladders).forEach(([from, to]) => drawLadder3D(+from, to));
        Object.entries(snakes).forEach(([from, to]) => drawSnake3D(+from, to));

        players.forEach((player, idx) => {
            let cx, cy;
            if (player.sliding && player.slideX != null) {
                cx = player.slideX;
                cy = player.slideY;
            } else {
                const t = boardTiles[player.displayPos ?? player.position];
                if (!t) return;
                const offsets = [
                    { x: -t.w * 0.18, y: -t.h * 0.1 },
                    { x: t.w * 0.18, y: -t.h * 0.1 },
                    { x: -t.w * 0.18, y: t.h * 0.12 },
                    { x: t.w * 0.18, y: t.h * 0.12 },
                ];
                const off = offsets[idx] || { x: 0, y: 0 };
                cx = t.cx + off.x;
                cy = t.cy + off.y;
            }
            const elevated = player.sliding ? 10 : (currentPlayer === idx && !animating ? 6 : 0);
            drawPlayerToken(cx, cy, player.color, player.number, elevated);
        });

        ctx.fillStyle = 'rgba(255,255,255,0.5)';
        ctx.font = '12px Arial';
        ctx.textAlign = 'right';
        ctx.fillText('3D perspective board', canvas.width - 14, canvas.height - 14);
    }

    function sleep(ms) {
        return new Promise(r => setTimeout(r, ms));
    }

    async function animateHop(player, from, to) {
        animating = true;
        const step = from < to ? 1 : -1;
        for (let pos = from; pos !== to; pos += step) {
            player.displayPos = pos + step;
            renderBoard();
            await sleep(90);
        }
        player.displayPos = to;
        player.position = to;
        animating = false;
        renderBoard();
    }

    async function animateSlide(player, from, to, type) {
        animating = true;
        highlightTiles = [from, to];
        const frames = 24;
        for (let i = 1; i <= frames; i++) {
            const t = i / frames;
            const ease = type === 'ladder' ? t * t : 1 - Math.pow(1 - t, 2);
            const a = boardTiles[from], b = boardTiles[to];
            if (a && b) {
                player.displayPos = from;
                player.slideX = a.cx + (b.cx - a.cx) * ease;
                player.slideY = a.cy + (b.cy - a.cy) * ease;
                player.sliding = true;
            }
            renderBoard();
            if (i % 4 === 0) GameSounds.play(type === 'ladder' ? 'ladder' : 'snake');
            await sleep(40);
        }
        player.sliding = false;
        player.slideX = null;
        player.slideY = null;
        player.displayPos = to;
        player.position = to;
        highlightTiles = [];
        animating = false;
        renderBoard();
    }

    function startGame(numPlayers) {
        GameSounds.init();
        GameSounds.play('start');
        players = [];
        for (let i = 0; i < numPlayers; i++) {
            players.push({ position: 1, displayPos: 1, color: playerColors[i], number: i + 1 });
        }
        currentPlayer = 0;
        playerSelectEl.style.display = 'none';
        gameAreaEl.classList.add('active');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                resizeCanvas();
                updateStatus();
            });
        });
    }

    function updateAllPlayers() {
        renderBoard();
    }

    function updateStatus() {
        statusEl.innerHTML = `Player <span style="color:${players[currentPlayer].color};">${players[currentPlayer].number}</span>'s turn! Roll the dice!`;
    }

    function rollDice() {
        return Math.floor(Math.random() * 6) + 1;
    }

    function animateRoll() {
        return new Promise((resolve) => {
            isRolling = true;
            diceDisplayEl.classList.add('rolling');
            rollBtn.disabled = true;
            let rollCount = 0;
            const rollInterval = setInterval(() => {
                diceDisplayEl.textContent = diceEmojis[Math.floor(Math.random() * diceEmojis.length)];
                if (rollCount % 3 === 0) GameSounds.play('dice');
                rollCount++;
                if (rollCount > 20) {
                    clearInterval(rollInterval);
                    const finalRoll = rollDice();
                    diceDisplayEl.textContent = diceEmojis[finalRoll - 1];
                    diceDisplayEl.classList.remove('rolling');
                    GameSounds.play('diceLand');
                    resolve(finalRoll);
                }
            }, 80);
        });
    }

    async function handleRoll() {
        if (isRolling || animating) return;
        const roll = await animateRoll();
        const player = players[currentPlayer];
        statusEl.innerHTML = `Player <span style="color:${player.color};">${player.number}</span> rolled ${roll}!`;

        const from = player.position;
        const newPos = from + roll;

        if (newPos > 100) {
            statusEl.innerHTML = `Player <span style="color:${player.color};">${player.number}</span> rolled ${roll}! Need ${100 - from} or less to win!`;
            nextTurn();
            return;
        }

        await animateHop(player, from, newPos);

        if (snakes[player.position]) {
            const dest = snakes[player.position];
            statusEl.innerHTML = `Player <span style="color:${player.color};">${player.number}</span> hit a SNAKE! ${player.position} → ${dest}`;
            await animateSlide(player, player.position, dest, 'snake');
        } else if (ladders[player.position]) {
            const dest = ladders[player.position];
            statusEl.innerHTML = `Player <span style="color:${player.color};">${player.number}</span> found a LADDER! ${player.position} → ${dest}`;
            await animateSlide(player, player.position, dest, 'ladder');
        }

        if (player.position === 100) {
            GameSounds.play('celebrate');
            highlightTiles = [100];
            renderBoard();
            setTimeout(() => alert(`🎉 Player ${player.number} WINS! 🎉`), 300);
            resetGame();
        } else {
            nextTurn();
        }
    }

    function nextTurn() {
        currentPlayer = (currentPlayer + 1) % players.length;
        updateStatus();
        rollBtn.disabled = false;
        isRolling = false;
        renderBoard();
    }

    function resetGame() {
        players.forEach(p => { p.position = 1; p.displayPos = 1; p.sliding = false; });
        currentPlayer = 0;
        highlightTiles = [];
        updateStatus();
        diceDisplayEl.textContent = '🎲';
        rollBtn.disabled = false;
        isRolling = false;
        renderBoard();
    }

    rollBtn.addEventListener('click', () => { GameSounds.init(); handleRoll(); });
    document.getElementById('soundBtn').addEventListener('click', () => {
        GameSounds.init();
        const on = GameSounds.toggle();
        document.getElementById('soundBtn').textContent = on ? '🔊' : '🔇';
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        if (!gameAreaEl.classList.contains('active')) return;
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(resizeCanvas, 100);
    });

    buildTileLayout();
    initStars();
    renderBoard();
</script>
@endsection
