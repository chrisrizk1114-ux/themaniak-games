@extends('layouts.app')

@section('content')
@once
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/game-leaderboard.css') }}?v=20260608">
        <script src="{{ asset('js/game-leaderboard.js') }}?v=20260608" defer></script>
    @endpush
@endonce
<style>
    @keyframes stella-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(236,72,153,0.5), inset 0 0 20px rgba(251,191,36,0.1); }
        50% { box-shadow: 0 0 40px rgba(236,72,153,0.8), inset 0 0 30px rgba(251,191,36,0.2); }
    }
    @keyframes crown-bounce {
        0%, 100% { transform: translateY(0) rotate(-5deg); }
        50% { transform: translateY(-3px) rotate(5deg); }
    }
    @keyframes shimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    @keyframes slide-in {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .stella-card { animation: stella-glow 2.5s ease-in-out infinite; }
    .stella-name {
        background: linear-gradient(90deg, #f472b6, #fbbf24, #a78bfa, #f472b6);
        background-size: 200% auto;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: shimmer 3s linear infinite;
    }
    .crown-icon { animation: crown-bounce 2s ease-in-out infinite; display: inline-block; }
    .ball-skin-btn { transition: all 0.2s; }
    .ball-skin-btn.active { ring: 2px; transform: scale(1.08); box-shadow: 0 0 16px rgba(34,211,238,0.6); border-color: #22d3ee !important; }
    .hud-panel { background: rgba(15,23,42,0.82); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.12); }
    .leaderboard-panel { animation: slide-in 0.3s ease; }

    .main-content:has(.bowling-page) {
        max-width: none;
        padding: 0;
        width: 100%;
    }
    .bowling-page {
        --nav-h: 76px;
        height: calc(100svh - var(--nav-h));
        min-height: calc(100svh - var(--nav-h));
        width: 100%;
        overflow: hidden;
        background: #080812;
        display: flex;
        flex-direction: column;
    }
    .game-shell {
        width: 100%;
        height: 100%;
        flex: 1;
        min-height: 0;
        max-width: none;
        margin: 0;
        padding: 0;
    }
    .bowling-stage {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: calc(100svh - var(--nav-h));
        overflow: hidden;
    }
    #bowling-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: block;
        z-index: 1;
        background: #0a0a14;
    }
    .game-overlay-hidden { display: none !important; }
    .bowling-stage {
        --game-scale: 1;
    }
    .hud-t { top: calc(0.75rem * var(--game-scale)); }
    .hud-r { right: calc(0.75rem * var(--game-scale)); }
    .hud-l { left: calc(0.75rem * var(--game-scale)); }
    .hud-b { bottom: calc(0.75rem * var(--game-scale)); }

    .hud-scoreboard {
        position: absolute;
        top: calc(0.75rem * var(--game-scale));
        left: 50%;
        transform: translateX(-50%);
        z-index: 30;
    }
    .hud-ball-picker {
        position: absolute;
        left: calc(0.75rem * var(--game-scale));
        top: 50%;
        transform: translateY(-50%);
        z-index: 30;
    }
    .hud-side-controls {
        position: absolute;
        right: calc(0.75rem * var(--game-scale));
        top: 50%;
        transform: translateY(-50%);
        z-index: 30;
    }
    .hud-top-right {
        position: absolute;
        top: calc(0.75rem * var(--game-scale));
        right: calc(0.75rem * var(--game-scale));
        z-index: 30;
    }
    .hud-coins-bar {
        position: absolute;
        bottom: calc(0.75rem * var(--game-scale));
        right: calc(0.75rem * var(--game-scale));
        z-index: 30;
    }
    .hud-frame-bottom {
        position: absolute;
        bottom: calc(0.75rem * var(--game-scale));
        left: calc(0.75rem * var(--game-scale));
        z-index: 30;
    }
    .bowling-stats-bar,
    .bowling-bottom-bar { display: contents; }

    .hud-icon-btn {
        width: calc(3.35rem * var(--game-scale));
        height: calc(3.35rem * var(--game-scale));
        font-size: calc(1.35rem * var(--game-scale));
    }
    .hud-ball-btn {
        width: calc(4rem * var(--game-scale));
        height: calc(4rem * var(--game-scale));
    }
    .hud-gap { gap: calc(0.65rem * var(--game-scale)); }
    .hud-score-num { font-size: calc(2.6rem * var(--game-scale)); line-height: 1; }
    .hud-score-lbl { font-size: calc(0.7rem * var(--game-scale)); }
    .hud-text-sm { font-size: calc(0.95rem * var(--game-scale)); }
    .hud-text-xs { font-size: calc(0.82rem * var(--game-scale)); }
    .hud-text-2xs { font-size: calc(0.65rem * var(--game-scale)); }
    .hud-avatar {
        width: calc(2.85rem * var(--game-scale));
        height: calc(2.85rem * var(--game-scale));
        font-size: calc(1.2rem * var(--game-scale));
    }
    .hud-avatar-sm {
        width: calc(2.3rem * var(--game-scale));
        height: calc(2.3rem * var(--game-scale));
        font-size: calc(0.95rem * var(--game-scale));
    }
    .hud-pin-mini {
        width: calc(3.4rem * var(--game-scale));
        padding: calc(0.55rem * var(--game-scale));
    }
    .hud-panel-pad { padding: calc(0.6rem * var(--game-scale)) calc(1.15rem * var(--game-scale)); }
    .hud-panel-pad-sm { padding: calc(0.45rem * var(--game-scale)) calc(0.9rem * var(--game-scale)); }
    .hud-coins-icon { font-size: calc(1.7rem * var(--game-scale)); }
    .hud-coins-num { font-size: calc(1.4rem * var(--game-scale)); }
    .hud-board { padding: calc(0.45rem * var(--game-scale)) calc(0.65rem * var(--game-scale)); }
    .hud-leaderboard { width: calc(min(18rem, 42vw) * var(--game-scale)); padding: calc(1rem * var(--game-scale)); }
    .hud-pause-title { font-size: calc(3rem * var(--game-scale)); }
    .hud-pause-btn {
        padding: calc(0.75rem * var(--game-scale)) calc(2rem * var(--game-scale));
        font-size: calc(1.125rem * var(--game-scale));
    }
    .hud-cheer { top: calc(4rem * var(--game-scale)); font-size: calc(0.875rem * var(--game-scale)); padding: calc(0.5rem * var(--game-scale)) calc(1.25rem * var(--game-scale)); }
    .score-frame-cell { min-width: calc(3.1rem * var(--game-scale)); }

    @media (max-width: 960px), (hover: none) and (pointer: coarse) {
        .bowling-page .bowling-stage {
            --game-scale: 0.55;
        }
        .bowling-page .bowling-stats-bar,
        .bowling-page .bowling-bottom-bar,
        .bowling-page .bowling-bottom-tools {
            display: contents !important;
        }
        .bowling-page #bowling-canvas {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            flex: unset !important;
            min-height: unset !important;
            order: unset !important;
        }
        .bowling-page .hud-scoreboard {
            position: absolute !important;
            top: 0.3rem !important;
            left: 50% !important;
            right: auto !important;
            bottom: auto !important;
            transform: translateX(-50%) !important;
            translate: none !important;
            width: min(calc(100% - 0.6rem), 18.5rem) !important;
            max-width: 96vw !important;
            margin: 0 !important;
            padding: 0.22rem 0.35rem !important;
            z-index: 30;
        }
        .bowling-page .hud-top-right {
            position: absolute !important;
            top: 2.35rem !important;
            right: 0.35rem !important;
            left: auto !important;
            bottom: auto !important;
            transform: none !important;
            translate: none !important;
        }
        .bowling-page .hud-coins-bar {
            position: absolute !important;
            top: 2.35rem !important;
            left: 0.35rem !important;
            right: auto !important;
            bottom: auto !important;
            transform: none !important;
            translate: none !important;
            padding: 0.25rem 0.5rem !important;
        }
        .bowling-page .hud-stella-block,
        .bowling-page .hud-player-block {
            display: none !important;
        }
        .bowling-page .hud-top-right .hud-score-num { font-size: 1.05rem; }
        .bowling-page .hud-top-right .hud-panel-pad { padding: 0.25rem 0.45rem; }
        .bowling-page .hud-top-right .hud-pin-mini { width: 2rem; padding: 0.2rem; }
        .bowling-page .hud-coins-bar .hud-coins-icon { font-size: 0.88rem; }
        .bowling-page .hud-coins-bar .hud-coins-num { font-size: 0.78rem; }
        .bowling-page .hud-ball-picker {
            position: absolute !important;
            left: 0.35rem !important;
            right: auto !important;
            top: auto !important;
            bottom: 2.55rem !important;
            transform: none !important;
            translate: none !important;
            flex-direction: row !important;
            gap: 0.28rem !important;
        }
        .bowling-page .hud-ball-picker .hud-ball-btn {
            width: 2.1rem;
            height: 2.1rem;
        }
        .bowling-page .hud-side-controls {
            position: absolute !important;
            right: 0.35rem !important;
            left: auto !important;
            top: auto !important;
            bottom: 2.55rem !important;
            transform: none !important;
            translate: none !important;
            flex-direction: row !important;
            gap: 0.28rem !important;
        }
        .bowling-page .hud-side-controls .hud-icon-btn {
            width: 2.1rem;
            height: 2.1rem;
            font-size: 0.82rem;
        }
        .bowling-page .hud-frame-bottom {
            position: absolute !important;
            left: 50% !important;
            right: auto !important;
            bottom: 0.3rem !important;
            transform: translateX(-50%) !important;
            translate: none !important;
            justify-content: center;
            gap: 0.28rem;
            flex-wrap: nowrap;
        }
        .bowling-page .hud-frame-bottom .hud-text-sm { font-size: 0.64rem; }
        .bowling-page .hud-frame-bottom .hud-panel-pad-sm { padding: 0.22rem 0.42rem; }
        .bowling-page .hud-leaderboard {
            width: min(17rem, 88vw) !important;
        }
        .bowling-page .hud-cheer {
            top: 3rem !important;
            font-size: 0.68rem;
            padding: 0.28rem 0.6rem;
        }
        .bowling-page .bowling-mobile-hint {
            display: block;
            position: absolute;
            left: 50%;
            bottom: 5.1rem;
            transform: translateX(-50%);
            z-index: 35;
            pointer-events: none;
            font-size: 0.72rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.88);
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            padding: 0.28rem 0.65rem;
            white-space: nowrap;
        }
    }

    .bowling-page .bowling-mobile-hint {
        display: none;
    }

    @media (max-width: 480px) {
        .bowling-page .bowling-stage { --game-scale: 0.62; }
    }
</style>

<div class="bowling-page" data-build="20260612">
<div class="game-shell">
    <div class="bowling-stage">

        <div class="hud-panel rounded-lg hud-board hud-scoreboard max-w-[70%] overflow-x-auto">
            <div id="scoreboard-frames" class="flex gap-1"></div>
        </div>

        <div class="bowling-stats-bar">
            <div class="hud-coins-bar hud-panel rounded-full hud-panel-pad flex items-center hud-gap">
                <span class="hud-coins-icon">🪙</span>
                <span class="hud-coins-num font-bold text-yellow-400" id="coins">800</span>
            </div>

            <div class="flex items-start hud-gap hud-top-right">
                <div class="hud-panel rounded-xl hud-panel-pad text-center" style="min-width: calc(5.25rem * var(--game-scale))">
                    <div class="hud-score-num font-black text-yellow-400" id="score">0</div>
                    <div class="hud-score-lbl text-gray-400 uppercase mt-0.5">Score</div>
                </div>
                <div class="hud-panel rounded-xl hud-pin-mini flex items-center justify-center">
                    <canvas id="pin-mini" width="40" height="50"></canvas>
                </div>
                <div class="flex flex-col hud-gap">
                    <div class="hud-panel rounded-xl hud-panel-pad-sm flex items-center hud-gap hud-player-block" style="min-width: calc(10rem * var(--game-scale))">
                        <div class="hud-avatar rounded-lg bg-gradient-to-br from-cyan-500 to-blue-700 flex items-center justify-center font-bold" id="player-avatar">P</div>
                        <div>
                            <div class="hud-text-sm font-bold text-white leading-tight" id="player-label">Player</div>
                            <div class="hud-text-2xs text-cyan-400">Lvl <span id="player-level">1</span></div>
                        </div>
                    </div>
                    <div class="hud-panel rounded-xl hud-panel-pad-sm flex items-center hud-gap border border-pink-500/30 hud-stella-block">
                        <div class="hud-avatar-sm rounded-lg bg-gradient-to-br from-pink-500 to-amber-500 flex items-center justify-center">👑</div>
                        <div class="flex-1">
                            <div class="hud-text-xs font-bold stella-name leading-tight">Stella</div>
                            <div class="hud-text-2xs text-pink-300">300 champ</div>
                        </div>
                        <div class="hud-text-sm font-bold text-pink-400">300</div>
                    </div>
                </div>
            </div>
        </div>

        <canvas id="bowling-canvas" width="1100" height="688"></canvas>

        <div class="bowling-mobile-hint" id="bowlingMobileHint">Slide to position · pull left/right to aim · down for power</div>

        <div class="bowling-bottom-bar">
            <div class="bowling-bottom-tools">
                <div class="flex flex-col hud-gap hud-ball-picker">
                    <button class="ball-skin-btn active hud-ball-btn rounded-xl hud-panel overflow-hidden border-2 border-transparent" data-skin="energy" title="Cosmic Energy">
                        <canvas class="skin-preview w-full h-full" data-skin="energy" width="56" height="56"></canvas>
                    </button>
                    <button class="ball-skin-btn hud-ball-btn rounded-xl hud-panel overflow-hidden border-2 border-transparent" data-skin="stella" title="Stella Strike">
                        <canvas class="skin-preview w-full h-full" data-skin="stella" width="56" height="56"></canvas>
                    </button>
                    <button class="ball-skin-btn hud-ball-btn rounded-xl hud-panel overflow-hidden border-2 border-transparent" data-skin="flag" title="Star Spangled">
                        <canvas class="skin-preview w-full h-full" data-skin="flag" width="56" height="56"></canvas>
                    </button>
                    <button class="ball-skin-btn hud-ball-btn rounded-xl hud-panel overflow-hidden border-2 border-transparent" data-skin="polka" title="Nebula Dots">
                        <canvas class="skin-preview w-full h-full" data-skin="polka" width="56" height="56"></canvas>
                    </button>
                </div>

                <div class="flex flex-col hud-gap hud-side-controls">
                    <button id="pause-btn" class="hud-icon-btn rounded-xl hud-panel text-cyan-400 hover:bg-white/10 flex items-center justify-center" title="Pause">⏸</button>
                    <button id="sound-btn" class="hud-icon-btn rounded-xl hud-panel text-gray-300 hover:bg-white/10 flex items-center justify-center" title="Sound">🔊</button>
                    <button id="menu-btn" class="hud-icon-btn rounded-xl hud-panel text-cyan-400 hover:bg-white/10 flex items-center justify-center relative" title="Leaderboard">
                        ☰
                        <span class="absolute -top-1 -right-1 rounded-full bg-red-500 hud-text-2xs font-bold flex items-center justify-center" style="width: calc(1rem * var(--game-scale)); height: calc(1rem * var(--game-scale));">1</span>
                    </button>
                </div>
            </div>

            <div class="flex hud-gap hud-frame-bottom flex-wrap">
                <div class="hud-panel rounded-full hud-panel-pad-sm hud-text-sm">
                    Frame <span class="font-bold text-purple-400" id="frame">1/10</span>
                </div>
                <div class="hud-panel rounded-full hud-panel-pad-sm hud-text-sm">
                    Strikes <span class="font-bold text-pink-400" id="strikes">0</span>
                </div>
                <button id="theme-btn" class="hud-panel rounded-full hud-panel-pad-sm hud-text-sm hover:bg-white/10 text-cyan-300" title="Lane theme">🌌 Theme</button>
                <button id="reset-btn" class="hud-panel rounded-full hud-panel-pad-sm hud-text-sm hover:bg-white/10 text-pink-300">↻ New Game</button>
            </div>
        </div>

        <!-- Stella cheer -->
        <div id="stella-cheer" class="game-overlay-hidden absolute hud-cheer left-1/2 -translate-x-1/2 z-30 rounded-full border border-pink-400/50 bg-pink-500/25 backdrop-blur text-pink-100 font-semibold">
            ✨ Stella approves! Legendary bowling!
        </div>

        <!-- Strike flash -->
        <div id="strike-flash" class="game-overlay-hidden absolute inset-0 z-20 pointer-events-none bg-gradient-to-b from-green-400/25 to-transparent"></div>

        <div id="bowling-toast" class="game-overlay-hidden absolute left-1/2 -translate-x-1/2 z-35 top-[18%] rounded-full border border-cyan-400/40 bg-slate-950/90 backdrop-blur px-5 py-2 text-center font-bold text-cyan-100 shadow-lg shadow-cyan-500/20 text-sm sm:text-base pointer-events-none"></div>

        <!-- Pause overlay -->
        <div id="pause-overlay" class="game-overlay-hidden absolute inset-0 z-40 bg-black/60 flex items-center justify-center">
            <div class="text-center">
                <div class="hud-pause-title font-black text-white mb-4">PAUSED</div>
                <button id="resume-btn" class="hud-pause-btn bg-cyan-500 rounded-xl font-bold hover:bg-cyan-400">Resume</button>
            </div>
        </div>

        <!-- Leaderboard slide panel -->
        <div id="leaderboard-panel" class="game-overlay-hidden absolute top-0 right-0 h-full hud-leaderboard z-50 leaderboard-panel hud-panel border-l border-cyan-500/30 overflow-y-auto">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-bold text-fuchsia-300">🏆 Top 3 Players</h2>
                <button id="close-leaderboard" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>

            <div id="bowling-leaderboard" class="mb-4"></div>

            <div class="stella-card rounded-2xl border-2 border-pink-400/50 p-3 mb-4 bg-pink-500/10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl crown-icon">👑</span>
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-pink-300/80">#1 Champion</p>
                        <h3 class="text-xl font-extrabold stella-name">Stella Strike</h3>
                    </div>
                </div>
                <p class="text-xs text-pink-100/80 italic mb-2">"The galaxy bends for a perfect release."</p>
                <div class="grid grid-cols-3 gap-1 text-center text-xs">
                    <div class="rounded-lg bg-black/30 py-1.5"><div class="font-bold text-amber-300">300</div><div class="text-gray-500 text-[9px]">Score</div></div>
                    <div class="rounded-lg bg-black/30 py-1.5"><div class="font-bold text-pink-300">12</div><div class="text-gray-500 text-[9px]">Strikes</div></div>
                    <div class="rounded-lg bg-black/30 py-1.5"><div class="font-bold text-fuchsia-300">47</div><div class="text-gray-500 text-[9px]">Streak</div></div>
                </div>
            </div>

            <div class="border-t border-gray-600 pt-3 mb-4">
                <h3 class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Arcade Rivals</h3>
                <div id="pro-leaderboard" class="space-y-1.5"></div>
            </div>

            <div class="border-t border-gray-600 pt-3">
                <h3 class="text-xs font-bold text-cyan-300 mb-2">🎯 Your Best Scores</h3>
                <div id="your-scores" class="space-y-1 text-xs mb-2 max-h-[80px] overflow-y-auto">
                    <p class="text-gray-500">Finish a game to save!</p>
                </div>
                <input id="player-name" type="text" maxlength="16" placeholder="Your name"
                    class="w-full rounded-lg bg-gray-800 border border-gray-600 px-2 py-1.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-pink-400">
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('bowling-canvas');
    const stage = canvas.parentElement;
    let ctx = null;

    function bindCtx() {
        ctx = canvas.getContext('2d', { alpha: false }) || canvas.getContext('2d');
        return ctx;
    }
    if (!bindCtx()) {
        console.error('Bowling: canvas 2D context unavailable');
        return;
    }
    const pinMini = document.getElementById('pin-mini');
    const pinMiniCtx = pinMini.getContext('2d');

    const BASE_W = 1100, BASE_H = 688;
    let gameScale = 1;
    let BALL_REST_Z, FOUL_LINE_Z, CAM_Z, LANE_W, Z_NEAR, Z_FAR, PROJ_BASE;
    let laneVanishRatio = 0.22;
    let laneDepthFactor = 0.62;
    let laneViewShift = 0;
    let mobileLaneScale = 1;
    let mobileLaneYOffset = 0;

    function S(v) { return v * gameScale; }
    function Fs(v) { return Math.max(10, v * gameScale); }

    function updateGameMetrics() {
        const cw = canvas.width > 0 ? canvas.width : BASE_W;
        const ch = canvas.height > 0 ? canvas.height : BASE_H;
        gameScale = Math.min(cw / BASE_W, ch / BASE_H) || 1;
        BALL_REST_Z = S(428);
        FOUL_LINE_Z = S(382);
        CAM_Z = S(560);
        LANE_W = S(165);
        Z_NEAR = S(45);
        Z_FAR = S(470);
        PROJ_BASE = S(310);
        const isMobileHud = window.innerWidth <= 960 || window.matchMedia('(hover: none) and (pointer: coarse)').matches;
        if (isMobileHud) {
            laneVanishRatio = 0.24;
            laneDepthFactor = 0.54;
            laneViewShift = ch * 0.10;
            mobileLaneScale = 0.95;
            mobileLaneYOffset = 0;
        } else {
            laneVanishRatio = 0.22;
            laneDepthFactor = 0.62;
            laneViewShift = 0;
            mobileLaneScale = 1;
            mobileLaneYOffset = 0;
        }
        const uiScale = isMobileHud
            ? Math.min(Math.max(gameScale * 0.88, 0.58), 0.78)
            : Math.min(Math.max(gameScale * 1.18, 1.08), 1.65);
        stage.style.setProperty('--game-scale', uiScale);
        const pinSize = Math.max(28, Math.round(40 * uiScale));
        pinMini.width = pinSize;
        pinMini.height = Math.max(35, Math.round(pinSize * 1.25));
    }

    function scaleWorld(ratio) {
        if (ratio === 1) return;
        if (ball) {
            ball.x *= ratio;
            ball.z *= ratio;
            ball.radius = S(24);
            ball.vx *= ratio;
            ball.vz *= ratio;
        }
        pins.forEach(pin => {
            pin.x *= ratio;
            pin.z *= ratio;
            pin.pinRadius = S(10);
            pin.vx *= ratio;
            pin.vz *= ratio;
        });
        if (pinsetter && pinsetter.placing) {
            pinsetter.placing.forEach(pin => {
                pin.x *= ratio;
                pin.z *= ratio;
            });
            pinsetter.sweepX *= ratio;
        }
    }

    function resizeCanvas() {
        let w = stage.clientWidth;
        let h = stage.clientHeight;
        if (w < 2 || h < 2) {
            w = window.innerWidth;
            h = Math.max(400, window.innerHeight - 76);
        }
        const prevScale = gameScale;
        canvas.width = w;
        canvas.height = h;
        bindCtx();
        updateGameMetrics();
        if (prevScale > 0 && Math.abs(prevScale - gameScale) > 0.001 && (ball || pins.length)) {
            scaleWorld(gameScale / prevScale);
        }
    }

    function scheduleCanvasResize() {
        requestAnimationFrame(() => {
            resizeCanvas();
            requestAnimationFrame(resizeCanvas);
        });
    }

    window.addEventListener('resize', () => {
        scheduleCanvasResize();
        initStars();
        drawPinMini();
        drawSkinPreviews();
    });
    const frameEl = document.getElementById('frame');
    const scoreEl = document.getElementById('score');
    const strikesEl = document.getElementById('strikes');
    const coinsEl = document.getElementById('coins');
    const playerLabel = document.getElementById('player-label');
    const playerAvatar = document.getElementById('player-avatar');
    const playerLevel = document.getElementById('player-level');
    const resetBtn = document.getElementById('reset-btn');
    const pauseBtn = document.getElementById('pause-btn');
    const resumeBtn = document.getElementById('resume-btn');
    const pauseOverlay = document.getElementById('pause-overlay');
    const soundBtn = document.getElementById('sound-btn');
    const menuBtn = document.getElementById('menu-btn');
    const leaderboardPanel = document.getElementById('leaderboard-panel');
    const closeLeaderboard = document.getElementById('close-leaderboard');
    const scoreboardFrames = document.getElementById('scoreboard-frames');
    const proLeaderboardEl = document.getElementById('pro-leaderboard');
    const yourScoresEl = document.getElementById('your-scores');
    const playerNameEl = document.getElementById('player-name');
    const stellaCheerEl = document.getElementById('stella-cheer');
    const strikeFlashEl = document.getElementById('strike-flash');
    const bowlingToastEl = document.getElementById('bowling-toast');
    const themeBtn = document.getElementById('theme-btn');

    const PRO_BOWLERS = [
        { rank: 2, name: 'Marcus Lane', score: 289, strikes: 11, avatar: '🎯', title: 'Pin Crusher' },
        { rank: 3, name: 'Jade Split', score: 276, strikes: 9, avatar: '💎', title: 'Spare Master' },
        { rank: 4, name: 'Rico Roll', score: 268, strikes: 8, avatar: '🔥', title: 'Hot Streak' },
        { rank: 5, name: 'Nova King', score: 255, strikes: 7, avatar: '⭐', title: 'Rising Star' },
        { rank: 6, name: 'Ace Gutters', score: 241, strikes: 6, avatar: '🎱', title: 'Lane Legend' },
    ];
    const STORAGE_KEY = 'galaxy_bowling_scores';
    const NAME_KEY = 'galaxy_bowling_player';
    const COINS_KEY = 'galaxy_bowling_coins';
    const SKIN_KEY = 'galaxy_bowling_skin';
    const THEME_KEY = 'galaxy_bowling_theme';
    const LANE_THEMES = ['cosmic', 'nebula'];

    let stars = [], asteroids = [];
    let pins = [], ball = null;
    let isDragging = false, paused = false, soundOn = true;
    let dragStart = { x: 0, y: 0 }, dragEnd = { x: 0, y: 0 };
    let ballXAtDragStart = 0;
    let aimLocked = false;
    let gutterGuardUsed = false;
    let consecutiveStrikes = 0;
    let cinematicTimer = 0;
    let laneTheme = 'cosmic';
    let canvasToast = { text: '', timer: 0, color: '#fff' };
    let skinTrailParticles = [];
    let lastSplitCallout = '';
    let frames = [], currentFrame = 1, currentRoll = 0, totalScore = 0;
    let gameOver = false, particles = [], confetti = [], impacts = [], sparks = [];
    let strikeCount = 0, stellaCheerShown = false, animFrame = 0, strikeFlashTimer = 0;
    let selectedSkin = 'energy', coins = 800;
    let lightningSeed = [];
    let audioCtx = null;
    let lastBallHitFrame = 0, lastChainHitFrame = 0;
    let screenShake = 0;
    let rollTrail = [];
    let pinsetter = { phase: 'idle', timer: 0, sweepX: 0, placing: [] };
    let canBowl = true;
    let needFullRack = false;
    let pinsKnockedThisRoll = 0;
    let standingAtRollStart = 10;
    const VANISH_Y = () => canvas.height * laneVanishRatio + laneViewShift;

    function project(x, y, z) {
        const depth = CAM_Z - z;
        if (!Number.isFinite(depth) || depth <= S(8)) {
            return { x: canvas.width / 2, y: canvas.height, scale: 0 };
        }
        const scale = PROJ_BASE / depth;
        const t = Math.max(0, Math.min(1, (z - Z_NEAR) / (Z_FAR - Z_NEAR)));
        const laneScale = mobileLaneScale;
        const px = canvas.width / 2 + x * scale * laneScale;
        const py = VANISH_Y() + t * (canvas.height * laneDepthFactor) + (y || 0) * scale * 0.9 + mobileLaneYOffset;
        if (!Number.isFinite(px) || !Number.isFinite(py) || !Number.isFinite(scale)) {
            return { x: canvas.width / 2, y: canvas.height * 0.5, scale: 0.01 };
        }
        return { x: px, y: py, scale: scale * laneScale };
    }

    function quad(x1,z1, x2,z2, x3,z3, x4,z4) {
        const p1=project(x1,0,z1), p2=project(x2,0,z2), p3=project(x3,0,z3), p4=project(x4,0,z4);
        return { p1,p2,p3,p4 };
    }

    function loadCoins() {
        const saved = localStorage.getItem(COINS_KEY);
        if (saved) coins = parseInt(saved, 10);
        coinsEl.textContent = coins;
    }
    function addCoins(n) {
        coins += n;
        localStorage.setItem(COINS_KEY, coins);
        coinsEl.textContent = coins;
    }

    function loadPlayerName() {
        const saved = localStorage.getItem(NAME_KEY);
        if (saved) {
            playerNameEl.value = saved;
            playerLabel.textContent = saved;
            playerAvatar.textContent = saved.charAt(0).toUpperCase();
        }
        playerNameEl.addEventListener('input', () => {
            const n = playerNameEl.value.trim();
            localStorage.setItem(NAME_KEY, n);
            playerLabel.textContent = n || 'Player';
            playerAvatar.textContent = (n || 'P').charAt(0).toUpperCase();
        });
    }

    function renderProLeaderboard() {
        proLeaderboardEl.innerHTML = PRO_BOWLERS.map(p => `
            <div class="flex items-center gap-2 rounded-lg bg-gray-800/60 px-2 py-1.5 text-xs">
                <span class="font-bold text-gray-500 w-4">#${p.rank}</span>
                <span>${p.avatar}</span>
                <div class="flex-1 min-w-0"><div class="font-semibold text-white truncate">${p.name}</div>
                <div class="text-[9px] text-gray-500">${p.title}</div></div>
                <div class="font-bold text-green-400">${p.score}</div>
            </div>`).join('');
    }

    function loadYourScores() {
        const scores = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        if (!scores.length) { yourScoresEl.innerHTML = '<p class="text-gray-500">Finish a game to save!</p>'; return; }
        yourScoresEl.innerHTML = scores.slice(0,6).map((s,i) => {
            const badge = s.score >= 300 ? ' 👑' : s.score >= 241 ? ' 🏆' : '';
            return `<div class="flex justify-between bg-gray-800/40 rounded px-2 py-0.5">
                <span class="text-gray-400">${s.name}</span><span class="font-bold text-yellow-400">${s.score}${badge}</span></div>`;
        }).join('');
    }

    function saveScore(score) {
        const name = playerNameEl.value.trim() || 'Player';
        const scores = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        scores.unshift({ name, score, strikes: strikeCount, date: Date.now() });
        scores.sort((a,b) => b.score - a.score);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(scores.slice(0,20)));
        loadYourScores();
        addCoins(Math.floor(score / 5));
        playerLevel.textContent = Math.min(99, 1 + Math.floor(coins / 200));
        @auth
        if (typeof GameLeaderboard !== 'undefined') {
            GameLeaderboard.submit('galaxy-bowling', score).catch(() => {});
        }
        @endauth
    }

    function getStellaRank(score) {
        if (score >= 300) return '👑 PERFECT! You matched Stella!';
        if (score >= 289) return '🏆 Beat Marcus Lane!';
        if (score >= 276) return '🏆 Beat Jade Split!';
        if (score >= 268) return '🏆 Beat Rico Roll!';
        if (score >= 255) return '🏆 Beat Nova King!';
        if (score >= 241) return '🏆 Beat Ace Gutters!';
        return 'Stella believes in you! ✨';
    }

    function initScoreboard() {
        scoreboardFrames.innerHTML = '';
        for (let i = 1; i <= 10; i++) {
            const d = document.createElement('div');
            d.className = 'score-frame-cell bg-gray-900/80 border border-gray-600/50 rounded px-1 py-0.5 text-center';
            d.innerHTML = `<div class="hud-text-2xs text-gray-500">${i}</div>
                <div class="flex justify-center gap-0.5 hud-text-2xs">
                    <span id="mini-roll1-${i}" class="text-white">-</span>
                    <span id="mini-roll2-${i}" class="text-white">-</span>
                </div>
                <div id="mini-total-${i}" class="hud-text-xs font-bold text-yellow-400">-</div>`;
            scoreboardFrames.appendChild(d);
        }
    }

    function getRackLayout() {
        const spacingX = S(46);
        const spacingZ = S(37);
        // Keep every row above Z_NEAR (~45) or projection collapses rows into one line (1-2-7 bug)
        const headZ = Z_NEAR + spacingZ * 3 + S(12);
        return { headZ, spacingX, spacingZ };
    }

    function buildPinRack() {
        const { headZ, spacingX, spacingZ } = getRackLayout();
        const rack = [];
        let id = 0;
        // Standard triangle from the ball: 1, then 2, then 3, then 4 pins
        for (let row = 0; row < 4; row++) {
            const pinCount = row + 1;
            const z = headZ - row * spacingZ;
            const startX = -(pinCount - 1) * spacingX / 2;
            for (let col = 0; col < pinCount; col++) {
                rack.push(createPinData(id++, startX + col * spacingX, z, row === 0));
            }
        }
        return rack;
    }

    function initPins() {
        pins = buildPinRack();
    }

    function resetBall() {
        rollTrail = [];
        ball = {
            x: 0, y: 0, z: BALL_REST_Z, radius: S(24),
            vx: 0, vy: 0, vz: 0, launchVx: 0, launchVz: 0, hookSpin: 0,
            rotation: 0, rolling: false, rollRamp: 0, squash: 0,
        };
    }

    /* â”€â”€ SOUND â”€â”€ */
    function initAudio() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
    }

    function playTone(freq, duration, type = 'sine', volume = 0.15, delay = 0) {
        if (!soundOn || !audioCtx) return;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = type;
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(0, audioCtx.currentTime + delay);
        gain.gain.linearRampToValueAtTime(volume, audioCtx.currentTime + delay + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + delay + duration);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(audioCtx.currentTime + delay);
        osc.stop(audioCtx.currentTime + delay + duration + 0.05);
    }

    function playNoise(duration, volume = 0.08) {
        if (!soundOn || !audioCtx) return;
        const bufferSize = audioCtx.sampleRate * duration;
        const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) data[i] = (Math.random() * 2 - 1) * (1 - i / bufferSize);
        const src = audioCtx.createBufferSource();
        const gain = audioCtx.createGain();
        src.buffer = buffer;
        gain.gain.value = volume;
        src.connect(gain);
        gain.connect(audioCtx.destination);
        src.start();
    }

    function playSound(name) {
        initAudio();
        switch (name) {
            case 'roll':
                playNoise(0.35, 0.06);
                playTone(80, 0.3, 'sawtooth', 0.04);
                break;
            case 'pinHit':
                playTone(95 + Math.random() * 35, 0.14, 'square', 0.2);
                playTone(240 + Math.random() * 60, 0.08, 'sine', 0.12, 0.015);
                playNoise(0.1, 0.1);
                break;
            case 'pinChain':
                playTone(380 + Math.random() * 120, 0.07, 'triangle', 0.14);
                playNoise(0.05, 0.06);
                break;
            case 'strike':
                [523, 659, 784, 1047].forEach((f, i) => playTone(f, 0.35, 'sine', 0.14, i * 0.08));
                playNoise(0.2, 0.07);
                break;
            case 'spare':
                [440, 554, 659].forEach((f, i) => playTone(f, 0.25, 'triangle', 0.12, i * 0.1));
                break;
            case 'gutter':
                playTone(60, 0.4, 'sawtooth', 0.06);
                break;
            case 'gameover':
                [392, 349, 330, 262].forEach((f, i) => playTone(f, 0.4, 'sine', 0.12, i * 0.15));
                break;
            case 'perfect':
                [523, 659, 784, 988, 1175].forEach((f, i) => playTone(f, 0.5, 'sine', 0.15, i * 0.1));
                playNoise(0.3, 0.08);
                break;
            case 'click':
                playTone(600, 0.06, 'sine', 0.08);
                break;
            case 'cheer':
                playTone(880, 0.2, 'triangle', 0.1);
                playTone(1108, 0.3, 'triangle', 0.1, 0.12);
                break;
            case 'sweep':
                playNoise(0.25, 0.07);
                playTone(140, 0.2, 'sawtooth', 0.05);
                break;
            case 'rack':
                playTone(440, 0.15, 'sine', 0.1);
                playTone(554, 0.2, 'sine', 0.08, 0.08);
                break;
            case 'turkey':
                [523, 659, 784, 988, 1175].forEach((f, i) => playTone(f, 0.22, 'square', 0.11, i * 0.07));
                playNoise(0.25, 0.09);
                break;
        }
    }

    function standingPinCount() {
        return pins.filter(p => !p.knocked).length;
    }

    function startPinsetter(fullRack) {
        needFullRack = fullRack;
        canBowl = false;
        pinsetter.phase = 'fall_wait';
        pinsetter.timer = 50;
        pinsetter.sweepX = S(-220);
        pinsetter.placing = [];
    }

    function createPinData(id, x, z, isHead) {
        return {
            id, x, y: 0, z, knocked: false, vx: 0, vy: 0, vz: 0,
            rotation: 0, isHead, pinRadius: S(10), spin: 0, hitAnim: 0, removed: false
        };
    }

    function buildRackPins() {
        return buildPinRack();
    }

    function updatePinsetter() {
        if (pinsetter.phase === 'idle') return;

        if (pinsetter.phase === 'fall_wait') {
            pinsetter.timer--;
            if (pinsetter.timer <= 0) {
                pinsetter.phase = 'sweep';
                pinsetter.sweepX = S(-220);
                playSound('sweep');
            }
            return;
        }

        if (pinsetter.phase === 'sweep') {
            pinsetter.sweepX += S(9);
            pins.forEach(pin => {
                if (pin.knocked && !pin.removed && pin.x < pinsetter.sweepX + S(30)) {
                    pin.removed = true;
                }
            });
            if (pinsetter.sweepX > S(220)) {
                pins = pins.filter(p => !p.knocked || !p.removed);
                pins = pins.filter(p => !p.knocked);
                if (needFullRack) {
                    pinsetter.phase = 'place';
                    pinsetter.timer = 0;
                    pinsetter.placing = buildRackPins().map(p => ({ ...p, y: S(-120), targetY: 0 }));
                    playSound('rack');
                } else {
                    pinsetter.phase = 'idle';
                    canBowl = standingPinCount() > 0;
                    if (canBowl && currentRoll === 1) {
                        const split = getSplitCallout();
                        if (split && split !== lastSplitCallout) {
                            lastSplitCallout = split;
                            showBowlingToast('⚠️ ' + split, '#f472b6', 130);
                        }
                    }
                }
            }
            return;
        }

        if (pinsetter.phase === 'place') {
            let allPlaced = true;
            pinsetter.placing.forEach((p, i) => {
                p.y += (p.targetY - p.y) * 0.12;
                p.rotation = Math.sin(pinsetter.timer * 0.15 + i) * 0.05;
                if (Math.abs(p.y - p.targetY) > 1) allPlaced = false;
            });
            pinsetter.timer++;
            if (allPlaced || pinsetter.timer > 90) {
                pins = pinsetter.placing.map(p => {
                    const np = createPinData(p.id, p.x, p.z, p.isHead);
                    return np;
                });
                pinsetter.placing = [];
                pinsetter.phase = 'idle';
                canBowl = true;
            }
        }
    }

    function resetGame() {
        frames = []; currentFrame = 1; currentRoll = 0; totalScore = 0;
        gameOver = false; particles = []; confetti = []; impacts = []; sparks = [];
        strikeCount = 0; stellaCheerShown = false; strikeFlashTimer = 0; screenShake = 0; paused = false;
        pinsetter = { phase: 'idle', timer: 0, sweepX: S(-220), placing: [] };
        canBowl = true; needFullRack = false; pinsKnockedThisRoll = 0; standingAtRollStart = 10;
        rollTrail = [];
        gutterGuardUsed = false;
        consecutiveStrikes = 0;
        cinematicTimer = 0;
        canvasToast = { text: '', timer: 0, color: '#fff' };
        skinTrailParticles = [];
        aimLocked = false;
        lastSplitCallout = '';
        pauseOverlay.classList.add('game-overlay-hidden');
        stellaCheerEl.classList.add('game-overlay-hidden');
        strikeFlashEl.classList.add('game-overlay-hidden');
        bowlingToastEl?.classList.add('game-overlay-hidden');
        initScoreboard(); initPins(); resetBall(); updateScore(); drawPinMini();
    }

    function showBowlingToast(text, color = '#fde047', duration = 110) {
        canvasToast = { text, timer: duration, color };
        if (bowlingToastEl) {
            bowlingToastEl.textContent = text;
            bowlingToastEl.style.color = color;
            bowlingToastEl.style.borderColor = color + '66';
            bowlingToastEl.classList.remove('game-overlay-hidden');
        }
    }

    function getSkinFxColors() {
        switch (selectedSkin) {
            case 'stella': return ['#f472b6', '#fbbf24', '#fbcfe8', '#fff'];
            case 'flag': return ['#ef4444', '#3b82f6', '#ffffff', '#fbbf24'];
            case 'polka': return ['#38bdf8', '#ffffff', '#7dd3fc', '#0ea5e9'];
            default: return ['#4ade80', '#22d3ee', '#fef9c3', '#a7f3d0'];
        }
    }

    function getSkinTrailColor() {
        switch (selectedSkin) {
            case 'stella': return '#f472b6';
            case 'flag': return '#60a5fa';
            case 'polka': return '#7dd3fc';
            default: return '#4ade80';
        }
    }

    function clampBallX(x) {
        return Math.max(-LANE_W + S(30), Math.min(LANE_W - S(30), x));
    }

    function getSplitCallout() {
        const standing = pins.filter(p => !p.knocked).map(p => p.id).sort((a, b) => a - b);
        if (standing.length < 2) return null;
        const key = standing.join(',');
        const named = {
            '6,9': '7-10 SPLIT!',
            '3,5': '4-6 SPLIT!',
            '2,9': '3-10 SPLIT!',
            '0,7': '1-8 SPLIT!',
            '1,8': '2-7 SPLIT!',
            '4,8': '5-9 SPLIT!',
        };
        return named[key] || (standing.length === 2 ? 'SPLIT!' : null);
    }

    function cycleLaneTheme() {
        const idx = LANE_THEMES.indexOf(laneTheme);
        laneTheme = LANE_THEMES[(idx + 1) % LANE_THEMES.length];
        localStorage.setItem(THEME_KEY, laneTheme);
        if (themeBtn) themeBtn.textContent = laneTheme === 'nebula' ? '🌠 Nebula' : '🌌 Cosmic';
        showBowlingToast(laneTheme === 'nebula' ? '🌠 Nebula lane!' : '🌌 Cosmic lane!', '#a78bfa', 70);
        playSound('click');
    }

    function calculateScore() {
        totalScore = 0; let fi = 0;
        for (let f = 0; f < 10; f++) {
            if (frames[fi] === undefined) break;
            let fs = 0;
            if (frames[fi] === 10) {
                fs = 10;
                if (frames[fi+1] !== undefined) fs += frames[fi+1];
                if (frames[fi+2] !== undefined) fs += frames[fi+2];
                fi++;
            } else if (frames[fi] + (frames[fi+1]||0) === 10) {
                fs = 10;
                if (frames[fi+2] !== undefined) fs += frames[fi+2];
                fi += 2;
            } else { fs = frames[fi] + (frames[fi+1]||0); fi += 2; }
            totalScore += fs;
            const el = document.getElementById(`mini-total-${f+1}`);
            if (el) el.textContent = totalScore;
        }
    }

    function updateScore() {
        frameEl.textContent = gameOver ? '10/10' : `${Math.min(currentFrame, 10)}/10`;
        scoreEl.textContent = totalScore;
        strikesEl.textContent = strikeCount;
        let ri = 0;
        for (let f = 1; f <= 10; f++) {
            const r1 = document.getElementById(`mini-roll1-${f}`);
            const r2 = document.getElementById(`mini-roll2-${f}`);
            if (!r1) continue;
            if (frames[ri] === undefined) {
                r1.textContent = '-';
                if (r2) r2.textContent = '-';
                continue;
            }

            const roll1 = frames[ri];
            if (roll1 === 10 && f < 10) {
                r1.textContent = 'X';
                r1.className = 'text-yellow-400 font-bold';
                if (r2) { r2.textContent = ''; r2.className = 'text-white'; }
                ri++;
                continue;
            }

            if (f === 10) {
                r1.textContent = roll1 === 10 ? 'X' : roll1;
                r1.className = roll1 === 10 ? 'text-yellow-400 font-bold' : 'text-white';
                ri++;
                if (r2 && frames[ri] !== undefined) {
                    const roll2 = frames[ri];
                    if (roll1 !== 10 && roll1 + roll2 === 10) {
                        r2.textContent = '/';
                        r2.className = 'text-cyan-400 font-bold';
                    } else {
                        r2.textContent = roll2 === 10 ? 'X' : roll2;
                        r2.className = roll2 === 10 ? 'text-yellow-400 font-bold' : 'text-white';
                    }
                    ri++;
                    if (frames[ri] !== undefined) {
                        const roll3 = frames[ri];
                        r2.textContent += (roll3 === 10 ? ' X' : ` ${roll3}`);
                        ri++;
                    }
                } else if (r2) {
                    r2.textContent = '-';
                }
                continue;
            }

            r1.textContent = roll1;
            r1.className = 'text-white';
            ri++;
            if (r2 && frames[ri] !== undefined) {
                const roll2 = frames[ri];
                if (roll1 + roll2 === 10) {
                    r2.textContent = '/';
                    r2.className = 'text-cyan-400 font-bold';
                } else {
                    r2.textContent = roll2;
                    r2.className = 'text-white';
                }
                ri++;
            } else if (r2) {
                r2.textContent = '-';
            }
        }
        drawPinMini();
    }

    function drawPinMini() {
        const w = pinMini.width, h = pinMini.height;
        const sx = w / 40, sy = h / 50;
        pinMiniCtx.clearRect(0, 0, w, h);
        pinMiniCtx.fillStyle = '#1e293b';
        pinMiniCtx.fillRect(0, 0, w, h);
        const standing = pins.filter(p => !p.knocked);
        // Mini map: id 0 = head, then rows 2, 3, 4 toward the top
        const positions = [
            [20, 36],
            [14, 28], [26, 28],
            [10, 18], [20, 18], [30, 18],
            [6, 8], [14, 8], [22, 8], [30, 8],
        ];
        positions.forEach((pos, i) => {
            const px = pos[0] * sx, py = pos[1] * sy;
            const pin = standing.find(p => p.id === i);
            pinMiniCtx.fillStyle = pin ? '#f8fafc' : '#334155';
            pinMiniCtx.beginPath();
            pinMiniCtx.moveTo(px, py - 4 * sy);
            pinMiniCtx.quadraticCurveTo(px + 2.5 * sx, py, px, py + 5 * sy);
            pinMiniCtx.quadraticCurveTo(px - 2.5 * sx, py, px, py - 4 * sy);
            pinMiniCtx.fill();
            if (pin) {
                pinMiniCtx.fillStyle = '#dc2626';
                pinMiniCtx.fillRect(px - 1.5 * sx, py - 0.5 * sy, 3 * sx, 1 * sy);
            }
        });
    }

    function createParticles(x, y, z, color, count = 15) {
        const palette = color ? [color] : getSkinFxColors();
        for (let i = 0; i < count; i++) particles.push({
            x, y, z,
            vx: (Math.random() - 0.5) * 12,
            vy: Math.random() * -6 - 2,
            vz: (Math.random() - 0.5) * 12,
            radius: Math.random() * 5 + 2,
            color: palette[i % palette.length],
            life: 1,
        });
    }

    function spawnImpact(x, y, z, type) {
        const p = project(x, y, z);
        const isBall = type === 'ball';
        const palette = getSkinFxColors();
        impacts.push({
            x: p.x, y: p.y,
            radius: 6 * p.scale,
            maxRadius: (isBall ? 50 : 32) * p.scale,
            life: 1,
            color: isBall ? palette[1] : '#f87171',
            width: isBall ? 4 : 2.5,
        });
        const sparkColors = isBall ? palette : ['#fecaca', '#f87171', '#fff'];
        const count = isBall ? 16 : 10;
        for (let i = 0; i < count; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = (isBall ? 10 : 6) + Math.random() * 8;
            sparks.push({
                x, y: y + 2, z,
                vx: Math.cos(angle) * speed,
                vy: -Math.random() * 5 - 1,
                vz: Math.sin(angle) * speed,
                life: 1,
                color: sparkColors[i % sparkColors.length],
                size: 1.5 + Math.random() * 3
            });
        }
    }

    function isMobileBowling() {
        return window.innerWidth <= 960 || window.matchMedia('(hover: none) and (pointer: coarse)').matches;
    }

    function knockPin(pin, dx, dz, fromBall) {
        if (pin.knocked) return;
        pinsKnockedThisRoll++;
        const mobileBoost = isMobileBowling() ? 1.18 : 1;
        const force = (fromBall ? Math.min(1.95, Math.sqrt(dx * dx + dz * dz) / 14) : 0.82) * mobileBoost;
        pin.knocked = true;
        pin.vx = dx * (fromBall ? 0.78 : 0.55) * force + (Math.random() - 0.5) * 2;
        pin.vy = fromBall ? -7 - Math.random() * 2 : -5;
        pin.vz = dz * (fromBall ? 0.78 : 0.55) * force - (fromBall ? 11 : 7);
        pin.spin = (Math.random() - 0.5) * 0.35;
        pin.rotation = (Math.random() - 0.5) * 0.4;
        pin.hitAnim = 18;

        spawnImpact(pin.x, pin.y, pin.z, fromBall ? 'ball' : 'chain');
        createParticles(pin.x, pin.y, pin.z, null, fromBall ? 14 : 8);

        if (fromBall) {
            if (animFrame - lastBallHitFrame > 2) {
                lastBallHitFrame = animFrame;
                playSound('pinHit');
            }
            if (ball) {
                ball.squash = 10;
                ball.vx *= 0.94;
                ball.vz *= 0.97;
                screenShake = Math.max(screenShake, 5);
            }
        } else if (animFrame - lastChainHitFrame > 4) {
            lastChainHitFrame = animFrame;
            playSound('pinChain');
        }
    }

    function createStellaBurst(x,y,z) {
        ['#f472b6','#fbbf24','#a78bfa','#4ade80'].forEach((c,i) => {
            setTimeout(() => createParticles(x,y,z,c,8), i*30);
        });
    }

    function launchConfetti() {
        const colors = ['#f472b6','#fbbf24','#4ade80','#60a5fa'];
        for (let i=0;i<40;i++) confetti.push({
            x:Math.random()*canvas.width, y:-10,
            vx:(Math.random()-0.5)*4, vy:Math.random()*3+2,
            w:Math.random()*8+4, h:Math.random()*5+2,
            color:colors[Math.floor(Math.random()*colors.length)],
            rotation:Math.random()*Math.PI*2, spin:(Math.random()-0.5)*0.15, life:1
        });
    }

    function nearPinZ() { return pins.length ? Math.max(...pins.map(p => p.z)) : S(160); }

    function showStellaCheer() {
        if (stellaCheerShown || totalScore < 150) return;
        stellaCheerShown = true;
        stellaCheerEl.classList.remove('game-overlay-hidden');
        launchConfetti();
        playSound('cheer');
    }

    function flashStrike() { strikeFlashTimer = 18; strikeFlashEl.classList.remove('game-overlay-hidden'); }

    function getCanvasPos(clientX, clientY) {
        const rect = canvas.getBoundingClientRect();
        if (rect.width < 1 || rect.height < 1) {
            return { x: canvas.width / 2, y: canvas.height * 0.82 };
        }
        const sx = canvas.width / rect.width, sy = canvas.height / rect.height;
        return { x: (clientX - rect.left) * sx, y: (clientY - rect.top) * sy };
    }

    function updateBallPositionFromDrag() {
        if (!ball || ball.rolling || !isDragging || aimLocked) return;
        const pullBack = dragEnd.y - dragStart.y;
        const threshold = isMobileBowling() ? S(10) : S(15);
        if (pullBack >= threshold) {
            aimLocked = true;
            return;
        }
        const laneDelta = (dragEnd.x - dragStart.x) * (LANE_W / Math.max(canvas.width * 0.42, S(120)));
        ball.x = clampBallX(ballXAtDragStart + laneDelta);
    }

    function computeAim() {
        if (!ball) return null;
        const bp = project(ball.x, ball.y, ball.z);
        const pullBack = dragEnd.y - dragStart.y;
        const side = dragStart.x - dragEnd.x;
        const pullDist = Math.sqrt(side * side + pullBack * pullBack);

        if (pullBack < (isMobileBowling() ? S(10) : S(15))) return { tooWeak: true, pullDist, power: 0, bp };

        const powerCap = isMobileBowling() ? 1.65 : 1.55;
        const power = Math.min(pullDist / S(80), powerCap);
        const vx = (side / S(80)) * power * S(10);
        const vz = -S(7) - (pullBack / S(80)) * power * S(13);
        const hookSpin = (side / S(80)) * power * S(2.2);

        return { vx, vz, hookSpin, power, pullDist, pullBack, side, tooWeak: false, bp };
    }

    function simulatePath(vx, vz, hookSpin = 0, steps = 36) {
        let x = ball.x, z = ball.z;
        let cvx = vx, cvz = vz;
        const pts = [];
        for (let i = 0; i < steps; i++) {
            pts.push(project(x, 0, z));
            cvx += hookSpin * 0.09;
            x += cvx; z += cvz;
            cvx *= 0.988; cvz *= 0.996;
            if (z < S(25)) break;
        }
        return pts;
    }

    function rollBall() {
        initAudio();
        if (pinsetter.phase !== 'idle' || standingPinCount() === 0) return;
        const aim = computeAim();
        if (!aim || aim.tooWeak) return;
        standingAtRollStart = standingPinCount();
        pinsKnockedThisRoll = 0;
        rollTrail = [];
        skinTrailParticles = [];
        ball.launchVx = aim.vx;
        ball.launchVz = aim.vz;
        ball.hookSpin = aim.hookSpin || 0;
        ball.vx = 0;
        ball.vz = 0;
        ball.rollRamp = 0;
        ball.rolling = true;
        playSound('roll');
    }

    function isFrameComplete(rollsInFrame, roll1, roll2) {
        if (currentFrame < 10) {
            return rollsInFrame >= 2 || (rollsInFrame === 1 && roll1 === 10);
        }
        if (rollsInFrame === 1) return false;
        if (rollsInFrame === 2) return roll1 !== 10 && roll1 + roll2 !== 10;
        return true;
    }

    function checkCollisions() {
        const pinHitPad = isMobileBowling() ? S(9) : S(5);
        const chainDist = isMobileBowling() ? S(52) : S(42);
        pins.forEach(pin => {
            if (pin.knocked || !ball) return;
            const dx = ball.x - pin.x, dz = ball.z - pin.z;
            const dist = Math.sqrt(dx * dx + dz * dz);
            if (dist < ball.radius + (pin.pinRadius || S(10)) + pinHitPad) {
                knockPin(pin, dx, dz, true);
            }
        });
        pins.forEach((p1, i) => {
            if (!p1.knocked) return;
            pins.forEach((p2, j) => {
                if (i === j || p2.knocked) return;
                const dx = p1.x - p2.x, dz = p1.z - p2.z;
                if (Math.sqrt(dx * dx + dz * dz) < chainDist) {
                    knockPin(p2, dx, dz, false);
                }
            });
        });
    }

    function update() {
        if (paused || gameOver) return;
        const slowMo = cinematicTimer > 0 ? 0.42 : 1;
        animFrame++;
        if (strikeFlashTimer > 0) { strikeFlashTimer--; if (!strikeFlashTimer) strikeFlashEl.classList.add('game-overlay-hidden'); }
        if (cinematicTimer > 0) cinematicTimer--;
        if (canvasToast.timer > 0) {
            canvasToast.timer--;
            if (canvasToast.timer <= 0) bowlingToastEl?.classList.add('game-overlay-hidden');
        }

        if (screenShake > 0) screenShake *= 0.82;
        if (ball && ball.squash > 0) ball.squash--;

        skinTrailParticles = skinTrailParticles.filter(t => {
            t.life -= 0.04 * slowMo;
            return t.life > 0;
        });

        particles = particles.filter(p => {
            p.x+=p.vx; p.y+=p.vy; p.z+=p.vz; p.vy+=0.25; p.life-=0.02;
            return p.life>0 && p.z<500;
        });
        sparks = sparks.filter(s => {
            s.x+=s.vx; s.y+=s.vy; s.z+=s.vz;
            s.vy+=0.2; s.vx*=0.96; s.vz*=0.96;
            s.life-=0.045;
            return s.life>0;
        });
        impacts = impacts.filter(im => {
            im.radius += (im.maxRadius - im.radius) * 0.18;
            im.life -= 0.07;
            return im.life > 0;
        });
        confetti = confetti.filter(c => {
            c.x+=c.vx; c.y+=c.vy; c.vy+=0.06; c.rotation+=c.spin; c.life-=0.008;
            return c.life>0 && c.y<canvas.height+20;
        });

        if (ball && ball.rolling) {
            if (ball.rollRamp < 1) {
                ball.rollRamp = Math.min(1, ball.rollRamp + 0.028 * slowMo);
                const ease = 1 - Math.pow(1 - ball.rollRamp, 2.5);
                ball.vx = ball.launchVx * ease;
                ball.vz = ball.launchVz * ease;
            } else {
                ball.vz *= Math.pow(0.996, slowMo);
                ball.vx *= Math.pow(0.988, slowMo);
            }

            if (ball.hookSpin) ball.vx += ball.hookSpin * 0.013 * slowMo;

            ball.x += ball.vx * slowMo;
            ball.z += ball.vz * slowMo;

            if (animFrame % 2 === 0) {
                skinTrailParticles.push({
                    x: ball.x, z: ball.z,
                    color: getSkinTrailColor(),
                    life: 1,
                    size: ball.radius * 0.35,
                });
            }

            const speed = Math.sqrt(ball.vx * ball.vx + ball.vz * ball.vz);
            ball.rotation += (speed / ball.radius) * slowMo;

            rollTrail.push({ x: ball.x, z: ball.z });
            if (rollTrail.length > 14) rollTrail.shift();

            const gutterEdge = LANE_W - S(10);
            if (ball.x < -gutterEdge) {
                if (!gutterGuardUsed && Math.abs(ball.vx) > 0.8) {
                    gutterGuardUsed = true;
                    ball.x = -gutterEdge + S(28);
                    ball.vx = Math.abs(ball.vx) * 0.45;
                    ball.launchVx = ball.vx;
                    showBowlingToast('🛡️ Gutter guard saved you!', '#67e8f9', 90);
                    playSound('click');
                } else {
                    ball.x = -gutterEdge;
                    ball.vx *= -0.5;
                    ball.launchVx = ball.vx;
                    if (Math.abs(ball.vx) > 1) playSound('gutter');
                }
            }
            if (ball.x > gutterEdge) {
                if (!gutterGuardUsed && Math.abs(ball.vx) > 0.8) {
                    gutterGuardUsed = true;
                    ball.x = gutterEdge - S(28);
                    ball.vx = -Math.abs(ball.vx) * 0.45;
                    ball.launchVx = ball.vx;
                    showBowlingToast('🛡️ Gutter guard saved you!', '#67e8f9', 90);
                    playSound('click');
                } else {
                    ball.x = gutterEdge;
                    ball.vx *= -0.5;
                    ball.launchVx = ball.vx;
                    if (Math.abs(ball.vx) > 1) playSound('gutter');
                }
            }
            checkCollisions();
        }

        pins.forEach(pin => {
            if (!pin.knocked || pin.removed) return;
            pin.x += pin.vx;
            pin.y += pin.vy;
            pin.z += pin.vz;
            pin.vx *= 0.9;
            pin.vy += 0.55;
            pin.vz += 1.8;
            pin.vx += pin.x > 0 ? 0.25 : -0.25;
            pin.rotation += pin.vx * 0.32 + (pin.spin || 0);
            if (pin.spin) pin.spin *= 0.97;
            if (pin.hitAnim > 0) pin.hitAnim--;
            if (pin.y > S(90) || pin.z > S(520)) pin.removed = true;
        });
        pins = pins.filter(p => !p.removed);

        updatePinsetter();

        if (ball && ball.rolling && ball.z < S(-40) && !gameOver) {
            const rollBefore = currentRoll;
            const standing = standingPinCount();
            let knocked = pinsKnockedThisRoll;
            if (knocked === 0 && standingAtRollStart > standing) {
                knocked = standingAtRollStart - standing;
            }

            frames.push(knocked);
            currentRoll++;
            ball.rolling = false;
            rollTrail = [];
            pinsKnockedThisRoll = 0;

            const isStrike = rollBefore === 0 && knocked === 10 && standingAtRollStart === 10;
            if (isStrike) {
                strikeCount++;
                consecutiveStrikes++;
                cinematicTimer = 80;
                screenShake = Math.max(screenShake, 14);
                createStellaBurst(0, 0, nearPinZ());
                createParticles(0, 0, nearPinZ(), null, 30);
                flashStrike();
                addCoins(25);
                playSound('strike');
                if (consecutiveStrikes >= 3) {
                    showBowlingToast('🦃 TURKEY! Three strikes!', '#fde047', 140);
                    playSound('turkey');
                    launchConfetti();
                    launchConfetti();
                } else {
                    showBowlingToast('💥 STRIKE!', '#4ade80', 85);
                }
            } else {
                consecutiveStrikes = 0;
                lastSplitCallout = '';
            }
            if (!isStrike && rollBefore === 1 && currentRoll === 2) {
                const firstRoll = frames[frames.length - 2];
                if (firstRoll + knocked === 10) {
                    playSound('spare');
                    showBowlingToast('✨ SPARE!', '#a78bfa', 80);
                }
            }

            const roll1 = currentRoll === 1 ? frames[frames.length - 1]
                : currentRoll === 2 ? frames[frames.length - 2]
                : frames[frames.length - 3];
            const roll2 = currentRoll >= 2 ? frames[frames.length - (currentRoll === 2 ? 1 : 2)] : undefined;
            const frameDone = isFrameComplete(currentRoll, roll1, roll2);

            if (frameDone) {
                currentRoll = 0;
                if (currentFrame >= 10) {
                    gameOver = true;
                    canBowl = false;
                } else {
                    currentFrame++;
                    startPinsetter(true);
                }
            } else {
                let needFull = knocked === 10;
                if (currentFrame === 10 && currentRoll === 2) {
                    needFull = roll1 === 10 || roll1 + roll2 === 10;
                }
                startPinsetter(needFull);
            }

            calculateScore();
            updateScore();
            if (gameOver) {
                saveScore(totalScore);
                playSound(totalScore >= 300 ? 'perfect' : 'gameover');
            }
            showStellaCheer();
            resetBall();
        }
    }

    /* â”€â”€ RENDERING â”€â”€ */

    function initStars() {
        stars = Array.from({length:300}, () => ({
            x: Math.random()*canvas.width, y: Math.random()*canvas.height*0.55,
            size: Math.random()*2+0.4, twinkle: Math.random()*Math.PI*2, speed: Math.random()*0.04+0.01
        }));
        asteroids = Array.from({length:8}, () => ({
            x: Math.random()*canvas.width, y: Math.random()*canvas.height*0.4,
            size: Math.random()*18+8, rot: Math.random()*Math.PI*2, speed: Math.random()*0.003+0.001
        }));
        lightningSeed = Array.from({length:12}, () => Math.random());
    }

    function drawCosmicWindow() {
        const horizon = VANISH_Y() + S(30);
        const winGrad = ctx.createLinearGradient(0, 0, 0, horizon + 80);
        if (laneTheme === 'nebula') {
            winGrad.addColorStop(0, '#0f0520');
            winGrad.addColorStop(0.35, '#3b0764');
            winGrad.addColorStop(0.65, '#701a75');
            winGrad.addColorStop(1, '#1e1b4b');
        } else {
            winGrad.addColorStop(0, '#1e1b4b');
            winGrad.addColorStop(0.35, '#4c1d95');
            winGrad.addColorStop(0.65, '#1e3a8a');
            winGrad.addColorStop(1, '#0f172a');
        }
        ctx.fillStyle = winGrad;
        ctx.fillRect(0, 0, canvas.width, horizon + 80);

        const t = animFrame * 0.004;
        const nebulae = laneTheme === 'nebula'
            ? [[0.3, 0.22, 90, '#ec4899'], [0.62, 0.18, 70, '#a855f7'], [0.48, 0.38, 100, '#6366f1']]
            : [[0.35, 0.25, 80, '#7c3aed'], [0.6, 0.2, 60, '#2563eb'], [0.45, 0.35, 90, '#db2777']];
        nebulae.forEach(([nx, ny, nr, nc], i) => {
            const cx = canvas.width*nx+Math.sin(t+i)*15, cy = canvas.height*ny;
            const g = ctx.createRadialGradient(cx,cy,0,cx,cy,nr);
            g.addColorStop(0, nc+'55'); g.addColorStop(1,'transparent');
            ctx.fillStyle=g; ctx.fillRect(0,0,canvas.width,canvas.height*0.55);
        });

        stars.forEach(s => {
            const a = 0.4+Math.abs(Math.sin(s.twinkle+animFrame*s.speed))*0.6;
            ctx.beginPath(); ctx.arc(s.x,s.y,s.size,0,Math.PI*2);
            ctx.fillStyle=`rgba(255,255,255,${a})`; ctx.fill();
        });
        asteroids.forEach(a => {
            a.rot+=a.speed;
            ctx.save(); ctx.translate(a.x,a.y); ctx.rotate(a.rot);
            ctx.fillStyle='rgba(100,116,139,0.5)';
            ctx.beginPath();
            for (let i=0;i<6;i++) {
                const ang=i/6*Math.PI*2, r=a.size*(0.7+Math.sin(i*2.3)*0.3);
                const px=Math.cos(ang)*r, py=Math.sin(ang)*r;
                i===0?ctx.moveTo(px,py):ctx.lineTo(px,py);
            }
            ctx.closePath(); ctx.fill(); ctx.restore();
        });
    }

    function drawPillarsAndCeiling() {
        [-LANE_W-S(55), LANE_W+S(55)].forEach(lx => {
            const top = project(lx, 0, S(480)), bot = project(lx, 0, S(-30));
            const pillarY = Math.min(top.y, bot.y);
            const pillarH = Math.max(1, Math.abs(bot.y - top.y));
            const g = ctx.createLinearGradient(top.x, top.y, bot.x, bot.y);
            g.addColorStop(0,'#e2e8f0'); g.addColorStop(0.5,'#94a3b8'); g.addColorStop(1,'#475569');
            ctx.fillStyle=g;
            ctx.fillRect(top.x-8, pillarY, 16, pillarH);
            ctx.fillStyle='rgba(124,58,237,0.15)';
            ctx.fillRect(top.x-10, pillarY, 20, pillarH * 0.6);
        });
        const ceilL = project(-canvas.width*0.3, 0, 500);
        const ceilR = project(canvas.width*0.3, 0, 500);
        ctx.strokeStyle='rgba(148,163,184,0.3)'; ctx.lineWidth=2;
        for (let i=0;i<8;i++) {
            const x = -200+i*55;
            const p1=project(x,0,490), p2=project(x*0.3,0,20);
            ctx.beginPath(); ctx.moveTo(p1.x,p1.y); ctx.lineTo(p2.x,p2.y-20); ctx.stroke();
        }
    }

    function drawSideLane(offsetX) {
        const w = LANE_W * 0.85;
        const bl=project(offsetX-w,0,S(480)), br=project(offsetX+w,0,S(480));
        const tl=project(offsetX-w*0.6,0,S(40)), tr=project(offsetX+w*0.6,0,S(40));
        ctx.beginPath();
        ctx.moveTo(bl.x,bl.y); ctx.lineTo(br.x,br.y); ctx.lineTo(tr.x,tr.y); ctx.lineTo(tl.x,tl.y); ctx.closePath();
        const lg=ctx.createLinearGradient(0,bl.y,0,tl.y);
        lg.addColorStop(0,'#6b4c2a'); lg.addColorStop(0.5,'#8b6914'); lg.addColorStop(1,'#5a3d1e');
        ctx.fillStyle=lg; ctx.globalAlpha=0.55; ctx.fill(); ctx.globalAlpha=1;
    }

    function drawLaneSurface(offsetX, isMain) {
        const w = isMain ? LANE_W : LANE_W*0.85;
        const bl=project(offsetX-w,0,S(490)), br=project(offsetX+w,0,S(490));
        const tl=project(offsetX-w*0.55,0,S(35)), tr=project(offsetX+w*0.55,0,S(35));

        if (!isMain) { drawSideLane(offsetX); return; }

        // Gutters
        const glb=project(offsetX-w-S(18),0,S(490)), grb=project(offsetX+w+S(18),0,S(490));
        const glt=project(offsetX-w*0.55-S(14),0,S(35)), grt=project(offsetX+w*0.55+S(14),0,S(35));
        ctx.beginPath(); ctx.moveTo(bl.x,bl.y); ctx.lineTo(glb.x,glb.y); ctx.lineTo(glt.x,glt.y); ctx.lineTo(tl.x,tl.y); ctx.closePath();
        ctx.fillStyle='#0f172a'; ctx.fill();
        ctx.beginPath(); ctx.moveTo(br.x,br.y); ctx.lineTo(grb.x,grb.y); ctx.lineTo(grt.x,grt.y); ctx.lineTo(tr.x,tr.y); ctx.closePath();
        ctx.fillStyle='#0f172a'; ctx.fill();

        // Wood lane base
        const lg=ctx.createLinearGradient(0,bl.y,0,tl.y);
        if (laneTheme === 'nebula') {
            lg.addColorStop(0, '#6d4c41'); lg.addColorStop(0.25, '#8d6e63'); lg.addColorStop(0.55, '#7e57c2'); lg.addColorStop(0.8, '#5e35b1'); lg.addColorStop(1, '#4527a0');
        } else {
            lg.addColorStop(0,'#b8956a'); lg.addColorStop(0.25,'#d4b87a'); lg.addColorStop(0.55,'#c9a66b'); lg.addColorStop(0.8,'#9a7848'); lg.addColorStop(1,'#7a5c30');
        }
        ctx.beginPath();
        ctx.moveTo(bl.x,bl.y); ctx.lineTo(br.x,br.y); ctx.lineTo(tr.x,tr.y); ctx.lineTo(tl.x,tl.y); ctx.closePath();
        ctx.fillStyle=lg; ctx.fill();

        // Wood planks (lengthwise)
        if (isMain) {
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(bl.x,bl.y); ctx.lineTo(br.x,br.y); ctx.lineTo(tr.x,tr.y); ctx.lineTo(tl.x,tl.y); ctx.closePath();
            ctx.clip();
            for (let i = -LANE_W; i <= LANE_W; i += S(22)) {
                const pb = project(offsetX + i, 0, S(490)), pt = project(offsetX + i * 0.55, 0, S(35));
                ctx.strokeStyle = 'rgba(60,40,20,0.18)';
                ctx.lineWidth = 1.5;
                ctx.beginPath(); ctx.moveTo(pb.x, pb.y); ctx.lineTo(pt.x, pt.y); ctx.stroke();
                ctx.strokeStyle = 'rgba(255,255,255,0.06)';
                ctx.lineWidth = 0.8;
                ctx.beginPath(); ctx.moveTo(pb.x + 1, pb.y); ctx.lineTo(pt.x + 1, pt.y); ctx.stroke();
            }
            ctx.restore();
        }

        // Gloss reflection
        const refl=ctx.createLinearGradient(bl.x,bl.y,tr.x,tl.y);
        refl.addColorStop(0,'rgba(255,255,255,0.22)'); refl.addColorStop(0.45,'rgba(255,255,255,0.05)'); refl.addColorStop(1,'rgba(255,255,255,0.15)');
        ctx.fillStyle=refl; ctx.fill();

        // Lane edge glow
        ctx.shadowColor = laneTheme === 'nebula' ? '#e879f9' : '#22d3ee'; ctx.shadowBlur=8;
        ctx.strokeStyle = laneTheme === 'nebula' ? 'rgba(232,121,249,0.55)' : 'rgba(34,211,238,0.5)'; ctx.lineWidth=2;
        ctx.beginPath(); ctx.moveTo(bl.x,bl.y); ctx.lineTo(tl.x,tl.y); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(br.x,br.y); ctx.lineTo(tr.x,tr.y); ctx.stroke();
        ctx.shadowBlur=0;

        // Arrow guides
        for (let z=S(180); z<=S(400); z+=S(70)) {
            const p = project(offsetX, 0, z);
            const sz = 8 * p.scale;
            ctx.fillStyle='rgba(255,255,255,0.55)';
            ctx.beginPath();
            ctx.moveTo(p.x, p.y - sz*1.5);
            ctx.lineTo(p.x - sz, p.y);
            ctx.lineTo(p.x + sz, p.y);
            ctx.closePath(); ctx.fill();
        }

        // Dots mid-lane
        for (let z=S(120); z<=S(320); z+=S(50)) {
            const p = project(offsetX, 0, z);
            ctx.beginPath(); ctx.arc(p.x, p.y, 2*p.scale, 0, Math.PI*2);
            ctx.fillStyle='rgba(0,0,0,0.2)'; ctx.fill();
        }

        if (isMain) drawBowlerArea(offsetX);
    }

    function drawBowlerArea(offsetX) {
        // Foul line
        const flL = project(offsetX - LANE_W, 0, FOUL_LINE_Z);
        const flR = project(offsetX + LANE_W, 0, FOUL_LINE_Z);
        ctx.strokeStyle = 'rgba(239,68,68,0.75)';
        ctx.lineWidth = Math.max(2, S(3));
        ctx.beginPath(); ctx.moveTo(flL.x, flL.y); ctx.lineTo(flR.x, flR.y); ctx.stroke();

        // Approach dots (bowler's steps)
        [S(400), S(418), BALL_REST_Z, S(455)].forEach(z => {
            const p = project(offsetX, 0, z);
            ctx.beginPath(); ctx.arc(p.x, p.y, 3.5 * p.scale, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255,255,255,0.4)'; ctx.fill();
        });

        // Ball rest circle â€” player's spot
        const spot = project(offsetX, 0, BALL_REST_Z);
        const spotRx = S(38) * spot.scale, spotRy = S(14) * spot.scale;
        ctx.strokeStyle = 'rgba(34,211,238,0.55)';
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 4]);
        ctx.beginPath(); ctx.ellipse(spot.x, spot.y, spotRx, spotRy, 0, 0, Math.PI * 2); ctx.stroke();
        ctx.setLineDash([]);

        const spotGrad = ctx.createRadialGradient(spot.x, spot.y, 0, spot.x, spot.y, spotRx);
        spotGrad.addColorStop(0, 'rgba(34,211,238,0.12)');
        spotGrad.addColorStop(1, 'transparent');
        ctx.fillStyle = spotGrad;
        ctx.beginPath(); ctx.ellipse(spot.x, spot.y, spotRx, spotRy, 0, 0, Math.PI * 2); ctx.fill();

        // Aim marker triangle
        const tri = project(offsetX, 0, S(462));
        const ts = S(11) * tri.scale;
        ctx.fillStyle = 'rgba(255,255,255,0.55)';
        ctx.beginPath();
        ctx.moveTo(tri.x, tri.y - ts * 1.2);
        ctx.lineTo(tri.x - ts * 0.75, tri.y + ts * 0.6);
        ctx.lineTo(tri.x + ts * 0.75, tri.y + ts * 0.6);
        ctx.closePath(); ctx.fill();

        ctx.font = `bold ${Fs(11 * spot.scale / gameScale)}px Arial`;
        ctx.fillStyle = 'rgba(34,211,238,0.65)';
        ctx.textAlign = 'center';
        ctx.fillText('YOUR SPOT', spot.x, spot.y + spotRy + S(14) * spot.scale);
    }

    function drawPinBody(r, h, isHead) {
        const bw = r * 0.4;
        ctx.beginPath();
        ctx.moveTo(-bw, h * 0.42);
        ctx.bezierCurveTo(-r * 0.52, h * 0.3, -r * 0.58, h * 0.02, -r * 0.4, -h * 0.06);
        ctx.bezierCurveTo(-r * 0.22, -h * 0.22, -r * 0.14, -h * 0.34, -r * 0.1, -h * 0.4);
        ctx.quadraticCurveTo(0, -h * 0.5, r * 0.1, -h * 0.4);
        ctx.bezierCurveTo(r * 0.14, -h * 0.34, r * 0.22, -h * 0.22, r * 0.4, -h * 0.06);
        ctx.bezierCurveTo(r * 0.58, h * 0.02, r * 0.52, h * 0.3, bw, h * 0.42);
        ctx.quadraticCurveTo(0, h * 0.48, -bw, h * 0.42);
        ctx.closePath();

        const bodyGrad = ctx.createLinearGradient(-r * 0.6, -h * 0.4, r * 0.5, h * 0.45);
        bodyGrad.addColorStop(0, '#e2e8f0');
        bodyGrad.addColorStop(0.25, '#ffffff');
        bodyGrad.addColorStop(0.55, '#f1f5f9');
        bodyGrad.addColorStop(0.85, '#cbd5e1');
        bodyGrad.addColorStop(1, '#94a3b8');
        ctx.fillStyle = bodyGrad;
        ctx.fill();

        // Subtle edge shading
        ctx.strokeStyle = 'rgba(100,116,139,0.25)';
        ctx.lineWidth = Math.max(0.5, r * 0.04);
        ctx.stroke();

        // Highlight reflection
        ctx.beginPath();
        ctx.moveTo(-r * 0.06, -h * 0.38);
        ctx.quadraticCurveTo(-r * 0.18, -h * 0.05, -r * 0.12, h * 0.28);
        ctx.strokeStyle = 'rgba(255,255,255,0.45)';
        ctx.lineWidth = r * 0.07;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Neck band (red)
        ctx.fillStyle = isHead ? '#be185d' : '#b91c1c';
        ctx.beginPath();
        ctx.ellipse(0, -h * 0.04, r * 0.24, h * 0.028, 0, 0, Math.PI * 2);
        ctx.fill();

        // Belly band (blue)
        ctx.fillStyle = '#1e40af';
        ctx.beginPath();
        ctx.ellipse(0, h * 0.14, r * 0.3, h * 0.032, 0, 0, Math.PI * 2);
        ctx.fill();

        // Base collar
        ctx.fillStyle = '#64748b';
        ctx.beginPath();
        ctx.ellipse(0, h * 0.4, r * 0.36, h * 0.045, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = '#475569';
        ctx.lineWidth = 1;
        ctx.stroke();
    }

    function drawPinsetter() {
        if (pinsetter.phase === 'idle' || pinsetter.phase === 'fall_wait') return;

        const deckZ = S(120);
        const deck = project(0, 0, deckZ);
        const left = project(-LANE_W - S(20), 0, deckZ);
        const right = project(LANE_W + S(20), 0, deckZ);

        // Pin deck housing
        ctx.fillStyle = 'rgba(30,41,59,0.85)';
        ctx.fillRect(left.x, deck.y - 18, right.x - left.x, 22);
        ctx.strokeStyle = 'rgba(34,211,238,0.5)';
        ctx.lineWidth = 2;
        ctx.strokeRect(left.x, deck.y - 18, right.x - left.x, 22);

        if (pinsetter.phase === 'sweep' || pinsetter.phase === 'place') {
            const sweepP = project(pinsetter.sweepX, 0, deckZ - 15);
            const armW = S(50) * sweepP.scale;
            const armH = S(28) * sweepP.scale;

            ctx.save();
            ctx.translate(sweepP.x, sweepP.y - armH * 0.5);
            ctx.fillStyle = '#475569';
            ctx.fillRect(-armW / 2, -armH * 0.3, armW, armH);
            const ag = ctx.createLinearGradient(-armW / 2, 0, armW / 2, 0);
            ag.addColorStop(0, 'rgba(34,211,238,0.3)');
            ag.addColorStop(0.5, 'rgba(167,139,250,0.5)');
            ag.addColorStop(1, 'rgba(34,211,238,0.3)');
            ctx.fillStyle = ag;
            ctx.fillRect(-armW / 2, armH * 0.1, armW, armH * 0.25);
            ctx.fillStyle = '#22d3ee';
            ctx.shadowColor = '#22d3ee';
            ctx.shadowBlur = 12;
            ctx.fillRect(-armW * 0.15, -armH * 0.5, armW * 0.3, armH * 0.4);
            ctx.shadowBlur = 0;
            ctx.restore();

            ctx.font = `bold ${Math.max(9, 10 * deck.scale)}px Arial`;
            ctx.fillStyle = 'rgba(34,211,238,0.8)';
            ctx.textAlign = 'center';
            ctx.fillText('PINSETTER', sweepP.x, sweepP.y - armH * 0.8);
        }

        if (pinsetter.phase === 'place') {
            ctx.font = `bold ${Math.max(10, 11 * deck.scale)}px Arial`;
            ctx.fillStyle = 'rgba(167,139,250,0.9)';
            ctx.textAlign = 'center';
            ctx.fillText('Setting new pins...', deck.x, deck.y - 35);
        }
    }

    function drawPin(pin) {
        if (pin.removed) return;
        const p = project(pin.x, pin.y, pin.z);
        if (p.scale <= 0 || p.y > canvas.height + S(60)) return;
        const r = (pin.pinRadius || S(10)) * p.scale;
        const h = S(38) * p.scale;

        // Ground shadow
        ctx.save();
        ctx.translate(p.x, p.y + h * 0.44);
        ctx.scale(1, 0.3);
        ctx.fillStyle = 'rgba(0,0,0,0.22)';
        ctx.beginPath();
        ctx.ellipse(0, 0, r * 0.5, r * 0.35, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();

        ctx.save();
        ctx.translate(p.x, p.y);

        if (!pin.knocked) {
            if (pin.isHead) {
                ctx.shadowColor = 'rgba(236,72,153,0.45)';
                ctx.shadowBlur = 8 * p.scale;
            }
            drawPinBody(r, h, pin.isHead);
            ctx.shadowBlur = 0;
        } else {
            const tumble = pin.rotation + (pin.hitAnim > 0 ? Math.sin(pin.hitAnim * 0.6) * 0.25 : 0);
            ctx.rotate(tumble + Math.min(pin.y * 0.02, 1.2));
            const lift = Math.min(pin.hitAnim * 0.04, 1) * h * 0.3;
            ctx.translate(0, -lift);
            ctx.globalAlpha = Math.max(0.35, 0.9 - pin.y * 0.008);
            drawPinBody(r, h, pin.isHead);
            ctx.globalAlpha = 1;
        }
        ctx.restore();
    }

    function drawBallSkin(cx, cy, r, skin, isPreview) {
        const s = isPreview ? 1 : 1;
        ctx.save(); ctx.translate(cx, cy);

        if (skin === 'energy') {
            const g=ctx.createRadialGradient(-r*0.3,-r*0.3,0,0,0,r);
            g.addColorStop(0,'#bbf7d0'); g.addColorStop(0.3,'#4ade80'); g.addColorStop(0.7,'#16a34a'); g.addColorStop(1,'#14532d');
            ctx.shadowColor='#4ade80'; ctx.shadowBlur=isPreview?0:12;
            ctx.beginPath(); ctx.arc(0,0,r,0,Math.PI*2); ctx.fillStyle=g; ctx.fill();
            ctx.shadowBlur=0;
            ctx.strokeStyle='#86efac'; ctx.lineWidth=isPreview?1:2.5;
            for (let i=0;i<5;i++) {
                const seed=lightningSeed[i]||0;
                ctx.beginPath(); ctx.moveTo(0,0);
                let lx=0, ly=0;
                for (let j=0;j<4;j++) {
                    lx+=(Math.sin(seed*10+j*2.1+animFrame*0.08)-0.5)*r*0.45;
                    ly+=(Math.cos(seed*7+j*1.7+animFrame*0.06)-0.5)*r*0.45;
                    ctx.lineTo(lx,ly);
                }
                ctx.stroke();
            }
        } else if (skin === 'stella') {
            const g=ctx.createRadialGradient(-r*0.3,-r*0.3,0,0,0,r);
            g.addColorStop(0,'#fce7f3'); g.addColorStop(0.4,'#ec4899'); g.addColorStop(0.8,'#9d174d'); g.addColorStop(1,'#500724');
            ctx.shadowColor='#f472b6'; ctx.shadowBlur=isPreview?0:20;
            ctx.beginPath(); ctx.arc(0,0,r,0,Math.PI*2); ctx.fillStyle=g; ctx.fill();
            ctx.shadowBlur=0;
            ctx.font=`bold ${r*0.9}px serif`; ctx.fillStyle='#fbbf24'; ctx.textAlign='center'; ctx.textBaseline='middle';
            ctx.fillText('✦',0,0);
        } else if (skin === 'flag') {
            ctx.save();
            ctx.beginPath();
            ctx.arc(0, 0, r, 0, Math.PI * 2);
            ctx.clip();

            ctx.fillStyle = '#f8fafc';
            ctx.fillRect(-r, -r, r * 2, r * 2);

            ctx.fillStyle = '#dc2626';
            const stripeH = (r * 2) / 7;
            for (let i = 0; i < 7; i += 2) {
                ctx.fillRect(-r, -r + i * stripeH, r * 2, stripeH);
            }

            ctx.fillStyle = '#1e3a8a';
            ctx.fillRect(-r, -r, r * 1.05, r * 0.78);

            ctx.fillStyle = '#f8fafc';
            [[-0.55, -0.58], [-0.2, -0.58], [0.15, -0.58], [-0.55, -0.28], [-0.2, -0.28], [0.15, -0.28], [-0.38, -0.43]].forEach(([sx, sy]) => {
                ctx.beginPath();
                ctx.arc(sx * r, sy * r, r * 0.05, 0, Math.PI * 2);
                ctx.fill();
            });

            ctx.restore();

            const shade = ctx.createRadialGradient(-r * 0.35, -r * 0.35, r * 0.05, 0, 0, r);
            shade.addColorStop(0, 'rgba(255,255,255,0.2)');
            shade.addColorStop(0.55, 'rgba(255,255,255,0)');
            shade.addColorStop(1, 'rgba(0,0,0,0.22)');
            ctx.beginPath();
            ctx.arc(0, 0, r, 0, Math.PI * 2);
            ctx.fillStyle = shade;
            ctx.fill();
        } else if (skin === 'polka') {
            ctx.save();
            ctx.beginPath();
            ctx.arc(0, 0, r, 0, Math.PI * 2);
            ctx.clip();
            const g = ctx.createRadialGradient(-r * 0.2, -r * 0.2, 0, 0, 0, r);
            g.addColorStop(0, '#bae6fd');
            g.addColorStop(1, '#0284c7');
            ctx.fillStyle = g;
            ctx.fillRect(-r, -r, r * 2, r * 2);
            ctx.fillStyle = 'rgba(255,255,255,0.85)';
            [[-0.3, -0.2], [0.25, -0.3], [-0.1, 0.3], [0.35, 0.15], [-0.35, 0.1], [0.05, -0.45]].forEach(([dx, dy]) => {
                ctx.beginPath();
                ctx.arc(dx * r, dy * r, r * 0.12, 0, Math.PI * 2);
                ctx.fill();
            });
            ctx.restore();
        }

        if (!isPreview) {
            ctx.fillStyle='#0f172a';
            [[-r*0.25,-r*0.15],[r*0.25,-r*0.15],[0,r*0.22]].forEach(([hx,hy]) => {
                ctx.beginPath(); ctx.arc(hx,hy,r*0.11,0,Math.PI*2); ctx.fill();
            });
        }
        ctx.restore();
    }

    function drawRollTrail() {
        if (!ball || !ball.rolling || rollTrail.length < 2) return;
        const c = getSkinTrailColor();
        const r = parseInt(c.slice(1, 3), 16);
        const g = parseInt(c.slice(3, 5), 16);
        const b = parseInt(c.slice(5, 7), 16);
        ctx.save();
        rollTrail.forEach((pt, i) => {
            const p = project(pt.x, 0, pt.z);
            if (p.scale <= 0) return;
            const alpha = (i / rollTrail.length) * 0.35;
            const tr = ball.radius * p.scale * (0.35 + i / rollTrail.length * 0.45);
            ctx.beginPath();
            ctx.ellipse(p.x, p.y + tr * 0.2, tr, tr * 0.28, 0, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${r},${g},${b},${alpha})`;
            ctx.fill();
        });
        ctx.restore();
    }

    function drawSkinTrailParticles() {
        if (!skinTrailParticles.length) return;
        ctx.save();
        skinTrailParticles.forEach(t => {
            const p = project(t.x, 0, t.z);
            if (p.scale <= 0) return;
            ctx.beginPath();
            ctx.arc(p.x, p.y, t.size * p.scale * t.life, 0, Math.PI * 2);
            const c = t.color;
            const r = parseInt(c.slice(1, 3), 16);
            const g = parseInt(c.slice(3, 5), 16);
            const b = parseInt(c.slice(5, 7), 16);
            ctx.fillStyle = `rgba(${r},${g},${b},${t.life * 0.65})`;
            ctx.fill();
        });
        ctx.restore();
    }

    function drawBallFingerHoles(r) {
        ctx.fillStyle = 'rgba(15, 23, 42, 0.82)';
        [[-r * 0.22, -r * 0.12, r * 0.1], [r * 0.24, -r * 0.08, r * 0.085], [r * 0.04, r * 0.2, r * 0.075]].forEach(([hx, hy, hr]) => {
            ctx.beginPath();
            ctx.arc(hx, hy, hr, 0, Math.PI * 2);
            ctx.fill();
        });
    }

    function drawBall() {
        if (!ball) return;
        const p = project(ball.x, ball.y, ball.z);
        if (p.scale <= 0) return;
        const r = ball.radius * p.scale;

        drawRollTrail();

        // Reflection on lane
        const rp = project(ball.x, 0, ball.z);
        ctx.save();
        ctx.globalAlpha = 0.18;
        const glowColor = selectedSkin==='energy'?'#4ade80':selectedSkin==='stella'?'#f472b6':'#60a5fa';
        ctx.fillStyle = glowColor;
        ctx.beginPath(); ctx.ellipse(rp.x, rp.y + r*0.35, r*0.55, r*0.12, 0, 0, Math.PI*2); ctx.fill();
        ctx.globalAlpha=1; ctx.restore();

        ctx.save();
        ctx.translate(p.x, p.y);
        if (ball.rolling) {
            ctx.rotate(ball.rotation);
        } else if (ball.squash > 0) {
            const sq = 1 + (ball.squash / 10) * 0.12;
            ctx.scale(1.08 / sq, sq);
        }
        drawBallSkin(0, 0, r, selectedSkin, false);
        if (ball.rolling || selectedSkin === 'energy' || selectedSkin === 'polka') {
            drawBallFingerHoles(r);
        }
        ctx.restore();
    }

    function drawImpacts() {
        impacts.forEach(im => {
            ctx.beginPath();
            ctx.arc(im.x, im.y, im.radius, 0, Math.PI * 2);
            ctx.strokeStyle = im.color;
            ctx.globalAlpha = im.life * 0.85;
            ctx.lineWidth = im.width * im.life;
            ctx.stroke();
            ctx.beginPath();
            ctx.arc(im.x, im.y, im.radius * 0.55, 0, Math.PI * 2);
            ctx.fillStyle = im.color;
            ctx.globalAlpha = im.life * 0.2;
            ctx.fill();
        });
        ctx.globalAlpha = 1;
    }

    function drawSparks() {
        sparks.forEach(s => {
            const p = project(s.x, s.y, s.z);
            if (p.scale <= 0) return;
            ctx.beginPath();
            ctx.arc(p.x, p.y, s.size * p.scale * s.life, 0, Math.PI * 2);
            ctx.fillStyle = s.color;
            ctx.globalAlpha = s.life;
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 1;
            ctx.globalAlpha = s.life * 0.5;
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            ctx.lineTo(p.x - s.vx * p.scale * 0.8, p.y - s.vy * p.scale * 0.8);
            ctx.stroke();
        });
        ctx.globalAlpha = 1;
    }

    function drawParticles3D() {
        particles.forEach(pt => {
            const p=project(pt.x,pt.y,pt.z);
            if (p.scale<=0) return;
            ctx.beginPath(); ctx.arc(p.x,p.y,pt.radius*p.scale,0,Math.PI*2);
            ctx.fillStyle=pt.color; ctx.globalAlpha=pt.life*p.scale; ctx.fill();
        });
        ctx.globalAlpha=1;
    }

    function drawConfetti() {
        confetti.forEach(c => {
            ctx.save(); ctx.translate(c.x,c.y); ctx.rotate(c.rotation);
            ctx.globalAlpha=c.life; ctx.fillStyle=c.color;
            ctx.fillRect(-c.w/2,-c.h/2,c.w,c.h); ctx.restore();
        });
        ctx.globalAlpha=1;
    }

    function drawAimLine() {
        if (!isDragging || !ball || ball.rolling || gameOver || paused || !canBowl || pinsetter.phase !== 'idle' || standingPinCount() === 0) return;
        const aim = computeAim();
        const bp = project(ball.x, ball.y, ball.z);

        // Slingshot band: cursor â†’ ball
        ctx.beginPath();
        ctx.moveTo(dragEnd.x, dragEnd.y);
        ctx.lineTo(bp.x, bp.y);
        const bandGrad = ctx.createLinearGradient(dragEnd.x, dragEnd.y, bp.x, bp.y);
        bandGrad.addColorStop(0, 'rgba(74,222,128,0.35)');
        bandGrad.addColorStop(1, 'rgba(74,222,128,0.95)');
        ctx.strokeStyle = bandGrad;
        ctx.lineWidth = Math.max(2, S(4));
        ctx.setLineDash([]);
        ctx.stroke();

        if (!aim || aim.tooWeak) {
            ctx.font = `bold ${Fs(13)}px Arial`;
            ctx.fillStyle = 'rgba(251,191,36,0.9)';
            ctx.textAlign = 'center';
            ctx.fillText('Slide ← → to position · pull down for power', bp.x, bp.y + S(55));
            return;
        }

        const path = simulatePath(aim.vx, aim.vz, aim.hookSpin || 0);

        if (path.length > 1) {
            const ang = Math.atan2(path[1].y - bp.y, path[1].x - bp.x);
            const ah = S(16);
            ctx.beginPath();
            ctx.moveTo(bp.x, bp.y);
            ctx.lineTo(bp.x + Math.cos(ang) * ah, bp.y + Math.sin(ang) * ah);
            ctx.lineTo(bp.x + Math.cos(ang - 0.45) * ah * 0.65, bp.y + Math.sin(ang - 0.45) * ah * 0.65);
            ctx.lineTo(bp.x + Math.cos(ang + 0.45) * ah * 0.65, bp.y + Math.sin(ang + 0.45) * ah * 0.65);
            ctx.closePath();
            ctx.fillStyle = 'rgba(74,222,128,0.9)';
            ctx.fill();
        }

        path.forEach((pt, i) => {
            if (i % 2 !== 0) return;
            const alpha = 0.35 + (i / path.length) * 0.55;
            const rad = S(3) + (i / path.length) * S(4);
            ctx.beginPath();
            ctx.arc(pt.x, pt.y, rad, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(250,204,21,${alpha})`;
            ctx.fill();
        });

        // Path line
        if (path.length > 1) {
            ctx.beginPath();
            ctx.moveTo(path[0].x, path[0].y);
            for (let i = 1; i < path.length; i++) ctx.lineTo(path[i].x, path[i].y);
            ctx.strokeStyle = 'rgba(250,204,21,0.45)';
            ctx.lineWidth = 2;
            ctx.setLineDash([6, 8]);
            ctx.stroke();
            ctx.setLineDash([]);

            const end = path[path.length - 1];
            ctx.beginPath();
            ctx.arc(end.x, end.y, S(10), 0, Math.PI * 2);
            ctx.strokeStyle = 'rgba(250,204,21,0.7)';
            ctx.lineWidth = 2;
            ctx.stroke();
        }

        // Left / right aim guide at pins
        const headPin = pins.find(p => p.isHead && !p.knocked);
        if (headPin) {
            const hp = project(headPin.x, headPin.y, headPin.z);
            ctx.setLineDash([4, 6]);
            ctx.strokeStyle = 'rgba(244,114,182,0.35)';
            ctx.lineWidth = 1.5;
            ctx.beginPath(); ctx.moveTo(hp.x, hp.y); ctx.lineTo(bp.x, bp.y); ctx.stroke();
            ctx.setLineDash([]);
        }

        // Power meter
        const pct = Math.round(aim.power * 100);
        const meterW = S(70), meterH = S(8);
        const mx = bp.x - meterW / 2, my = bp.y + S(42);
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        ctx.fillRect(mx - 2, my - 2, meterW + 4, meterH + 16);
        ctx.fillStyle = 'rgba(55,65,81,0.9)';
        ctx.fillRect(mx, my, meterW, meterH);
        const pGrad = ctx.createLinearGradient(mx, 0, mx + meterW, 0);
        pGrad.addColorStop(0, '#4ade80');
        pGrad.addColorStop(0.7, '#fbbf24');
        pGrad.addColorStop(1, '#ef4444');
        ctx.fillStyle = pGrad;
        ctx.fillRect(mx, my, meterW * aim.power, meterH);
        ctx.font = `bold ${Fs(11)}px Arial`;
        ctx.fillStyle = '#fff';
        ctx.textAlign = 'center';
        ctx.fillText(`POWER ${pct}%`, bp.x, my + meterH + S(11));
        ctx.font = `${Fs(10)}px Arial`;
        ctx.fillStyle = 'rgba(255,255,255,0.6)';
        ctx.fillText(aim.vx > 0.5 ? 'shot → right' : aim.vx < -0.5 ? '← left shot' : 'straight', bp.x, my - 6);
        if (Math.abs(aim.hookSpin || 0) > 0.4) {
            ctx.fillStyle = 'rgba(167,139,250,0.75)';
            ctx.fillText('↻ curve', bp.x, my - S(18));
        }
    }

    function drawSpareHelper() {
        if (!ball || ball.rolling || gameOver || paused || !canBowl || pinsetter.phase !== 'idle') return;
        if (currentRoll !== 1 || standingPinCount() === 0 || standingPinCount() === 10) return;
        const bp = project(ball.x, ball.y, ball.z);
        const standing = pins.filter(p => !p.knocked);
        ctx.save();
        ctx.setLineDash([5, 7]);
        ctx.lineWidth = 2;
        standing.forEach(pin => {
            const pp = project(pin.x, pin.y, pin.z);
            ctx.strokeStyle = 'rgba(244,114,182,0.45)';
            ctx.beginPath();
            ctx.moveTo(bp.x, bp.y);
            ctx.lineTo(pp.x, pp.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.arc(pp.x, pp.y, S(8), 0, Math.PI * 2);
            ctx.strokeStyle = 'rgba(250,204,21,0.7)';
            ctx.stroke();
        });
        ctx.setLineDash([]);
        ctx.font = `bold ${Fs(11)}px Arial`;
        ctx.fillStyle = 'rgba(250,204,21,0.9)';
        ctx.textAlign = 'center';
        ctx.fillText('Spare aim — pick a pin line', bp.x, bp.y + S(62));
        ctx.restore();
    }

    function drawGameOver() {
        if (!gameOver) return;
        ctx.fillStyle='rgba(0,0,0,0.85)'; ctx.fillRect(0,0,canvas.width,canvas.height);
        if (totalScore>=300) {
            const g=ctx.createLinearGradient(0,canvas.height/2-S(60),0,canvas.height/2);
            g.addColorStop(0,'#f472b6'); g.addColorStop(0.5,'#fbbf24'); g.addColorStop(1,'#a78bfa');
            ctx.font=`bold ${Fs(46)}px Arial`; ctx.fillStyle=g; ctx.textAlign='center';
            ctx.fillText('👑 PERFECT GALAXY GAME! 👑', canvas.width/2, canvas.height/2-S(45));
        } else {
            ctx.font=`bold ${Fs(42)}px Arial`; ctx.fillStyle='#fbbf24'; ctx.textAlign='center';
            ctx.fillText('GAME COMPLETE', canvas.width/2, canvas.height/2-S(40));
        }
        ctx.font=`bold ${Fs(32)}px Arial`; ctx.fillStyle='#4ade80';
        ctx.fillText(`SCORE: ${totalScore}`, canvas.width/2, canvas.height/2+S(10));
        ctx.font=`${Fs(18)}px Arial`; ctx.fillStyle=totalScore>=241?'#4ade80':'#f472b6';
        ctx.fillText(getStellaRank(totalScore), canvas.width/2, canvas.height/2+S(50));
        ctx.font=`${Fs(14)}px Arial`; ctx.fillStyle='rgba(255,255,255,0.6)';
        ctx.fillText('Tap New Game to play again', canvas.width/2, canvas.height/2+S(85));
    }

    function drawFallbackScene() {
        const w = canvas.width, h = canvas.height;
        const bg = ctx.createLinearGradient(0, 0, 0, h);
        bg.addColorStop(0, '#1e1b4b');
        bg.addColorStop(0.5, '#312e81');
        bg.addColorStop(1, '#0f172a');
        ctx.fillStyle = bg;
        ctx.fillRect(0, 0, w, h);

        const topY = h * 0.2, botY = h * 0.88;
        const topW = w * 0.11, botW = w * 0.4;
        const cx = w / 2;
        ctx.fillStyle = '#0f172a';
        ctx.fillRect(cx - botW - 12, topY, (botW + 12) * 2, botY - topY + 8);
        ctx.fillStyle = '#c9a66b';
        ctx.beginPath();
        ctx.moveTo(cx - topW, topY);
        ctx.lineTo(cx + topW, topY);
        ctx.lineTo(cx + botW, botY);
        ctx.lineTo(cx - botW, botY);
        ctx.closePath();
        ctx.fill();

        if (ball) {
            const bx = cx, by = botY - h * 0.08;
            ctx.fillStyle = '#4ade80';
            ctx.beginPath();
            ctx.arc(bx, by, Math.max(10, w * 0.035), 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function drawScene() {
        ctx.save();
        if (screenShake > 0.5) {
            const shake = screenShake;
            ctx.translate((Math.random() - 0.5) * shake, (Math.random() - 0.5) * shake);
        }
        if (cinematicTimer > 0) {
            const zoom = 1.08 + (1 - cinematicTimer / 80) * 0.14;
            const cx = canvas.width / 2;
            const cy = canvas.height * 0.52;
            ctx.translate(cx, cy);
            ctx.scale(zoom, zoom);
            ctx.translate(-cx, -cy);
        }
        ctx.fillStyle = '#0a0a14';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const floorG=ctx.createLinearGradient(0,canvas.height*0.5,0,canvas.height);
        floorG.addColorStop(0,'#1a1035'); floorG.addColorStop(1,'#0a0a14');
        ctx.fillStyle=floorG; ctx.fillRect(0,0,canvas.width,canvas.height);

        drawCosmicWindow();
        drawPillarsAndCeiling();
        drawLaneSurface(-LANE_W*2.2, false);
        drawLaneSurface(LANE_W*2.2, false);
        drawLaneSurface(0, true);
        drawPinsetter();

        const pinList = pinsetter.phase === 'place' ? pinsetter.placing : pins;
        const objs = [
            ...pinList.map(p => ({ type: 'pin', z: p.z, obj: p })),
            ...(ball ? [{ type: 'ball', z: ball.z }] : [])
        ].sort((a, b) => b.z - a.z);
        objs.forEach(o => { if (o.type === 'pin') drawPin(o.obj); else drawBall(); });

        drawSkinTrailParticles();
        drawSpareHelper();
        drawSparks();
        drawParticles3D();
        drawImpacts();
        drawConfetti();
        drawAimLine();
        drawGameOver();
        ctx.restore();
    }

    function draw() {
        if (!ctx || canvas.width < 2 || canvas.height < 2) return;
        try {
            drawScene();
        } catch (err) {
            console.error('Bowling draw failed, using fallback:', err);
            try {
                drawFallbackScene();
            } catch (fallbackErr) {
                console.error('Bowling fallback draw failed:', fallbackErr);
            }
        }
    }

    function drawSkinPreviews() {
        document.querySelectorAll('.skin-preview').forEach(c => {
            const s=c.getContext('2d'), skin=c.dataset.skin;
            s.clearRect(0,0,56,56);
            s.fillStyle='#1e293b'; s.fillRect(0,0,56,56);
            drawBallSkinOn(s, 28, 28, 22, skin);
        });
    }

    function drawBallSkinOn(s, cx, cy, r, skin) {
        s.save(); s.translate(cx,cy);
        if (skin==='energy') {
            const g=s.createRadialGradient(-5,-5,0,0,0,r);
            g.addColorStop(0,'#bbf7d0'); g.addColorStop(0.5,'#4ade80'); g.addColorStop(1,'#14532d');
            s.beginPath(); s.arc(0,0,r,0,Math.PI*2); s.fillStyle=g; s.fill();
            s.strokeStyle='#86efac'; s.lineWidth=1.5;
            for(let i=0;i<3;i++){s.beginPath();s.moveTo(0,0);s.lineTo((i-1)*8,(i-1)*6);s.stroke();}
        } else if (skin==='stella') {
            const g=s.createRadialGradient(-5,-5,0,0,0,r);
            g.addColorStop(0,'#fce7f3'); g.addColorStop(1,'#9d174d');
            s.beginPath(); s.arc(0,0,r,0,Math.PI*2); s.fillStyle=g; s.fill();
            s.fillStyle='#fbbf24'; s.font='bold 16px serif'; s.textAlign='center'; s.textBaseline='middle';
            s.fillText('✦',0,0);
        } else if (skin==='flag') {
            s.save();
            s.beginPath(); s.arc(0, 0, r, 0, Math.PI * 2); s.clip();
            s.fillStyle = '#f8fafc'; s.fillRect(-r, -r, r * 2, r * 2);
            s.fillStyle = '#dc2626';
            const sh = (r * 2) / 7;
            for (let i = 0; i < 7; i += 2) s.fillRect(-r, -r + i * sh, r * 2, sh);
            s.fillStyle = '#1e3a8a'; s.fillRect(-r, -r, r * 1.05, r * 0.78);
            s.fillStyle = '#f8fafc';
            [[-0.55, -0.58], [-0.2, -0.58], [0.15, -0.58], [-0.38, -0.43]].forEach(([sx, sy]) => {
                s.beginPath(); s.arc(sx * r, sy * r, r * 0.05, 0, Math.PI * 2); s.fill();
            });
            s.restore();
        } else {
            s.beginPath(); s.arc(0,0,r,0,Math.PI*2); s.fillStyle='#38bdf8'; s.fill();
            s.fillStyle='#fff'; s.beginPath(); s.arc(-5,-3,4,0,Math.PI*2); s.fill();
            s.beginPath(); s.arc(6,4,3,0,Math.PI*2); s.fill();
        }
        s.restore();
    }

    function gameLoop() {
        try {
            update();
            draw();
        } catch (err) {
            console.error('Bowling render error:', err);
            try { drawFallbackScene(); } catch (_) {}
        }
        requestAnimationFrame(gameLoop);
    }

    function tickBowling() {
        try {
            update();
            draw();
        } catch (err) {
            console.error('Bowling tick error:', err);
            try { drawFallbackScene(); } catch (_) {}
        }
    }

    // Events
    canvas.addEventListener('mousedown', e => {
        initAudio();
        if (gameOver || paused || !canBowl || !ball || ball.rolling || pinsetter.phase !== 'idle' || standingPinCount() === 0) return;
        const bp = project(ball.x, ball.y, ball.z);
        dragStart.x = bp.x;
        dragStart.y = bp.y;
        ballXAtDragStart = ball.x;
        aimLocked = false;
        dragEnd = getCanvasPos(e.clientX, e.clientY);
        isDragging = true;
    });
    canvas.addEventListener('mousemove', e => {
        if (!isDragging) return;
        dragEnd = getCanvasPos(e.clientX, e.clientY);
        updateBallPositionFromDrag();
    });
    canvas.addEventListener('mouseup', () => {
        if (isDragging && ball && !ball.rolling && !gameOver && !paused && canBowl) rollBall();
        isDragging = false;
        aimLocked = false;
    });
    canvas.addEventListener('mouseleave', () => { isDragging = false; aimLocked = false; });

    canvas.addEventListener('touchstart', e => {
        e.preventDefault();
        initAudio();
        if (gameOver || paused || !canBowl || !ball || ball.rolling || pinsetter.phase !== 'idle' || standingPinCount() === 0) return;
        const bp = project(ball.x, ball.y, ball.z);
        dragStart.x = bp.x;
        dragStart.y = bp.y;
        ballXAtDragStart = ball.x;
        aimLocked = false;
        dragEnd = getCanvasPos(e.touches[0].clientX, e.touches[0].clientY);
        isDragging = true;
    }, {passive:false});
    canvas.addEventListener('touchmove', e => {
        e.preventDefault();
        if (!isDragging) return;
        dragEnd = getCanvasPos(e.touches[0].clientX, e.touches[0].clientY);
        updateBallPositionFromDrag();
    }, {passive:false});
    canvas.addEventListener('touchend', () => {
        if (isDragging && ball && !ball.rolling && !gameOver && !paused && canBowl) rollBall();
        isDragging = false;
        aimLocked = false;
    });

    document.querySelectorAll('.ball-skin-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            initAudio();
            playSound('click');
            selectedSkin = btn.dataset.skin;
            localStorage.setItem(SKIN_KEY, selectedSkin);
            document.querySelectorAll('.ball-skin-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    pauseBtn.addEventListener('click', () => { initAudio(); playSound('click'); paused=true; pauseOverlay.classList.remove('game-overlay-hidden'); });
    resumeBtn.addEventListener('click', () => { initAudio(); playSound('click'); paused=false; pauseOverlay.classList.add('game-overlay-hidden'); });
    soundBtn.addEventListener('click', () => {
        initAudio();
        soundOn=!soundOn;
        soundBtn.textContent = soundOn ? '🔊' : '🔇';
        if (soundOn) playSound('click');
    });
    menuBtn.addEventListener('click', () => { initAudio(); playSound('click'); leaderboardPanel.classList.remove('game-overlay-hidden'); });
    closeLeaderboard.addEventListener('click', () => { playSound('click'); leaderboardPanel.classList.add('game-overlay-hidden'); });
    resetBtn.addEventListener('click', () => { initAudio(); playSound('click'); resetGame(); });
    themeBtn?.addEventListener('click', () => { initAudio(); cycleLaneTheme(); });

    const savedSkin = localStorage.getItem(SKIN_KEY);
    if (savedSkin) {
        selectedSkin = savedSkin;
        document.querySelectorAll('.ball-skin-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.skin === savedSkin);
        });
    }
    const savedTheme = localStorage.getItem(THEME_KEY);
    if (savedTheme && LANE_THEMES.includes(savedTheme)) {
        laneTheme = savedTheme;
        if (themeBtn) themeBtn.textContent = laneTheme === 'nebula' ? '🌠 Nebula' : '🌌 Cosmic';
    }

    loadPlayerName();
    loadCoins();
    renderProLeaderboard();
    loadYourScores();
    if (typeof GameLeaderboard !== 'undefined') {
        GameLeaderboard.mount('#bowling-leaderboard', 'galaxy-bowling');
    }

    function startBowling() {
        if (window._bowlingBooted) {
            resizeCanvas();
            initStars();
            draw();
            return;
        }
        if (stage.clientWidth < 8 || stage.clientHeight < 8) return;

        window._bowlingBooted = true;
        resizeCanvas();
        initStars();
        drawSkinPreviews();
        resetGame();
        draw();

        if (!window._bowlingLoopStarted) {
            window._bowlingLoopStarted = true;
            const isTouchDevice = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
            if (isTouchDevice) {
                setInterval(tickBowling, 33);
            } else {
                requestAnimationFrame(gameLoop);
            }
        }
    }

    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(() => startBowling()).observe(stage);
    }
    window.addEventListener('load', startBowling);
    window.addEventListener('orientationchange', () => {
        setTimeout(() => {
            resizeCanvas();
            initStars();
            draw();
        }, 200);
    });
    startBowling();
});
</script>
@endsection
