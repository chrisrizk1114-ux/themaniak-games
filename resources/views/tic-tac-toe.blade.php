@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800&family=Rajdhani:wght@500;600;700&display=swap');

    body:has(.ttt-page) {
        overflow-y: auto;
    }

    .main-content:has(.ttt-page) {
        max-width: none;
        padding: 0;
        width: 100%;
    }

    .ttt-page {
        --neon-pink: #ff2d6a;
        --neon-cyan: #00f0ff;
        --neon-gold: #ffd54a;
        --neon-purple: #a855f7;
        --panel-bg: rgba(8, 12, 28, 0.88);
        --nav-h: 76px;
        --cell: min(calc((100svh - 260px) / 3), calc((100vw - 200px) / 3), 150px);
        font-family: 'Rajdhani', sans-serif;
        min-height: calc(100svh - var(--nav-h));
        width: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background:
            radial-gradient(ellipse at 15% 20%, rgba(255,45,106,0.14) 0%, transparent 45%),
            radial-gradient(ellipse at 85% 80%, rgba(0,240,255,0.12) 0%, transparent 45%),
            radial-gradient(ellipse at 50% 50%, rgba(168,85,247,0.08) 0%, transparent 60%),
            linear-gradient(160deg, #050510 0%, #0a0a1e 45%, #080818 100%);
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .ttt-page::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(1px 1px at 10% 20%, rgba(255,255,255,0.5) 0%, transparent 100%),
            radial-gradient(1px 1px at 30% 70%, rgba(255,255,255,0.4) 0%, transparent 100%),
            radial-gradient(1.5px 1.5px at 70% 30%, rgba(255,255,255,0.45) 0%, transparent 100%),
            radial-gradient(1px 1px at 90% 60%, rgba(255,255,255,0.35) 0%, transparent 100%),
            radial-gradient(1px 1px at 50% 90%, rgba(255,255,255,0.4) 0%, transparent 100%);
        pointer-events: none;
        animation: stars-twinkle 4s ease-in-out infinite alternate;
    }

    @keyframes stars-twinkle {
        from { opacity: 0.5; }
        to { opacity: 1; }
    }

    .ttt-shell {
        position: relative;
        z-index: 1;
        min-height: calc(100svh - var(--nav-h) - 120px);
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: clamp(0.75rem, 2vh, 1.5rem) clamp(0.75rem, 3vw, 2rem) 0;
        gap: clamp(0.75rem, 2vh, 1.25rem);
    }

    .ttt-header {
        text-align: center;
        width: 100%;
    }

    .ttt-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.4rem, 3.5vw, 2.4rem);
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        margin: 0;
        background: linear-gradient(90deg, var(--neon-cyan), #fff, var(--neon-pink));
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: title-shimmer 4s linear infinite;
        filter: drop-shadow(0 0 12px rgba(0,240,255,0.35));
    }

    @keyframes title-shimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .ttt-subtitle {
        font-size: clamp(0.75rem, 1.5vw, 0.95rem);
        color: rgba(255,255,255,0.45);
        letter-spacing: 0.2em;
        text-transform: uppercase;
        margin-top: 0.25rem;
    }

    .ttt-main {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: clamp(1rem, 4vw, 3rem);
        width: 100%;
    }

    .player-panel {
        width: clamp(120px, 16vw, 200px);
        padding: clamp(0.75rem, 2vh, 1.25rem);
        border-radius: 16px;
        background: var(--panel-bg);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.08);
        text-align: center;
        transition: all 0.35s ease;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .player-panel .panel-left {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .player-panel.active-x {
        border-color: rgba(255,45,106,0.7);
        box-shadow: 0 0 30px rgba(255,45,106,0.35), inset 0 0 20px rgba(255,45,106,0.08);
    }

    .player-panel.active-o {
        border-color: rgba(0,240,255,0.7);
        box-shadow: 0 0 30px rgba(0,240,255,0.35), inset 0 0 20px rgba(0,240,255,0.08);
    }

    .player-mark {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(2rem, 5vw, 3rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.35rem;
    }

    .player-panel.x-panel .player-mark { color: var(--neon-pink); text-shadow: 0 0 20px rgba(255,45,106,0.6); }
    .player-panel.o-panel .player-mark { color: var(--neon-cyan); text-shadow: 0 0 20px rgba(0,240,255,0.6); }

    .player-label {
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.5);
        margin-bottom: 0.5rem;
    }

    .player-score {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 700;
        color: var(--neon-gold);
        text-shadow: 0 0 12px rgba(255,213,74,0.4);
    }

    .player-wins-label {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.35);
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .board-wrap {
        position: relative;
        padding: clamp(0.5rem, 1.5vw, 1rem);
        border-radius: 24px;
        background: var(--panel-bg);
        border: 2px solid rgba(168,85,247,0.25);
        box-shadow:
            0 0 0 1px rgba(255,255,255,0.04),
            0 20px 60px rgba(0,0,0,0.5),
            0 0 40px rgba(168,85,247,0.12);
    }

    .game-board {
        display: grid;
        grid-template-columns: repeat(3, var(--cell));
        grid-template-rows: repeat(3, var(--cell));
        gap: clamp(6px, 1.2vw, 12px);
        position: relative;
        isolation: isolate;
    }

    .cell {
        width: var(--cell);
        height: var(--cell);
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        background: linear-gradient(145deg, rgba(20,24,48,0.9), rgba(12,14,32,0.95));
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: clamp(10px, 2vw, 18px);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s, transform 0.15s;
        position: relative;
        overflow: hidden;
    }

    .cell::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.06) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .cell:hover:not(.taken) {
        border-color: rgba(168,85,247,0.5);
        transform: scale(1.03);
    }

    .cell:hover:not(.taken)::before { opacity: 1; }

    .cell.taken { cursor: default; }

    .cell .mark {
        font-family: 'Orbitron', sans-serif;
        font-size: calc(var(--cell) * 0.55);
        font-weight: 800;
        line-height: 1;
        animation: mark-pop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        pointer-events: none;
    }

    .cell.x .mark {
        color: var(--neon-pink);
        text-shadow: 0 0 24px rgba(255,45,106,0.7), 0 0 48px rgba(255,45,106,0.3);
    }

    .cell.o .mark {
        color: var(--neon-cyan);
        text-shadow: 0 0 24px rgba(0,240,255,0.7), 0 0 48px rgba(0,240,255,0.3);
    }

    @keyframes mark-pop {
        0% { transform: scale(0) rotate(-20deg); opacity: 0; }
        70% { transform: scale(1.15) rotate(5deg); }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    .cell.winner {
        background: linear-gradient(145deg, rgba(255,213,74,0.2), rgba(255,213,74,0.08));
        border-color: var(--neon-gold);
        animation: win-pulse 0.7s ease-in-out infinite alternate;
    }

    @keyframes win-pulse {
        from { box-shadow: 0 0 0 rgba(255,213,74,0); transform: scale(1); }
        to { box-shadow: 0 0 28px rgba(255,213,74,0.55); transform: scale(1.04); }
    }

    .win-line-svg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: visible;
    }

    .win-line-svg line {
        stroke: var(--neon-gold);
        stroke-width: 5;
        stroke-linecap: round;
        filter: drop-shadow(0 0 8px rgba(255,213,74,0.8));
        stroke-dasharray: 400;
        stroke-dashoffset: 400;
        animation: draw-line 0.5s ease forwards 0.1s;
    }

    @keyframes draw-line {
        to { stroke-dashoffset: 0; }
    }

    .turn-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: clamp(0.5rem, 1.5vh, 0.85rem);
        padding: 0.45rem 1.25rem;
        border-radius: 999px;
        background: var(--panel-bg);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: clamp(0.95rem, 2vw, 1.15rem);
        font-weight: 600;
        letter-spacing: 0.06em;
        transition: all 0.3s;
    }

    .turn-badge.turn-x { color: var(--neon-pink); border-color: rgba(255,45,106,0.4); box-shadow: 0 0 20px rgba(255,45,106,0.15); }
    .turn-badge.turn-o { color: var(--neon-cyan); border-color: rgba(0,240,255,0.4); box-shadow: 0 0 20px rgba(0,240,255,0.15); }
    .turn-badge.game-end { color: var(--neon-gold); border-color: rgba(255,213,74,0.4); }

    .ttt-footer {
        position: sticky;
        bottom: 0;
        z-index: 20;
        flex-shrink: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        padding: clamp(0.85rem, 2vh, 1.1rem) clamp(0.75rem, 3vw, 2rem);
        margin-top: auto;
        background: rgba(5, 8, 20, 0.96);
        backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.45);
    }

    .ttt-footer-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: clamp(0.5rem, 2vw, 1rem);
        flex-wrap: wrap;
        width: 100%;
        max-width: 640px;
    }

    .ttt-lb-wrap {
        width: min(360px, 100%);
    }

    .ttt-btn {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: clamp(0.9rem, 1.8vw, 1.05rem);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.65rem 1.75rem;
        border-radius: 999px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.25s;
    }

    .ttt-btn-primary {
        background: linear-gradient(135deg, var(--neon-pink), #c026d3);
        color: #fff;
        border-color: rgba(255,45,106,0.5);
        box-shadow: 0 4px 20px rgba(255,45,106,0.3);
    }

    .ttt-btn-primary:hover {
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 8px 30px rgba(255,45,106,0.45);
    }

    .ttt-btn-ghost {
        background: var(--panel-bg);
        color: rgba(255,255,255,0.85);
        border-color: rgba(255,255,255,0.15);
    }

    .ttt-btn-ghost:hover {
        border-color: var(--neon-cyan);
        color: var(--neon-cyan);
        box-shadow: 0 0 16px rgba(0,240,255,0.2);
    }

    .draws-pill {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.4);
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .draws-pill strong { color: rgba(255,255,255,0.7); }

    .result-overlay {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(5,5,16,0.75);
        backdrop-filter: blur(6px);
        animation: fade-in 0.3s ease;
    }

    .result-overlay.hidden { display: none; }

    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .result-card {
        text-align: center;
        padding: clamp(1.5rem, 4vw, 2.5rem) clamp(2rem, 6vw, 3.5rem);
        border-radius: 24px;
        background: linear-gradient(145deg, rgba(20,24,48,0.95), rgba(8,10,24,0.98));
        border: 2px solid rgba(255,213,74,0.4);
        box-shadow: 0 0 60px rgba(255,213,74,0.2), 0 30px 80px rgba(0,0,0,0.6);
        animation: card-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes card-pop {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .result-card h2 {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.5rem, 4vw, 2.2rem);
        margin: 0 0 0.5rem;
        color: var(--neon-gold);
        text-shadow: 0 0 20px rgba(255,213,74,0.5);
    }

    .result-card p {
        color: rgba(255,255,255,0.6);
        font-size: 1rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 700px) {
        .cell {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        .ttt-page {
            --cell: min(calc((100vw - 60px) / 3), 120px);
        }
        .ttt-shell {
            min-height: auto;
            padding-bottom: 0;
        }
        .ttt-footer {
            position: sticky;
            bottom: 0;
            padding-bottom: max(0.85rem, env(safe-area-inset-bottom));
        }
        .ttt-main {
            flex-direction: column;
            gap: 0.75rem;
        }
        .player-panel {
            width: 100%;
            max-width: 320px;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1rem;
        }
        .player-panel .panel-left {
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
        }
        .player-mark { font-size: 1.6rem; margin: 0; }
        .player-label, .player-wins-label { margin: 0; }
        .player-score { font-size: 1.6rem; }
    }
</style>

<div class="ttt-page">
    <div class="ttt-shell">
        <header class="ttt-header">
            <h1 class="ttt-title">Neon Grid</h1>
            <p class="ttt-subtitle">Tic Tac Toe Arena</p>
            <div id="turnBadge" class="turn-badge turn-x">
                <span id="turnMark">✕</span>
                <span id="status">Player X's Turn</span>
            </div>
        </header>

        <div class="ttt-main">
            <aside id="panelX" class="player-panel x-panel active-x">
                <div class="panel-left">
                    <div class="player-mark">✕</div>
                    <div>
                        <div class="player-label">Player X</div>
                        <div class="player-wins-label">Wins</div>
                    </div>
                </div>
                <div class="player-score" id="scoreX">0</div>
            </aside>

            <div class="board-wrap">
                <div class="game-board" id="gameBoard">
                    <div class="cell" data-index="0"></div>
                    <div class="cell" data-index="1"></div>
                    <div class="cell" data-index="2"></div>
                    <div class="cell" data-index="3"></div>
                    <div class="cell" data-index="4"></div>
                    <div class="cell" data-index="5"></div>
                    <div class="cell" data-index="6"></div>
                    <div class="cell" data-index="7"></div>
                    <div class="cell" data-index="8"></div>
                    <svg id="winLine" class="win-line-svg" preserveAspectRatio="none"></svg>
                </div>
            </div>

            <aside id="panelO" class="player-panel o-panel">
                <div class="panel-left">
                    <div class="player-mark">○</div>
                    <div>
                        <div class="player-label">Player O</div>
                        <div class="player-wins-label">Wins</div>
                    </div>
                </div>
                <div class="player-score" id="scoreO">0</div>
            </aside>
        </div>
    </div>

    <footer class="ttt-footer">
        <div class="ttt-footer-actions">
            <span class="draws-pill">Draws: <strong id="scoreDraw">0</strong></span>
            <button class="ttt-btn ttt-btn-primary" id="restartBtn" type="button">↻ New Round</button>
            <button class="ttt-btn ttt-btn-ghost" id="resetScoresBtn" type="button">Reset Scores</button>
            <button class="ttt-btn ttt-btn-ghost" id="soundBtn" type="button" title="Toggle sound">🔊</button>
        </div>
        <div class="ttt-lb-wrap">
            @include('partials.game-leaderboard', ['game' => 'tic-tac-toe', 'id' => 'tttLeaderboard'])
        </div>
    </footer>

    <div id="resultOverlay" class="result-overlay hidden">
        <div class="result-card">
            <h2 id="resultTitle">X Wins!</h2>
            <p id="resultSub">Three in a row — neon victory!</p>
            <button class="ttt-btn ttt-btn-primary" id="resultBtn" type="button">Play Again</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cells = document.querySelectorAll('.cell');
    const statusEl = document.getElementById('status');
    const turnBadge = document.getElementById('turnBadge');
    const turnMark = document.getElementById('turnMark');
    const panelX = document.getElementById('panelX');
    const panelO = document.getElementById('panelO');
    const scoreXEl = document.getElementById('scoreX');
    const scoreOEl = document.getElementById('scoreO');
    const scoreDrawEl = document.getElementById('scoreDraw');
    const winLineSvg = document.getElementById('winLine');
    const resultOverlay = document.getElementById('resultOverlay');
    const resultTitle = document.getElementById('resultTitle');
    const resultSub = document.getElementById('resultSub');
    const restartBtn = document.getElementById('restartBtn');
    const resetScoresBtn = document.getElementById('resetScoresBtn');
    const resultBtn = document.getElementById('resultBtn');
    const soundBtn = document.getElementById('soundBtn');

    const winningConditions = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6]
    ];

    let gameActive = true;
    let currentPlayer = 'X';
    let gameState = Array(9).fill('');
    let scores = { x: 0, o: 0, draw: 0 };

    function initScores() {
        scores = { x: 0, o: 0, draw: 0 };
        updateScoreDisplay();
    }

    function updateScoreDisplay() {
        scoreXEl.textContent = String(scores.x);
        scoreOEl.textContent = String(scores.o);
        scoreDrawEl.textContent = String(scores.draw);
    }

    function setTurnUI(player, message) {
        const isX = player === 'X';
        turnMark.textContent = isX ? '✕' : '○';
        statusEl.textContent = message || (isX ? "Player X's Turn" : "Player O's Turn");
        turnBadge.className = 'turn-badge ' + (isX ? 'turn-x' : 'turn-o');
        panelX.classList.toggle('active-x', isX && gameActive);
        panelO.classList.toggle('active-o', !isX && gameActive);
    }

    function drawWinLine(line) {
        const board = document.getElementById('gameBoard');
        const boardRect = board.getBoundingClientRect();
        const startCell = cells[line[0]];
        const endCell = cells[line[2]];
        const sRect = startCell.getBoundingClientRect();
        const eRect = endCell.getBoundingClientRect();
        const x1 = sRect.left + sRect.width / 2 - boardRect.left;
        const y1 = sRect.top + sRect.height / 2 - boardRect.top;
        const x2 = eRect.left + eRect.width / 2 - boardRect.left;
        const y2 = eRect.top + eRect.height / 2 - boardRect.top;
        winLineSvg.setAttribute('viewBox', `0 0 ${boardRect.width} ${boardRect.height}`);
        winLineSvg.innerHTML = `<line x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}" />`;
    }

    function clearWinLine() {
        winLineSvg.innerHTML = '';
    }

    function showResult(title, sub) {
        resultTitle.textContent = title;
        resultSub.textContent = sub;
        resultOverlay.classList.remove('hidden');
        turnBadge.className = 'turn-badge game-end';
    }

    function hideResult() {
        resultOverlay.classList.add('hidden');
    }

    function handleCellClick(e) {
        const cell = e.currentTarget;
        const index = parseInt(cell.dataset.index, 10);

        if (gameState[index] !== '' || !gameActive) return;

        GameSounds.init();
        gameState[index] = currentPlayer;
        cell.classList.add(currentPlayer.toLowerCase(), 'taken');
        const mark = document.createElement('span');
        mark.className = 'mark';
        mark.textContent = currentPlayer === 'X' ? '✕' : '○';
        cell.appendChild(mark);
        GameSounds.play(currentPlayer === 'X' ? 'placeX' : 'placeO');

        checkResult();
    }

    function highlightWin(line) {
        line.forEach(i => cells[i].classList.add('winner'));
        drawWinLine(line);
    }

    function checkResult() {
        let roundWon = false;
        let winLine = null;

        for (const [a, b, c] of winningConditions) {
            if (!gameState[a] || gameState[a] !== gameState[b] || gameState[b] !== gameState[c]) continue;
            roundWon = true;
            winLine = [a, b, c];
            break;
        }

        if (roundWon) {
            highlightWin(winLine);
            panelX.classList.remove('active-x');
            panelO.classList.remove('active-o');
            statusEl.textContent = `${currentPlayer} Wins!`;
            gameActive = false;
            GameSounds.play('win');
            if (currentPlayer === 'X') scores.x++;
            else scores.o++;
            updateScoreDisplay();
            @auth
            if (typeof GameLeaderboard !== 'undefined') {
                GameLeaderboard.recordWin('tic-tac-toe').catch(() => {});
            }
            @endauth
            setTimeout(() => {
                showResult(
                    `${currentPlayer} Wins! 🎉`,
                    'Three in a row — neon victory!'
                );
            }, 450);
            return;
        }

        if (!gameState.includes('')) {
            panelX.classList.remove('active-x');
            panelO.classList.remove('active-o');
            statusEl.textContent = "It's a Draw!";
            gameActive = false;
            GameSounds.play('draw');
            scores.draw++;
            updateScoreDisplay();
            setTimeout(() => {
                showResult('Draw! 🤝', 'The grid is full — nobody wins this round.');
            }, 300);
            return;
        }

        currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
        setTurnUI(currentPlayer);
    }

    function restartGame(playSound) {
        if (playSound) {
            GameSounds.init();
            GameSounds.play('click');
        }
        hideResult();
        gameActive = true;
        currentPlayer = 'X';
        gameState = Array(9).fill('');
        clearWinLine();
        setTurnUI('X');
        cells.forEach(cell => {
            cell.textContent = '';
            cell.classList.remove('x', 'o', 'winner', 'taken');
        });
    }

    function resetScores() {
        GameSounds.init();
        GameSounds.play('click');
        scores = { x: 0, o: 0, draw: 0 };
        updateScoreDisplay();
        restartGame(false);
    }

    cells.forEach(cell => cell.addEventListener('click', handleCellClick));
    restartBtn.addEventListener('click', () => restartGame(true));
    resultBtn.addEventListener('click', () => restartGame(true));
    resetScoresBtn.addEventListener('click', resetScores);
    soundBtn.addEventListener('click', () => {
        GameSounds.init();
        soundBtn.textContent = GameSounds.toggle() ? '🔊' : '🔇';
    });

    initScores();
    setTurnUI('X');
});
</script>
@endsection
