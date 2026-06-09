@extends('layouts.app')

@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
@endpush

@section('content')
<style>
    html:has(.chess-page),
    body:has(.chess-page) {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        height: auto !important;
        overscroll-behavior: auto !important;
    }

    .main-content:has(.chess-page) {
        max-width: none;
        padding: 0;
        width: 100%;
        overflow: visible;
        min-height: auto;
    }

    .chess-page {
        --royal-gold: #d4a853;
        --royal-gold-light: #f0d78c;
        --royal-burgundy: #4a1528;
        --royal-navy: #0c1222;
        --royal-ivory: #f5f0e6;
        --board-light: #f3ead6;
        --board-dark: #3d2c1e;
        --board-gold: #c9a227;
        --board-gold-bright: #f0d78c;
        --leather: #2a1810;
        --nav-h: 76px;
        font-family: 'Source Sans 3', sans-serif;
        width: 100%;
        background:
            radial-gradient(ellipse at 20% 0%, rgba(212,168,83,0.12) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 100%, rgba(74,21,40,0.35) 0%, transparent 55%),
            linear-gradient(165deg, #0a0f1a 0%, #14101c 40%, #0c1222 100%);
        position: relative;
        overflow: visible;
        padding-bottom: 2rem;
    }
    .chess-page::before {
        content: '♔ ♕ ♖ ♗ ♘ ♙';
        position: fixed;
        inset: 0;
        font-size: 4rem;
        color: rgba(212,168,83,0.03);
        letter-spacing: 2rem;
        line-height: 6rem;
        word-break: break-all;
        pointer-events: none;
        z-index: 0;
    }

    .game-container {
        padding: 1rem 0.75rem 2rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        min-height: auto;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    .game-container:has(#gameArea.active) {
        max-width: none;
        padding: 0.4rem clamp(0.5rem, 2vw, 2rem) 0;
        justify-content: flex-start;
        min-height: auto;
        /* room for fixed footer bar */
        padding-bottom: calc(5.75rem + env(safe-area-inset-bottom, 0px));
    }
    .game-container:has(#gameArea.active) .chess-title {
        margin: 0 0 0.25rem;
        font-size: clamp(1.1rem, 2.2vh, 1.7rem);
    }

    .chess-title {
        font-family: 'Cinzel', serif;
        font-size: clamp(1.6rem, 4vw, 2.2rem);
        margin: 0.2rem 0 0.8rem;
        background: linear-gradient(180deg, var(--royal-gold-light), var(--royal-gold));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .chess-title span {
        -webkit-text-fill-color: var(--royal-gold);
        font-size: 0.55em;
        vertical-align: middle;
        margin-left: 0.3rem;
    }

    .player-select {
        background: linear-gradient(145deg, rgba(42,24,16,0.95), rgba(20,16,28,0.98));
        padding: 2rem 1.8rem;
        border-radius: 4px;
        border: 2px solid rgba(212,168,83,0.35);
        box-shadow: 0 0 0 1px rgba(0,0,0,0.5), 0 20px 50px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);
        margin: 0.5rem auto;
        max-width: 960px;
        width: 100%;
        position: relative;
    }
    .player-select::before,
    .player-select::after {
        content: '♛';
        position: absolute;
        font-size: 1.4rem;
        color: rgba(212,168,83,0.25);
        top: 0.6rem;
    }
    .player-select::before { left: 0.8rem; }
    .player-select::after { right: 0.8rem; transform: scaleX(-1); }
    .player-select h2 {
        margin-bottom: 1.2rem;
        color: var(--royal-gold);
        font-family: 'Cinzel', serif;
        font-size: 1.35rem;
        letter-spacing: 0.08em;
    }
    .match-modes {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        text-align: left;
    }
    @media (max-width: 820px) {
        .match-modes { grid-template-columns: 1fr; }
    }
    .mode-card {
        background: rgba(0,0,0,0.22);
        border: 1px solid rgba(212,168,83,0.28);
        border-radius: 3px;
        padding: 1.15rem 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        min-height: 220px;
    }
    .mode-card h3 {
        margin: 0;
        color: var(--royal-gold);
        font-family: 'Cinzel', serif;
        font-size: 1.05rem;
        letter-spacing: 0.06em;
        text-align: center;
    }
    .mode-desc {
        margin: 0;
        color: rgba(245,240,230,0.55);
        font-size: 0.88rem;
        text-align: center;
        line-height: 1.35;
    }
    .mode-btn {
        background: linear-gradient(180deg, #6b2038, var(--royal-burgundy));
        color: var(--royal-ivory);
        border: 1px solid rgba(212,168,83,0.4);
        padding: 0.75rem 1.2rem;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.25s;
        font-family: 'Cinzel', serif;
        letter-spacing: 0.04em;
        width: 100%;
        margin-top: auto;
    }
    .mode-btn:hover {
        border-color: var(--royal-gold);
        box-shadow: 0 0 20px rgba(212,168,83,0.25);
        transform: translateY(-2px);
    }
    .difficulty-buttons {
        display: flex;
        gap: 0.45rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: auto;
    }
    .difficulty-buttons h4 {
        width: 100%;
        margin: 0 0 0.25rem;
        color: rgba(212,168,83,0.75);
        font-size: 0.8rem;
        font-family: Cinzel, serif;
        letter-spacing: 0.06em;
        text-align: center;
    }
    .diff-btn {
        background: linear-gradient(180deg, #3d5a3a, #2a3d28);
        color: var(--royal-ivory);
        border: 1px solid rgba(212,168,83,0.3);
        padding: 0.55rem 1.1rem;
        font-size: 0.95rem;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.25s;
    }
    .diff-btn:hover {
        border-color: var(--royal-gold);
        box-shadow: 0 0 12px rgba(212,168,83,0.2);
    }

    .friends-list {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        max-height: 180px;
        overflow-y: auto;
        flex: 1;
    }
    .friend-pick-btn {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        width: 100%;
        padding: 0.65rem 0.85rem;
        border-radius: 2px;
        border: 1px solid rgba(212,168,83,0.25);
        background: rgba(0,0,0,0.25);
        color: var(--royal-ivory);
        cursor: pointer;
        text-align: left;
        transition: all 0.2s;
    }
    .friend-pick-btn:hover {
        border-color: var(--royal-gold);
        background: rgba(212,168,83,0.1);
    }
    .friend-pick-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        background: linear-gradient(135deg, #00f0ff, #a855f7);
        color: #fff;
        flex-shrink: 0;
    }
    .friends-empty {
        color: rgba(255,255,255,0.45);
        font-size: 0.95rem;
        padding: 0.5rem 0;
    }
    .friends-login-hint {
        color: rgba(212,168,83,0.75);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    .friends-login-hint a {
        color: var(--royal-gold-light);
    }

    .friend-pick-meta {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        flex: 1;
        min-width: 0;
    }
    .friend-pick-meta small {
        color: rgba(212,168,83,0.65);
        font-size: 0.75rem;
    }
    .friend-online-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 8px rgba(74,222,128,0.6);
        flex-shrink: 0;
    }
    .friend-offline-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        flex-shrink: 0;
    }
    .friend-action-row {
        display: flex;
        gap: 0.35rem;
        margin-top: 0.35rem;
    }
    .friend-action-btn {
        flex: 1;
        padding: 0.4rem 0.5rem;
        font-size: 0.78rem;
        border-radius: 2px;
        border: 1px solid rgba(212,168,83,0.35);
        background: rgba(212,168,83,0.12);
        color: var(--royal-gold-light);
        cursor: pointer;
        font-family: 'Source Sans 3', sans-serif;
    }
    .friend-action-btn:hover {
        background: rgba(212,168,83,0.22);
    }
    .friend-action-btn.primary {
        background: linear-gradient(180deg, #6b2038, var(--royal-burgundy));
        color: var(--royal-ivory);
    }

    .chess-invite-alerts {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-bottom: 1.25rem;
        text-align: left;
    }
    .chess-invite-alert {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
        padding: 0.85rem 1rem;
        border-radius: 3px;
        border: 1px solid rgba(212,168,83,0.45);
        background: rgba(212,168,83,0.12);
        color: var(--royal-ivory);
        font-size: 0.92rem;
    }
    .chess-invite-alert strong {
        color: var(--royal-gold-light);
    }
    .chess-invite-alert .mode-btn {
        width: auto;
        margin: 0;
        padding: 0.55rem 1rem;
        font-size: 0.85rem;
    }
    .friend-card {
        width: 100%;
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(212,168,83,0.12);
    }
    .friend-card:last-child { border-bottom: none; }

    .chess-room-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0,0,0,0.75);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .chess-room-modal.show { display: flex; }
    .chess-room-modal-box {
        width: min(100%, 420px);
        background: linear-gradient(145deg, rgba(42,24,16,0.98), rgba(20,16,28,0.98));
        border: 2px solid rgba(212,168,83,0.4);
        border-radius: 6px;
        padding: 1.5rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    }
    .chess-room-modal-box h3 {
        font-family: 'Cinzel', serif;
        color: var(--royal-gold-light);
        margin-bottom: 0.75rem;
        text-align: center;
    }
    .chess-room-modal-box p {
        color: var(--royal-ivory);
        opacity: 0.85;
        text-align: center;
        margin-bottom: 1rem;
        font-size: 0.92rem;
    }
    .chess-room-actions {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .chess-room-code {
        font-family: 'Orbitron', monospace;
        font-size: 2rem;
        letter-spacing: 0.35em;
        text-align: center;
        color: var(--royal-gold-light);
        padding: 1rem;
        margin: 0.5rem 0 1rem;
        border: 2px dashed rgba(212,168,83,0.45);
        border-radius: 4px;
        background: rgba(0,0,0,0.25);
    }
    .chess-room-input {
        width: 100%;
        padding: 0.85rem 1rem;
        font-size: 1.4rem;
        letter-spacing: 0.2em;
        text-align: center;
        text-transform: uppercase;
        border-radius: 4px;
        border: 1px solid rgba(212,168,83,0.35);
        background: rgba(0,0,0,0.3);
        color: var(--royal-gold-light);
        margin-bottom: 0.75rem;
    }
    .chess-room-close {
        margin-top: 1rem;
        width: 100%;
        background: transparent;
        border: 1px solid rgba(212,168,83,0.25);
        color: rgba(245,240,230,0.7);
        padding: 0.55rem;
        cursor: pointer;
        border-radius: 3px;
    }
    .chess-room-view { display: none; }
    .chess-room-view.active { display: block; }

    .chess-waiting-overlay {
        display: none;
        position: absolute;
        inset: 0;
        z-index: 20;
        background: rgba(8, 6, 12, 0.88);
        border-radius: 4px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        text-align: center;
        gap: 0.75rem;
    }
    .chess-waiting-overlay.show { display: flex; }
    .chess-waiting-overlay h4 {
        font-family: 'Cinzel', serif;
        color: var(--royal-gold-light);
        font-size: 1.05rem;
        margin: 0;
    }
    .chess-waiting-overlay p {
        color: rgba(245,240,230,0.8);
        font-size: 0.9rem;
        margin: 0;
        max-width: 280px;
    }
    .chess-waiting-code {
        font-family: 'Orbitron', monospace;
        font-size: clamp(1.6rem, 6vw, 2.2rem);
        letter-spacing: 0.3em;
        color: var(--royal-gold-light);
        padding: 0.75rem 1rem;
        border: 2px dashed rgba(212,168,83,0.5);
        border-radius: 4px;
        background: rgba(0,0,0,0.35);
    }
    .chess-waiting-overlay .mode-btn {
        margin-top: 0.25rem;
        min-width: 10rem;
    }
    .board-pedestal.waiting-for-opponent .board-frame {
        filter: brightness(0.55);
        pointer-events: none;
    }

    .chess-chat-panel {
        display: none;
        flex-direction: column;
        min-height: 260px;
        max-height: min(420px, 42vh);
    }
    #gameArea.online-friends .chess-chat-panel {
        display: flex;
    }
    #gameArea.online-friends #whitePanelCard h4,
    #gameArea.online-friends #whitePanelCard .captured-row {
        display: none;
    }
    .chess-chat-panel h4 {
        margin-bottom: 0.5rem;
    }
    .chess-chat-list {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        padding: 0.35rem 0;
        margin-bottom: 0.55rem;
        min-height: 120px;
        max-height: 280px;
        text-align: left;
    }
    .chess-chat-msg {
        padding: 0.45rem 0.6rem;
        border-radius: 2px;
        font-size: 0.82rem;
        line-height: 1.35;
        max-width: 100%;
        word-break: break-word;
    }
    .chess-chat-msg.mine {
        background: rgba(212,168,83,0.15);
        border: 1px solid rgba(212,168,83,0.25);
        align-self: flex-end;
    }
    .chess-chat-msg.theirs {
        background: rgba(0,0,0,0.28);
        border: 1px solid rgba(255,255,255,0.08);
        align-self: flex-start;
    }
    .chess-chat-msg.system {
        background: transparent;
        color: rgba(212,168,83,0.7);
        font-size: 0.75rem;
        text-align: center;
        align-self: center;
        font-style: italic;
    }
    .chess-chat-msg strong {
        color: var(--royal-gold);
        font-weight: 600;
        display: block;
        font-size: 0.72rem;
        margin-bottom: 0.1rem;
    }
    .chess-chat-form {
        display: flex;
        gap: 0.4rem;
    }
    .chess-chat-form input {
        flex: 1;
        min-width: 0;
        padding: 0.5rem 0.65rem;
        border-radius: 2px;
        border: 1px solid rgba(212,168,83,0.25);
        background: rgba(0,0,0,0.35);
        color: var(--royal-ivory);
        font-family: 'Source Sans 3', sans-serif;
        font-size: 0.88rem;
    }
    .chess-chat-form button {
        padding: 0.5rem 0.85rem;
        border-radius: 2px;
        border: 1px solid rgba(212,168,83,0.4);
        background: linear-gradient(180deg, #6b2038, var(--royal-burgundy));
        color: var(--royal-ivory);
        cursor: pointer;
        font-size: 0.85rem;
        white-space: nowrap;
    }
    .online-invite-bar {
        display: none;
        margin-top: 0.35rem;
        padding: 0.55rem 0.85rem;
        background: rgba(212,168,83,0.1);
        border: 1px solid rgba(212,168,83,0.25);
        border-radius: 2px;
        font-size: 0.85rem;
        color: rgba(245,240,230,0.85);
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }
    #gameArea.online-friends .online-invite-bar.show {
        display: flex;
    }
    .board-frame.board-flipped {
        transform: rotate(180deg);
    }
    .board-frame.board-flipped .square {
        transform: rotate(180deg);
    }

    /* ── Arena: board flanked by side panels ── */
    .chess-arena {
        display: grid;
        grid-template-columns: minmax(175px, 240px) auto minmax(175px, 240px);
        gap: 1rem;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    #gameArea.active .chess-arena {
        width: 100%;
        max-width: 100%;
        align-items: center;
        justify-content: center;
        grid-template-columns: minmax(175px, 240px) auto minmax(175px, 240px);
        gap: 1.15rem;
        padding: 0 clamp(0.5rem, 2vw, 2rem);
        --sq: min(
            calc((100vh - 210px) / 8),
            calc((100vw - 540px) / 8),
            12vmin
        );
    }
    @media (max-width: 960px), (hover: none) and (pointer: coarse) {
        .chess-arena {
            grid-template-columns: 1fr;
            grid-template-rows: auto auto auto;
            max-width: 400px;
        }
        #gameArea.active .chess-arena {
            --sq: min(calc((100vw - 24px) / 8), calc((100svh - 320px) / 8), 48px);
            padding: 0 0.25rem;
        }
        .chess-panel--left { order: 1; }
        .chess-board-col { order: 2; }
        .chess-panel--right { order: 3; }
        .panel-card { max-width: 100%; }
        .chess-turn-row .mobile-clock,
        .chess-turn-row .mobile-clock.active {
            display: inline-flex !important;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
        .chess-panel--left .player-strip .timer,
        .chess-panel--right .player-strip .timer,
        .chess-page #black-timer,
        .chess-page #white-timer {
            display: none !important;
        }
        .chess-turn-row .status {
            min-width: 0;
            flex: 1;
            max-width: 11rem;
            font-size: 0.82rem;
            padding: 0.35rem 0.55rem;
        }
        .player-icon { font-size: 1.35rem; }
        .panel-card { padding: 0.55rem 0.7rem; }
        .controls-bar {
            flex-wrap: wrap;
            gap: 0.4rem;
            justify-content: center;
        }
        .control-btn {
            min-height: 44px;
            min-width: 44px;
            padding: 0.5rem 0.75rem;
        }
        .square {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
    }

    .chess-panel {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        min-height: 100%;
    }
    .chess-panel--left,
    .chess-panel--right { justify-content: flex-start; }

    .panel-card {
        width: 100%;
        max-width: 240px;
        background: linear-gradient(160deg, rgba(42,24,16,0.92), rgba(18,14,22,0.96));
        border: 1px solid rgba(212,168,83,0.28);
        border-radius: 3px;
        padding: 0.9rem 1rem;
        text-align: left;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.05), 0 8px 24px rgba(0,0,0,0.35);
    }
    .panel-card h4 {
        font-family: 'Cinzel', serif;
        color: var(--royal-gold);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin: 0 0 0.5rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid rgba(212,168,83,0.2);
    }

    .player-strip {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 0.45rem;
    }
    .player-icon {
        font-size: 2rem;
        line-height: 1;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
    }
    .player-icon--white { color: #f8f4ec; text-shadow: 0 0 8px rgba(255,255,255,0.3); }
    .player-icon--black { color: #1a1a1a; text-shadow: 0 0 0 2px #888; }
    .player-meta { flex: 1; min-width: 0; }
    .player-label {
        font-family: 'Cinzel', serif;
        font-size: 0.88rem;
        color: var(--royal-ivory);
        letter-spacing: 0.06em;
    }
    .player-label small {
        display: block;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 0.65rem;
        color: rgba(212,168,83,0.7);
        letter-spacing: 0.02em;
        margin-top: 1px;
    }

    .timer {
        font-family: 'Source Sans 3', monospace;
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--royal-ivory);
        background: rgba(0,0,0,0.45);
        border: 1px solid rgba(212,168,83,0.2);
        border-radius: 2px;
        padding: 0.35rem 0.6rem;
        text-align: center;
        min-width: 78px;
        transition: all 0.3s;
    }
    .timer.active {
        border-color: var(--royal-gold);
        background: rgba(212,168,83,0.12);
        box-shadow: 0 0 16px rgba(212,168,83,0.35), inset 0 0 12px rgba(212,168,83,0.08);
        color: var(--royal-gold-light);
    }

    .captured-row {
        min-height: 36px;
        font-size: 1.4rem;
        line-height: 1.5;
        color: #c9b896;
        letter-spacing: 0.15em;
        word-break: break-all;
    }
    .chess-board-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.65rem;
        justify-content: center;
    }
    #gameArea.active .chess-board-col {
        flex: 0 1 auto;
        justify-content: center;
    }

    .board-pedestal {
        padding: 1.5rem 2rem 2rem;
        background:
            radial-gradient(ellipse at 50% 0%, rgba(212,168,83,0.08) 0%, transparent 60%),
            linear-gradient(180deg, rgba(30,20,15,0.6) 0%, transparent 100%);
        border-radius: 4px;
        position: relative;
    }
    .board-pedestal::after {
        content: '';
        position: absolute;
        bottom: 0.5rem;
        left: 10%;
        right: 10%;
        height: 12px;
        background: radial-gradient(ellipse, rgba(0,0,0,0.55) 0%, transparent 70%);
        filter: blur(8px);
        pointer-events: none;
    }
    #gameArea.active .board-pedestal {
        padding: clamp(0.5rem, 1.5vh, 1.25rem) clamp(0.75rem, 2vw, 1.5rem) clamp(0.75rem, 2vh, 1.5rem);
    }

    .board-frame {
        padding: 14px;
        background:
            linear-gradient(160deg, #5a3d28 0%, #3a2518 40%, #2a1810 100%);
        border-radius: 3px;
        box-shadow:
            0 0 0 1px rgba(212,168,83,0.2),
            0 0 0 4px #1a1008,
            0 0 0 6px rgba(201,162,39,0.35),
            0 30px 70px rgba(0,0,0,0.7),
            0 0 40px rgba(212,168,83,0.08),
            inset 0 2px 6px rgba(255,255,255,0.06),
            inset 0 -3px 8px rgba(0,0,0,0.4);
        position: relative;
        border: 2px solid;
        border-image: linear-gradient(135deg, #8b6914, #f0d78c, #c9a227, #f0d78c, #8b6914) 1;
    }
    .board-frame::before,
    .board-frame::after {
        content: '♛';
        position: absolute;
        font-size: 1rem;
        color: rgba(212,168,83,0.45);
        z-index: 3;
        text-shadow: 0 0 8px rgba(212,168,83,0.3);
    }
    .board-frame::before { top: 3px; left: 6px; }
    .board-frame::after { bottom: 3px; right: 6px; transform: rotate(180deg); }

    .board-inner-border {
        padding: 3px;
        background: linear-gradient(180deg, #c9a227 0%, #f0d78c 30%, #a67c00 70%, #c9a227 100%);
        box-shadow: inset 0 1px 2px rgba(255,255,255,0.4), inset 0 -1px 2px rgba(0,0,0,0.3);
    }

    .board {
        display: grid;
        grid-template-columns: repeat(8, minmax(58px, 76px));
        gap: 0;
        width: fit-content;
        margin: 0 auto;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.25);
    }
    #gameArea.active .board {
        grid-template-columns: repeat(8, var(--sq, 72px));
    }
    .square {
        width: 100%;
        aspect-ratio: 1;
        min-width: 58px;
        max-width: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(2.6rem, 5.5vw, 3.4rem);
        cursor: pointer;
        user-select: none;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        transition: filter 0.2s, box-shadow 0.2s;
        position: relative;
    }
    #gameArea.active .square {
        width: var(--sq, 72px);
        height: var(--sq, 72px);
        min-width: 0;
        max-width: none;
        font-size: calc(var(--sq, 72px) * 0.58);
    }
    .square:hover {
        filter: brightness(1.08);
        z-index: 1;
    }
    .square.light {
        background:
            radial-gradient(ellipse at 25% 20%, rgba(255,255,255,0.55) 0%, transparent 55%),
            radial-gradient(ellipse at 75% 85%, rgba(180,150,100,0.15) 0%, transparent 45%),
            linear-gradient(155deg, #faf4e8 0%, #ede0c8 45%, #e2d4b8 100%);
    }
    .square.dark {
        background:
            radial-gradient(ellipse at 30% 25%, rgba(90,70,50,0.4) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 75%, rgba(0,0,0,0.2) 0%, transparent 40%),
            linear-gradient(155deg, #4a3628 0%, #3d2c1e 50%, #2e2118 100%);
    }
    .square.selected {
        box-shadow:
            inset 0 0 0 2px var(--board-gold-bright),
            inset 0 0 24px rgba(212,168,83,0.45) !important;
        z-index: 2;
    }
    .square.valid-move::after {
        content: '';
        position: absolute;
        width: 22%;
        height: 22%;
        min-width: 10px;
        min-height: 10px;
        background: radial-gradient(circle, rgba(212,168,83,0.9) 0%, rgba(212,168,83,0.4) 100%);
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(212,168,83,0.6);
    }
    .square.valid-capture {
        box-shadow:
            inset 0 0 0 2px rgba(201,162,39,0.95),
            inset 0 0 16px rgba(212,168,83,0.35) !important;
    }
    .square.valid-capture::after {
        width: 78%;
        height: 78%;
        min-width: 0;
        min-height: 0;
        background: transparent;
        border: 2px solid rgba(212,168,83,0.75);
        border-radius: 50%;
        box-shadow: none;
    }
    .square.last-move {
        box-shadow: inset 0 0 0 999px rgba(212,168,83,0.18) !important;
    }
    .square.in-check {
        box-shadow: inset 0 0 28px rgba(180,40,40,0.55) !important;
        animation: check-pulse 1.4s ease-in-out infinite;
    }
    @keyframes check-pulse {
        0%, 100% { box-shadow: inset 0 0 16px rgba(180,40,40,0.5), inset 0 0 0 1px rgba(212,168,83,0.3); }
        50% { box-shadow: inset 0 0 28px rgba(220,50,50,0.75), inset 0 0 0 2px rgba(212,168,83,0.5); }
    }
    .coord {
        position: absolute;
        font-size: 0.55rem;
        font-weight: 700;
        opacity: 0.55;
        pointer-events: none;
    }
    .coord.file { bottom: 2px; right: 3px; color: #5c4030; }
    .coord.rank { top: 2px; left: 3px; color: #5c4030; }
    .square.dark .coord { color: #e8d4b0; opacity: 0.7; }
    .game-over-modal {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.82);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10001;
    }
    .game-over-modal.show { display: flex; }
    .game-over-box {
        background: linear-gradient(160deg, #2a1810, #14101c);
        border: 2px solid var(--royal-gold);
        border-radius: 3px;
        padding: 2rem;
        text-align: center;
        max-width: 360px;
        box-shadow: 0 0 40px rgba(212,168,83,0.15);
    }
    .game-over-box h2 {
        font-family: 'Cinzel', serif;
        color: var(--royal-gold);
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
        letter-spacing: 0.08em;
    }
    .control-btn.secondary {
        background: linear-gradient(180deg, #2a3d50, #1a2838);
        border-color: rgba(212,168,83,0.3);
    }
    .control-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .piece-white {
        color: #fffef5;
        filter: drop-shadow(0 3px 5px rgba(0,0,0,0.55));
        text-shadow:
            0 0 12px rgba(212,168,83,0.25),
            1px 1px 0 rgba(0,0,0,0.85),
            -1px -1px 0 rgba(0,0,0,0.85);
    }
    .piece-black {
        color: #14100c;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));
        text-shadow:
            0 0 1px rgba(212,168,83,0.5),
            0 1px 0 rgba(255,255,255,0.15);
    }
    .status {
        font-family: 'Cinzel', serif;
        font-size: 0.95rem;
        margin: 0;
        color: var(--royal-gold-light);
        text-shadow: 0 1px 3px rgba(0,0,0,0.6);
        letter-spacing: 0.06em;
        padding: 0.4rem 1rem;
        background: rgba(42,24,16,0.6);
        border: 1px solid rgba(212,168,83,0.2);
        border-radius: 2px;
        min-width: 200px;
    }

    .chess-turn-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        max-width: 400px;
    }

    .mobile-clock {
        display: none !important;
        align-items: center;
        justify-content: center;
        font-family: 'Source Sans 3', monospace;
        font-size: 0.7rem;
        font-weight: 700;
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 999px;
        padding: 0.2rem 0.45rem;
        min-width: 2.65rem;
        line-height: 1.2;
        transition: all 0.25s;
    }

    @media (max-width: 960px), (hover: none) and (pointer: coarse) {
        .chess-page .chess-turn-row .mobile-clock,
        .chess-page .chess-turn-row .mobile-clock.active {
            display: inline-flex !important;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
    }

    @media (min-width: 961px) and (hover: hover) and (pointer: fine) {
        .chess-page .mobile-clock {
            display: none !important;
        }
    }

    .mobile-clock.active {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.7);
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.25);
    }
    #gameArea {
        display: none;
        width: 100%;
        flex-direction: column;
        align-items: center;
    }
    #gameArea.active {
        display: flex;
        flex-direction: column;
        width: 100%;
        justify-content: flex-start;
    }

    .chess-footer {
        flex-shrink: 0;
        width: fit-content;
        max-width: 100%;
        margin: 0.5rem auto 0;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        padding: 0.85rem 1.4rem;
        background: rgba(42,24,16,0.55);
        border: 1px solid rgba(212,168,83,0.15);
        border-radius: 3px;
    }
    /* Always-visible controls while playing */
    #gameArea.active .chess-footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 900;
        width: 100%;
        max-width: 100%;
        margin: 0;
        border-radius: 0;
        border-left: none;
        border-right: none;
        border-bottom: none;
        padding: 0.7rem 1rem calc(0.7rem + env(safe-area-inset-bottom, 0px));
        background: rgba(26, 14, 10, 0.96);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 -6px 28px rgba(0,0,0,0.55);
    }
    .move-review-label {
        flex-basis: 100%;
        text-align: center;
        font-size: 0.82rem;
        color: rgba(212,168,83,0.85);
        font-family: Cinzel, serif;
        letter-spacing: 0.05em;
        margin: -0.25rem 0 0.15rem;
    }
    .move-review-label.reviewing {
        color: var(--royal-gold-light);
    }
    .control-btn {
        background: linear-gradient(180deg, #6b2038, var(--royal-burgundy));
        color: var(--royal-ivory);
        border: 1px solid rgba(212,168,83,0.35);
        padding: 0.55rem 1.2rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.25s;
        font-family: 'Source Sans 3', sans-serif;
    }
    .control-btn:hover {
        border-color: var(--royal-gold);
        box-shadow: 0 0 14px rgba(212,168,83,0.2);
        transform: translateY(-1px);
    }
    .control-btn.icon-side {
        min-width: 2.75rem;
        font-size: 1.05rem;
    }
    .promotion-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 10000;
    }
    .promotion-modal.show {
        display: flex;
    }
    .promotion-content {
        background: linear-gradient(160deg, #2a1810, #14101c);
        padding: 2rem;
        border-radius: 3px;
        border: 2px solid var(--royal-gold);
        text-align: center;
        box-shadow: 0 0 40px rgba(0,0,0,0.6);
    }
    .promotion-content h3 {
        font-family: 'Cinzel', serif;
        color: var(--royal-gold);
        margin-bottom: 1rem;
        letter-spacing: 0.1em;
    }
    .promotion-pieces {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
    .promotion-piece {
        font-size: 3rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 10px;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    .promotion-piece:hover {
        transform: scale(1.2);
        border-color: #ffd700;
        background-color: rgba(255,215,0,0.2);
    }
</style>

<div class="chess-page" data-build="20260611">
<div class="game-container">
    <h1 class="chess-title">Royal Chess <span>♟</span></h1>
    
    <div class="player-select" id="playerSelect">
        <h2>Choose Your Match</h2>

        @auth
            @if ($errors->has('chess_invite'))
            <div class="chess-invite-alerts">
                <div class="chess-invite-alert" style="border-color:rgba(239,68,68,0.5);">
                    <span>{{ $errors->first('chess_invite') }}</span>
                </div>
            </div>
            @endif
            @if (session('chess_invite_status'))
            <div class="chess-invite-alerts">
                <div class="chess-invite-alert" style="border-color:rgba(34,197,94,0.5);">
                    <span>{{ session('chess_invite_status') }}</span>
                </div>
            </div>
            @endif
            @if(($incomingChessInvites ?? collect())->isNotEmpty())
            <div class="chess-invite-alerts">
                @foreach($incomingChessInvites as $invite)
                <div class="chess-invite-alert">
                    <span>♟ <strong>{{ $invite->whitePlayer->name }}</strong> invited you — you play <strong>Black</strong></span>
                    <a href="{{ url('/chess?game='.$invite->token) }}" class="mode-btn">Join match</a>
                </div>
                @endforeach
            </div>
            @endif
            @if(($outgoingChessInvites ?? collect())->isNotEmpty())
            <div class="chess-invite-alerts">
                @foreach($outgoingChessInvites as $invite)
                <div class="chess-invite-alert">
                    <span>Waiting for <strong>{{ $invite->blackPlayer->name }}</strong> to join your match</span>
                    <a href="{{ url('/chess?game='.$invite->token) }}" class="mode-btn">Open match</a>
                </div>
                @endforeach
            </div>
            @endif
        @endauth

        <div class="match-modes">
            <div class="mode-card">
                <h3>Solo vs AI</h3>
                <p class="mode-desc">Pick a difficulty and play against the computer.</p>
                <div class="difficulty-buttons">
                    <h4>Difficulty</h4>
                    <button class="diff-btn" type="button" onclick="startSoloGame('easy')">Easy</button>
                    <button class="diff-btn" type="button" onclick="startSoloGame('medium')">Medium</button>
                    <button class="diff-btn" type="button" onclick="startSoloGame('hard')">Hard</button>
                </div>
            </div>
            <div class="mode-card">
                <h3>Two Players</h3>
                <p class="mode-desc">Pass the device and take turns on the same board.</p>
                <button class="mode-btn" type="button" onclick="startTwoPlayerGame()">Start Game</button>
            </div>
            <div class="mode-card">
                <h3>Play with Friends</h3>
                <p class="mode-desc">Create a room and share a code, or join with your friend's code. Same live board on mobile or desktop.</p>
                @auth
                    <button type="button" class="mode-btn" onclick="openChessRoomModal()">♟ Play with Friend</button>
                    @if(($openChessRooms ?? collect())->isNotEmpty())
                    <div style="margin-top:0.75rem;text-align:left;">
                        @foreach($openChessRooms as $room)
                        <div class="chess-invite-alert" style="margin-bottom:0.5rem;">
                            <span>Your room code: <strong>{{ $room->room_code }}</strong></span>
                            <a href="{{ url('/chess?game='.$room->token) }}" class="mode-btn">Open room</a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @else
                    <p class="friends-login-hint">You must <a href="{{ route('login') }}">log in</a> to play with friends.</p>
                @endauth
            </div>
        </div>
    </div>
    
    <div id="gameArea">
        <div class="chess-arena">
            <!-- LEFT: Black player + pieces White captured -->
            <aside class="chess-panel chess-panel--left">
                <div class="panel-card">
                    <div class="player-strip">
                        <span class="player-icon player-icon--black">♚</span>
                        <div class="player-meta">
                            <div class="player-label">Black <small id="black-role">Opponent</small></div>
                        </div>
                        <div class="timer" id="black-timer">10:00</div>
                    </div>
                    <h4>Taken by White</h4>
                    <div class="captured-row" id="captured-by-white">—</div>
                </div>
            </aside>

            <!-- CENTER: Board -->
            <div class="chess-board-col">
                <div class="chess-turn-row">
                    <span class="mobile-clock" id="black-timer-mobile" style="color:#fff!important;-webkit-text-fill-color:#fff!important">10:00</span>
                    <div class="status" id="status">White's Turn</div>
                    <span class="mobile-clock" id="white-timer-mobile" style="color:#fff!important;-webkit-text-fill-color:#fff!important">10:00</span>
                </div>
                <div class="online-invite-bar" id="onlineInviteBar">
                    <span id="onlineInviteText"></span>
                    <button type="button" class="friend-action-btn" id="copyGameLinkBtn" onclick="copyOnlineGameLink()">Copy link</button>
                    <button type="button" class="friend-action-btn primary" id="acceptOnlineBtn" style="display:none" onclick="acceptOnlineInvite()">Join match</button>
                </div>
                <div class="board-pedestal" id="boardPedestal">
                    <div class="board-frame">
                        <div class="board-inner-border">
                            <div class="board" id="board"></div>
                        </div>
                    </div>
                    <div class="chess-waiting-overlay" id="chessWaitingOverlay">
                        <h4>Waiting for your friend</h4>
                        <p>Share this room code. The game starts when they join.</p>
                        <div class="chess-waiting-code" id="chessWaitingCode">------</div>
                        <button type="button" class="mode-btn" onclick="copyOnlineGameLink()">Copy Code</button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: White player + live chat (online friends) -->
            <aside class="chess-panel chess-panel--right">
                <div class="panel-card" id="whitePanelCard">
                    <div class="player-strip">
                        <span class="player-icon player-icon--white">♔</span>
                        <div class="player-meta">
                            <div class="player-label">White <small id="white-role">You</small></div>
                        </div>
                        <div class="timer" id="white-timer">10:00</div>
                    </div>
                    <h4>Taken by Black</h4>
                    <div class="captured-row" id="captured-by-black">—</div>
                </div>
                <div class="panel-card chess-chat-panel" id="onlineChatPanel">
                    <h4>Live Chat</h4>
                    <div class="chess-chat-list" id="chessChatList"></div>
                    <form class="chess-chat-form" id="chessChatForm" onsubmit="return sendChessChat(event)">
                        <input type="text" id="chessChatInput" maxlength="500" placeholder="Message your opponent…" autocomplete="off">
                        <button type="submit">Send</button>
                    </form>
                </div>
            </aside>
        </div>
        
        <div class="chess-footer">
            <div class="move-review-label" id="moveReviewLabel"></div>
            <button class="control-btn secondary icon-side" type="button" onclick="showSelect()" title="Back to menu">☰ Menu</button>
            <button class="control-btn secondary icon-side" type="button" id="moveBackBtn" onclick="goMoveBack()" title="Previous move" disabled>◀ Back</button>
            <button class="control-btn secondary icon-side" type="button" id="moveForwardBtn" onclick="goMoveForward()" title="Next move" disabled>Forward ▶</button>
            <button class="control-btn secondary" id="hintBtn" onclick="showHint()">💡 Hint</button>
            <button class="control-btn" id="soundBtn" type="button" onclick="toggleChessSound()">🔊</button>
            <button class="control-btn" type="button" id="rematchBtn" onclick="rematchGame()" title="Rematch">↻ Rematch</button>
        </div>
    </div>
</div>
</div>

<div class="game-over-modal" id="gameOverModal">
    <div class="game-over-box">
        <h2 id="gameOverTitle">Game Over</h2>
        <p id="gameOverMsg" style="color:#cbd5e1; margin-bottom:1.2rem;"></p>
        <button class="control-btn" onclick="closeGameOver(); showSelect();">New Game</button>
        <button class="control-btn secondary" id="gameOverRematchBtn" onclick="closeGameOver(); rematchGame();">Rematch</button>
    </div>
</div>

<div class="promotion-modal" id="promotionModal">
    <div class="promotion-content">
        <h3>Promote Pawn</h3>
        <div class="promotion-pieces" id="promotionPieces"></div>
    </div>
</div>

<div class="chess-room-modal" id="chessRoomModal">
    <div class="chess-room-modal-box">
        <h3>♟ Play with a Friend</h3>
        <div class="chess-room-view active" id="roomChoiceView">
            <p>Create a room and share the code, or enter a friend's code to join.</p>
            <div class="chess-room-actions">
                <button type="button" class="mode-btn" id="createRoomBtn" onclick="createChessRoom()">Create Room</button>
                <button type="button" class="mode-btn" style="background:linear-gradient(180deg,#2a4a6b,#1a3050);" onclick="showEnterCodeView()">Enter Code</button>
            </div>
        </div>
        <div class="chess-room-view" id="roomCreateView">
            <p>Share this code with your friend:</p>
            <div class="chess-room-code" id="roomCodeDisplay">------</div>
            <div class="chess-room-actions">
                <button type="button" class="mode-btn" onclick="copyRoomCode()">Copy Code</button>
                <button type="button" class="mode-btn" onclick="enterCreatedRoom()">Enter Room (You = White)</button>
            </div>
        </div>
        <div class="chess-room-view" id="roomJoinView">
            <p>Enter the 6-character room code:</p>
            <input type="text" class="chess-room-input" id="roomCodeInput" maxlength="6" placeholder="ABC123" autocomplete="off">
            <button type="button" class="mode-btn" style="width:100%;" onclick="joinChessRoom()">Join as Black</button>
        </div>
        <button type="button" class="chess-room-close" onclick="closeChessRoomModal()">Close</button>
    </div>
</div>

<script>
    const chessFriends = @json($friends ?? []);
    const chessLoggedIn = @json(auth()->check());
    const chessUserId = @json($chessUserId ?? null);
    const chessUserName = @json($chessUserName ?? null);
    const chessCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function getChessCsrf() {
        const meta = document.querySelector('meta[name="csrf-token"]')?.content;
        if (meta) return meta;
        const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : '';
    }

    function showChessToast(message, isError = false) {
        let toast = document.getElementById('chessActionToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'chessActionToast';
            toast.className = 'chess-invite-alert';
            toast.style.cssText = 'position:fixed;top:5rem;left:50%;transform:translateX(-50%);z-index:9999;max-width:min(92vw,480px);';
            document.body.appendChild(toast);
        }
        toast.style.borderColor = isError ? 'rgba(239,68,68,0.5)' : 'rgba(34,197,94,0.5)';
        toast.textContent = message;
        toast.style.display = 'flex';
        clearTimeout(toast._hideTimer);
        toast._hideTimer = setTimeout(() => { toast.style.display = 'none'; }, 5000);
    }

    const PIECES = {
        'R': '♖', 'N': '♘', 'B': '♗', 'Q': '♕', 'K': '♔', 'P': '♙',
        'r': '♜', 'n': '♞', 'b': '♝', 'q': '♛', 'k': '♚', 'p': '♟'
    };

    const PIECE_VALUES = {
        'p': 100, 'n': 320, 'b': 330, 'r': 500, 'q': 900, 'k': 20000
    };

    let board = [];
    let selectedSquare = null;
    let currentPlayer = 'white';
    let validMoves = [];
    let gameMode = '2p';
    let difficulty = 'easy';
    let castlingRights = {
        white: { kingSide: true, queenSide: true },
        black: { kingSide: true, queenSide: true }
    };
    let enPassantTarget = null;
    let moveHistory = [];
    let positionHistory = [];
    let historyIndex = -1;
    let friendOpponentName = null;
    let gameOver = false;
    let lastGameOverInfo = null;
    let reviewingHistory = false;
    let pendingPromotion = null;
    let lastMovedPieceType = null;
    let lastMove = null;
    let capturedByWhite = [];
    let capturedByBlack = [];

    let whiteTime = 600;
    let blackTime = 600;
    let timerInterval = null;

    let onlineGame = null;
    let myColor = null;
    let onlinePollTimer = null;
    let applyingRemote = false;
    let lastWinner = null;
    let pendingInvites = { incoming: [], outgoing: [], active: [] };

    const FILES = 'abcdefgh';
    const PST_PAWN = [0,5,5,-10,-10,5,5,0, 2,2,2,2,2,2,2,2, 5,5,10,15,15,10,5,5, 10,10,15,20,20,15,10,10, 15,15,20,25,25,20,15,15, 20,25,25,30,30,25,25,20, 30,30,30,35,35,30,30,30, 0,0,0,0,0,0,0,0];
    const PST_KNIGHT = [-50,-40,-30,-30,-30,-30,-40,-50,-40,-20,0,0,0,0,-20,-40,-30,0,10,15,15,10,0,-30,-30,5,15,20,20,15,5,-30,-30,0,15,20,20,15,0,-30,-30,5,15,20,20,15,5,-30,-40,-20,0,5,5,0,-20,-40,-50,-40,-30,-30,-30,-30,-40,-50];

    function playMoveSound({ pieceType, captured, castled }) {
        GameSounds.init();
        if (castled) GameSounds.play('castle');
        else if (captured) GameSounds.play('capture');
        else if (pieceType === 'p') GameSounds.play('pawnMove');
        else GameSounds.play('move');
    }

    function toggleChessSound() {
        GameSounds.init();
        const on = GameSounds.toggle();
        document.getElementById('soundBtn').textContent = on ? '🔊' : '🔇';
    }

    function initBoard() {
        board = [
            ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'],
            ['p', 'p', 'p', 'p', 'p', 'p', 'p', 'p'],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P'],
            ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R']
        ];
        selectedSquare = null;
        currentPlayer = 'white';
        validMoves = [];
        castlingRights = {
            white: { kingSide: true, queenSide: true },
            black: { kingSide: true, queenSide: true }
        };
        enPassantTarget = null;
        moveHistory = [];
        positionHistory = [];
        historyIndex = -1;
        gameOver = false;
        lastGameOverInfo = null;
        reviewingHistory = false;
        pendingPromotion = null;
        lastMove = null;
        capturedByWhite = [];
        capturedByBlack = [];
        updateCapturedDisplay();
        closeGameOver();
    }

    function squareName(row, col) {
        return FILES[col] + (8 - row);
    }

    function updateCapturedDisplay() {
        document.getElementById('captured-by-white').textContent =
            capturedByWhite.map(p => PIECES[p]).join(' ') || '—';
        document.getElementById('captured-by-black').textContent =
            capturedByBlack.map(p => PIECES[p]).join(' ') || '—';
    }

    function recordCapture(piece, capturerColor) {
        if (!piece) return;
        if (capturerColor === 'white') capturedByWhite.push(piece);
        else capturedByBlack.push(piece);
        updateCapturedDisplay();
    }

    function closeGameOver() {
        document.getElementById('gameOverModal').classList.remove('show');
    }

    function showGameOver(title, msg) {
        lastGameOverInfo = { title, msg };
        document.getElementById('gameOverTitle').textContent = title;
        document.getElementById('gameOverMsg').textContent = msg;
        document.getElementById('gameOverModal').classList.add('show');
    }

    function pstValue(type, row, col, color) {
        const idx = color === 'white' ? row * 8 + col : (7 - row) * 8 + col;
        const tables = { p: PST_PAWN, n: PST_KNIGHT };
        return (tables[type] || new Array(64).fill(0))[idx] || 0;
    }

    function getPieceColor(piece) {
        if (!piece) return null;
        return piece === piece.toUpperCase() ? 'white' : 'black';
    }

    function isValidPosition(row, col) {
        return row >= 0 && row < 8 && col >= 0 && col < 8;
    }

    function snapshotState() {
        return {
            board: cloneBoard(board),
            currentPlayer,
            castlingRights: JSON.parse(JSON.stringify(castlingRights)),
            enPassantTarget: enPassantTarget ? { ...enPassantTarget } : null,
            capturedByWhite: [...capturedByWhite],
            capturedByBlack: [...capturedByBlack],
            lastMove: lastMove ? { from: { ...lastMove.from }, to: { ...lastMove.to } } : null,
            whiteTime,
            blackTime,
            gameOver,
            gameOverInfo: gameOver && lastGameOverInfo ? { ...lastGameOverInfo } : null,
            statusText: document.getElementById('status').textContent,
        };
    }

    function updateReviewLabel() {
        const label = document.getElementById('moveReviewLabel');
        if (!label) return;
        const totalMoves = Math.max(0, positionHistory.length - 1);
        if (totalMoves === 0) {
            label.textContent = '';
            label.classList.remove('reviewing');
            return;
        }
        if (!isAtLatestPosition()) {
            label.textContent = `Reviewing move ${historyIndex} of ${totalMoves} — go Forward to continue playing`;
            label.classList.add('reviewing');
        } else {
            label.textContent = totalMoves > 0 ? `Move ${totalMoves} — use Back to review previous moves` : '';
            label.classList.remove('reviewing');
        }
    }

    function applySnapshot(snap) {
        board = cloneBoard(snap.board);
        currentPlayer = snap.currentPlayer;
        castlingRights = JSON.parse(JSON.stringify(snap.castlingRights));
        enPassantTarget = snap.enPassantTarget ? { ...snap.enPassantTarget } : null;
        capturedByWhite = [...snap.capturedByWhite];
        capturedByBlack = [...snap.capturedByBlack];
        lastMove = snap.lastMove;
        whiteTime = snap.whiteTime;
        blackTime = snap.blackTime;
        gameOver = snap.gameOver;
        selectedSquare = null;
        validMoves = [];
        pendingPromotion = null;
        updateCapturedDisplay();
        updateTimerDisplay();
        closeGameOver();

        if (isAtLatestPosition()) {
            lastGameOverInfo = snap.gameOverInfo;
            renderBoard();
            if (snap.gameOver && snap.gameOverInfo) {
                showGameOver(snap.gameOverInfo.title, snap.gameOverInfo.msg);
            }
        } else {
            reviewingHistory = true;
            document.getElementById('status').textContent = snap.statusText || `${cap(currentPlayer)}'s turn`;
            renderBoard(true);
            reviewingHistory = false;
        }

        updateHistoryButtons();
        updateReviewLabel();
    }

    function isAtLatestPosition() {
        return historyIndex === positionHistory.length - 1;
    }

    function updateHistoryButtons() {
        const backBtn = document.getElementById('moveBackBtn');
        const forwardBtn = document.getElementById('moveForwardBtn');
        if (!backBtn || !forwardBtn) return;
        backBtn.disabled = historyIndex <= 0;
        forwardBtn.disabled = historyIndex >= positionHistory.length - 1;
    }

    function recordPosition() {
        const snap = snapshotState();
        if (historyIndex < positionHistory.length - 1) {
            positionHistory = positionHistory.slice(0, historyIndex + 1);
        }
        positionHistory.push(snap);
        historyIndex = positionHistory.length - 1;
        updateHistoryButtons();
        updateReviewLabel();
    }

    function resetHistory() {
        positionHistory = [];
        historyIndex = -1;
        recordPosition();
    }

    function pauseTimerForReview() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    function resumeTimerIfNeeded() {
        if (isAtLatestPosition() && !gameOver && !timerInterval) {
            startTimer();
        }
    }

    function goMoveBack() {
        if (historyIndex <= 0) return;
        GameSounds.play('click');
        historyIndex--;
        applySnapshot(positionHistory[historyIndex]);
        if (!isAtLatestPosition()) pauseTimerForReview();
    }

    function goMoveForward() {
        if (historyIndex >= positionHistory.length - 1) return;
        GameSounds.play('click');
        historyIndex++;
        applySnapshot(positionHistory[historyIndex]);
        if (isAtLatestPosition()) resumeTimerIfNeeded();
        else pauseTimerForReview();
    }

    function cloneBoard(b) {
        return b.map(row => row.slice());
    }

    function makeMoveNoCheck(fromRow, fromCol, toRow, toCol, promotionPiece = null, epTarget = enPassantTarget) {
        const newBoard = cloneBoard(board);
        const piece = newBoard[fromRow][fromCol];
        
        if (!piece) return newBoard;
        
        newBoard[toRow][toCol] = piece;
        newBoard[fromRow][fromCol] = '';
        
        const type = piece.toLowerCase();
        const color = getPieceColor(piece);
        
        if (type === 'k' && Math.abs(toCol - fromCol) === 2) {
            if (toCol > fromCol) {
                newBoard[fromRow][5] = newBoard[fromRow][7];
                newBoard[fromRow][7] = '';
            } else {
                newBoard[fromRow][3] = newBoard[fromRow][0];
                newBoard[fromRow][0] = '';
            }
        }
        
        if (type === 'p' && epTarget && toRow === epTarget.row && toCol === epTarget.col) {
            if (color === 'white') {
                newBoard[toRow + 1][toCol] = '';
            } else {
                newBoard[toRow - 1][toCol] = '';
            }
        }
        
        if (type === 'p' && (toRow === 0 || toRow === 7)) {
            if (promotionPiece) {
                newBoard[toRow][toCol] = color === 'white' ? promotionPiece.toUpperCase() : promotionPiece.toLowerCase();
            }
        }
        
        return newBoard;
    }

    function getKingPosition(b, color) {
        const king = color === 'white' ? 'K' : 'k';
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                if (b[r][c] === king) {
                    return { row: r, col: c };
                }
            }
        }
        return null;
    }

    function isSquareAttacked(b, row, col, byColor) {
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const piece = b[r][c];
                if (!piece || getPieceColor(piece) !== byColor) continue;
                
                const type = piece.toLowerCase();
                const color = getPieceColor(piece);
                
                if (type === 'p') {
                    const dir = color === 'white' ? -1 : 1;
                    if (r + dir === row && (c + 1 === col || c - 1 === col)) {
                        return true;
                    }
                } else if (type === 'r') {
                    if (isRookAttack(b, r, c, row, col)) return true;
                } else if (type === 'n') {
                    const dr = Math.abs(row - r);
                    const dc = Math.abs(col - c);
                    if ((dr === 2 && dc === 1) || (dr === 1 && dc === 2)) {
                        return true;
                    }
                } else if (type === 'b') {
                    if (isBishopAttack(b, r, c, row, col)) return true;
                } else if (type === 'q') {
                    if (isRookAttack(b, r, c, row, col) || isBishopAttack(b, r, c, row, col)) {
                        return true;
                    }
                } else if (type === 'k') {
                    const dr = Math.abs(row - r);
                    const dc = Math.abs(col - c);
                    if (dr <= 1 && dc <= 1) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function isRookAttack(b, r1, c1, r2, c2) {
        if (r1 !== r2 && c1 !== c2) return false;
        const dr = r1 === r2 ? 0 : (r2 > r1 ? 1 : -1);
        const dc = c1 === c2 ? 0 : (c2 > c1 ? 1 : -1);
        let r = r1 + dr;
        let c = c1 + dc;
        while (r >= 0 && r < 8 && c >= 0 && c < 8) {
            if (r === r2 && c === c2) return true;
            if (b[r][c]) break;
            r += dr;
            c += dc;
        }
        return false;
    }

    function isBishopAttack(b, r1, c1, r2, c2) {
        if (Math.abs(r2 - r1) !== Math.abs(c2 - c1)) return false;
        const dr = r2 > r1 ? 1 : -1;
        const dc = c2 > c1 ? 1 : -1;
        let r = r1 + dr;
        let c = c1 + dc;
        while (r >= 0 && r < 8 && c >= 0 && c < 8) {
            if (r === r2 && c === c2) return true;
            if (b[r][c]) break;
            r += dr;
            c += dc;
        }
        return false;
    }

    function isInCheck(b, color) {
        const kingPos = getKingPosition(b, color);
        if (!kingPos) return false;
        const enemyColor = color === 'white' ? 'black' : 'white';
        return isSquareAttacked(b, kingPos.row, kingPos.col, enemyColor);
    }

    function getValidMoves(row, col) {
        const piece = board[row][col];
        if (!piece) return [];
        
        const color = getPieceColor(piece);
        if (color !== currentPlayer) return [];
        
        const type = piece.toLowerCase();
        const moves = [];
        
        if (type === 'p') {
            const dir = color === 'white' ? -1 : 1;
            const startRow = color === 'white' ? 6 : 1;
            
            if (isValidPosition(row + dir, col) && !board[row + dir][col]) {
                if (isMoveSafe(row, col, row + dir, col)) {
                    moves.push({ row: row + dir, col: col });
                }
                if (row === startRow && !board[row + 2 * dir][col]) {
                    if (isMoveSafe(row, col, row + 2 * dir, col)) {
                        moves.push({ row: row + 2 * dir, col: col });
                    }
                }
            }
            
            [-1, 1].forEach(dc => {
                const nr = row + dir;
                const nc = col + dc;
                if (isValidPosition(nr, nc)) {
                    const target = board[nr][nc];
                    if (target && getPieceColor(target) !== color) {
                        if (isMoveSafe(row, col, nr, nc)) {
                            moves.push({ row: nr, col: nc });
                        }
                    } else if (enPassantTarget && nr === enPassantTarget.row && nc === enPassantTarget.col) {
                        if (isMoveSafe(row, col, nr, nc)) {
                            moves.push({ row: nr, col: nc });
                        }
                    }
                }
            });
        } else if (type === 'r') {
                [[0,1],[0,-1],[1,0],[-1,0]].forEach(([dr, dc]) => {
                    let r = row + dr;
                    let c = col + dc;
                    while (isValidPosition(r, c)) {
                        if (board[r][c]) {
                            if (getPieceColor(board[r][c]) !== color) {
                                if (isMoveSafe(row, col, r, c)) {
                                    moves.push({ row: r, col: c });
                                }
                            }
                            break;
                        }
                        if (isMoveSafe(row, col, r, c)) {
                            moves.push({ row: r, col: c });
                        }
                        r += dr;
                        c += dc;
                    }
                });
        } else if (type === 'n') {
                [[2,1],[2,-1],[-2,1],[-2,-1],[1,2],[1,-2],[-1,2],[-1,-2]].forEach(([dr, dc]) => {
                    const r = row + dr;
                    const c = col + dc;
                    if (isValidPosition(r, c)) {
                        if (!board[r][c] || getPieceColor(board[r][c]) !== color) {
                            if (isMoveSafe(row, col, r, c)) {
                                moves.push({ row: r, col: c });
                            }
                        }
                    }
                });
            } else if (type === 'b') {
                [[1,1],[1,-1],[-1,1],[-1,-1]].forEach(([dr, dc]) => {
                    let r = row + dr;
                    let c = col + dc;
                    while (isValidPosition(r, c)) {
                        if (board[r][c]) {
                            if (getPieceColor(board[r][c]) !== color) {
                                if (isMoveSafe(row, col, r, c)) {
                                    moves.push({ row: r, col: c });
                                }
                            }
                            break;
                        }
                        if (isMoveSafe(row, col, r, c)) {
                            moves.push({ row: r, col: c });
                        }
                        r += dr;
                        c += dc;
                    }
                });
            } else if (type === 'q') {
                [[0,1],[0,-1],[1,0],[-1,0],[1,1],[1,-1],[-1,1],[-1,-1]].forEach(([dr, dc]) => {
                    let r = row + dr;
                    let c = col + dc;
                    while (isValidPosition(r, c)) {
                        if (board[r][c]) {
                            if (getPieceColor(board[r][c]) !== color) {
                                if (isMoveSafe(row, col, r, c)) {
                                    moves.push({ row: r, col: c });
                                }
                            }
                            break;
                        }
                        if (isMoveSafe(row, col, r, c)) {
                            moves.push({ row: r, col: c });
                        }
                        r += dr;
                        c += dc;
                    }
                });
            } else if (type === 'k') {
                [[0,1],[0,-1],[1,0],[-1,0],[1,1],[1,-1],[-1,1],[-1,-1]].forEach(([dr, dc]) => {
                    const r = row + dr;
                    const c = col + dc;
                    if (isValidPosition(r, c)) {
                        if (!board[r][c] || getPieceColor(board[r][c]) !== color) {
                            if (isMoveSafe(row, col, r, c)) {
                                moves.push({ row: r, col: c });
                            }
                        }
                    }
                });
            
            const rights = castlingRights[color];
            const kingRow = color === 'white' ? 7 : 0;
            const enemyColor = color === 'white' ? 'black' : 'white';
            
            if (row === kingRow && col === 4 && !isInCheck(board, color)) {
                if (rights.kingSide && !board[kingRow][5] && !board[kingRow][6]) {
                    if (!isSquareAttacked(board, kingRow, 5, enemyColor) && 
                        !isSquareAttacked(board, kingRow, 6, enemyColor)) {
                        moves.push({ row: kingRow, col: 6 });
                    }
                }
                if (rights.queenSide && !board[kingRow][1] && !board[kingRow][2] && !board[kingRow][3]) {
                    if (!isSquareAttacked(board, kingRow, 2, enemyColor) && 
                        !isSquareAttacked(board, kingRow, 3, enemyColor)) {
                        moves.push({ row: kingRow, col: 2 });
                    }
                }
            }
        }
        
        return moves;
    }

    function isMoveSafe(fromRow, fromCol, toRow, toCol) {
        const testBoard = makeMoveNoCheck(fromRow, fromCol, toRow, toCol);
        return !isInCheck(testBoard, getPieceColor(board[fromRow][fromCol]));
    }

    function hasLegalMoves(color) {
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const piece = board[r][c];
                if (piece && getPieceColor(piece) === color) {
                    const tempSelected = selectedSquare;
                    selectedSquare = { row: r, col: c };
                    const moves = getValidMoves(r, c);
                    selectedSquare = tempSelected;
                    if (moves.length > 0) return true;
                }
            }
        }
        return false;
    }

    function evaluateBoard(b) {
        let score = 0;
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const p = b[r][c];
                if (!p) continue;
                const type = p.toLowerCase();
                const color = getPieceColor(p);
                const val = PIECE_VALUES[type] + pstValue(type, r, c, color);
                score += color === 'black' ? val : -val;
            }
        }
        if (isInCheck(b, 'white')) score -= 40;
        if (isInCheck(b, 'black')) score += 40;
        return score;
    }

    function showHint() {
        if (gameOver || !isAtLatestPosition() || (gameMode === '1p' && currentPlayer === 'black') || (gameMode === 'online' && currentPlayer !== myColor)) return;
        const moves = [];
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                if (board[r][c] && getPieceColor(board[r][c]) === currentPlayer) {
                    const saved = selectedSquare;
                    selectedSquare = { row: r, col: c };
                    getValidMoves(r, c).forEach(m => moves.push({ from: { r, c }, to: m }));
                    selectedSquare = saved;
                }
            }
        }
        if (!moves.length) return;
        let best = null, bestScore = currentPlayer === 'white' ? Infinity : -Infinity;
        const maximizing = currentPlayer === 'black';
        for (const m of moves) {
            const nb = makeMoveNoCheck(m.from.r, m.from.c, m.to.row, m.to.col);
            const sc = evaluateBoard(nb);
            if (currentPlayer === 'white' && sc < bestScore) { bestScore = sc; best = m; }
            if (currentPlayer === 'black' && sc > bestScore) { bestScore = sc; best = m; }
        }
        if (best) {
            selectedSquare = { row: best.from.r, col: best.from.c };
            validMoves = [best.to];
            GameSounds.play('click');
            renderBoard();
            document.getElementById('status').textContent =
                `Hint: ${PIECES[board[best.from.r][best.from.c]]} → ${squareName(best.to.row, best.to.col)}`;
        }
    }

    function getAllMovesForAI(color, b) {
        const originalBoard = board;
        board = b;
        const moves = [];
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const piece = b[r][c];
                if (piece && getPieceColor(piece) === color) {
                    const pieceMoves = getValidMoves(r, c);
                    pieceMoves.forEach(m => {
                        moves.push({ fromRow: r, fromCol: c, toRow: m.row, toCol: m.col });
                    });
                }
            }
        }
        board = originalBoard;
        return moves;
    }

    function minimax(b, depth, maximizing, alpha, beta) {
        const originalBoard = board;
        board = b;
        
        if (depth === 0) {
            const score = evaluateBoard(b);
            board = originalBoard;
            return score;
        }
        
        const color = maximizing ? 'black' : 'white';
        const moves = getAllMovesForAI(color, b);
        board = originalBoard;
        
        if (moves.length === 0) {
            return maximizing ? -Infinity : Infinity;
        }
        
        if (maximizing) {
            let maxEval = -Infinity;
            for (const move of moves) {
                const newBoard = makeMoveNoCheck(move.fromRow, move.fromCol, move.toRow, move.toCol);
                const evalResult = minimax(newBoard, depth - 1, false, alpha, beta);
                maxEval = Math.max(maxEval, evalResult);
                alpha = Math.max(alpha, evalResult);
                if (beta <= alpha) break;
            }
            return maxEval;
        } else {
            let minEval = Infinity;
            for (const move of moves) {
                const newBoard = makeMoveNoCheck(move.fromRow, move.fromCol, move.toRow, move.toCol);
                const evalResult = minimax(newBoard, depth - 1, true, alpha, beta);
                minEval = Math.min(minEval, evalResult);
                beta = Math.min(beta, evalResult);
                if (beta <= alpha) break;
            }
            return minEval;
        }
    }

    function getBestMove() {
        const originalBoard = board;
        const moves = getAllMovesForAI('black', board);
        board = originalBoard;
        
        if (moves.length === 0) return null;
        
        let depth = difficulty === 'easy' ? 2 : difficulty === 'medium' ? 3 : 4;

        moves.sort((a, b) => {
            const capA = board[a.toRow][a.toCol] ? 10 : 0;
            const capB = board[b.toRow][b.toCol] ? 10 : 0;
            return capB - capA;
        });

        let bestMove = moves[0];
        let bestScore = -Infinity;

        for (const move of moves) {
            const newBoard = makeMoveNoCheck(move.fromRow, move.fromCol, move.toRow, move.toCol);
            const score = minimax(newBoard, depth - 1, false, -Infinity, Infinity);
            if (score > bestScore) {
                bestScore = score;
                bestMove = move;
            }
        }
        return bestMove;
    }

    function updateStatus() {
        if (reviewingHistory) return;

        const statusEl = document.getElementById('status');
        if (gameOver) return;

        if (isInCheck(board, currentPlayer)) {
            if (!hasLegalMoves(currentPlayer)) {
                gameOver = true;
                clearInterval(timerInterval);
                const winner = currentPlayer === 'white' ? 'Black' : 'White';
                lastWinner = winner.toLowerCase();
                statusEl.textContent = `Checkmate! ${winner} wins!`;
                GameSounds.play('checkmate');
                showGameOver('Checkmate!', `${winner} wins by checkmate.`);
            } else {
                statusEl.textContent = `${cap(currentPlayer)} — CHECK!`;
            }
        } else if (!hasLegalMoves(currentPlayer)) {
            gameOver = true;
            lastWinner = 'draw';
            clearInterval(timerInterval);
            statusEl.textContent = 'Stalemate — Draw!';
            GameSounds.play('draw');
            showGameOver('Stalemate', 'The game is a draw.');
        } else if (gameMode === '1p' && currentPlayer === 'black') {
            statusEl.textContent = 'AI is thinking...';
        } else {
            statusEl.textContent = `${cap(currentPlayer)}'s turn`;
        }
    }

    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    function renderBoard(skipStatusUpdate = false) {
        const boardEl = document.getElementById('board');
        boardEl.innerHTML = '';

        const whiteKing = getKingPosition(board, 'white');
        const blackKing = getKingPosition(board, 'black');
        const whiteInCheck = isInCheck(board, 'white');
        const blackInCheck = isInCheck(board, 'black');

        for (let row = 0; row < 8; row++) {
            for (let col = 0; col < 8; col++) {
                const square = document.createElement('div');
                const isLight = (row + col) % 2 === 0;
                square.className = `square ${isLight ? 'light' : 'dark'}`;

                const piece = board[row][col];
                if (piece) {
                    square.textContent = PIECES[piece];
                    square.classList.add(getPieceColor(piece) === 'white' ? 'piece-white' : 'piece-black');
                }

                if (lastMove && (
                    (lastMove.from.row === row && lastMove.from.col === col) ||
                    (lastMove.to.row === row && lastMove.to.col === col)
                )) square.classList.add('last-move');

                if (whiteInCheck && whiteKing && whiteKing.row === row && whiteKing.col === col) square.classList.add('in-check');
                if (blackInCheck && blackKing && blackKing.row === row && blackKing.col === col) square.classList.add('in-check');

                if (selectedSquare && selectedSquare.row === row && selectedSquare.col === col) square.classList.add('selected');

                const isValid = validMoves.some(m => m.row === row && m.col === col);
                if (isValid) square.classList.add(board[row][col] ? 'valid-capture' : 'valid-move');

                if (col === 7) {
                    const rk = document.createElement('span');
                    rk.className = 'coord rank';
                    rk.textContent = 8 - row;
                    square.appendChild(rk);
                }
                if (row === 7) {
                    const fl = document.createElement('span');
                    fl.className = 'coord file';
                    fl.textContent = FILES[col];
                    square.appendChild(fl);
                }

                square.addEventListener('click', () => handleSquareClick(row, col));
                boardEl.appendChild(square);
            }
        }
        if (!skipStatusUpdate) updateStatus();
    }

    function showPromotionModal(color) {
        const modal = document.getElementById('promotionModal');
        const piecesContainer = document.getElementById('promotionPieces');
        piecesContainer.innerHTML = '';
        
        const pieceOptions = ['q', 'r', 'b', 'n'];
        pieceOptions.forEach(p => {
            const pieceDiv = document.createElement('div');
            pieceDiv.className = 'promotion-piece';
            pieceDiv.textContent = color === 'white' ? PIECES[p.toUpperCase()] : PIECES[p];
            pieceDiv.onclick = () => completePromotion(p);
            piecesContainer.appendChild(pieceDiv);
        });
        
        modal.classList.add('show');
    }

    function completePromotion(pieceType) {
        if (!pendingPromotion) return;

        const { fromRow, fromCol, toRow, toCol } = pendingPromotion;
        lastMovedPieceType = 'p';
        lastMove = { from: { row: fromRow, col: fromCol }, to: { row: toRow, col: toCol } };
        board[toRow][toCol] = currentPlayer === 'white' ? pieceType.toUpperCase() : pieceType.toLowerCase();
        board[fromRow][fromCol] = '';

        document.getElementById('promotionModal').classList.remove('show');
        pendingPromotion = null;

        finishMove();
    }

    function finishMove(soundInfo = null) {
        if (soundInfo) playMoveSound(soundInfo);

        currentPlayer = currentPlayer === 'white' ? 'black' : 'white';
        selectedSquare = null;
        validMoves = [];
        lastMovedPieceType = null;
        updateTimerDisplay();
        renderBoard();
        recordPosition();

        if (!gameOver && isInCheck(board, currentPlayer)) GameSounds.play('check');

        if (gameMode === 'online') {
            syncMoveToServer();
        } else if (gameMode === '1p' && currentPlayer === 'black' && !gameOver && isAtLatestPosition()) {
            setTimeout(makeAIMove, 450);
        }
    }

    function handleSquareClick(row, col) {
        if (gameOver) return;
        if (!isAtLatestPosition()) return;
        if (gameMode === 'online') {
            if (onlineGame?.status === 'pending') return;
            if (currentPlayer !== myColor) return;
        }
        if (gameMode === '1p' && currentPlayer === 'black') return;
        
        const piece = board[row][col];
        const color = getPieceColor(piece);

        if (selectedSquare) {
            const isValid = validMoves.some(m => m.row === row && m.col === col);
            if (isValid) {
                const movingPiece = board[selectedSquare.row][selectedSquare.col];
                const movingType = movingPiece.toLowerCase();
                const movingColor = getPieceColor(movingPiece);
                lastMovedPieceType = movingType;
                
                lastMove = { from: { row: selectedSquare.row, col: selectedSquare.col }, to: { row, col } };

                let capturedPiece = board[row][col];
                if (movingType === 'p' && enPassantTarget && row === enPassantTarget.row && col === enPassantTarget.col) {
                    capturedPiece = movingColor === 'white' ? board[row + 1][col] : board[row - 1][col];
                }
                if (capturedPiece) recordCapture(capturedPiece, movingColor);

                if (movingType === 'p' && (row === 0 || row === 7)) {
                    pendingPromotion = {
                        fromRow: selectedSquare.row,
                        fromCol: selectedSquare.col,
                        toRow: row,
                        toCol: col
                    };
                    board[row][col] = board[selectedSquare.row][selectedSquare.col];
                    board[selectedSquare.row][selectedSquare.col] = '';
                    showPromotionModal(movingColor);
                    renderBoard();
                    return;
                }

                if (movingType === 'k') {
                    if (selectedSquare.col === 4 && col === 6) {
                        castlingRights[movingColor].kingSide = false;
                        castlingRights[movingColor].queenSide = false;
                        board[row][3] = board[row][7];
                        board[row][7] = '';
                    } else if (selectedSquare.col === 4 && col === 2) {
                        castlingRights[movingColor].kingSide = false;
                        castlingRights[movingColor].queenSide = false;
                        board[row][5] = board[row][0];
                        board[row][0] = '';
                    } else {
                        castlingRights[movingColor].kingSide = false;
                        castlingRights[movingColor].queenSide = false;
                    }
                } else if (movingType === 'r') {
                    if (selectedSquare.row === (movingColor === 'white' ? 7 : 0) && selectedSquare.col === 0) {
                        castlingRights[movingColor].queenSide = false;
                    } else if (selectedSquare.row === (movingColor === 'white' ? 7 : 0) && selectedSquare.col === 7) {
                        castlingRights[movingColor].kingSide = false;
                    }
                }
                
                if (movingType === 'p' && enPassantTarget && row === enPassantTarget.row && col === enPassantTarget.col) {
                    if (movingColor === 'white') {
                        board[row + 1][col] = '';
                    } else {
                        board[row - 1][col] = '';
                    }
                }
                
                if (movingType === 'p' && Math.abs(row - selectedSquare.row) === 2) {
                    enPassantTarget = { row: (selectedSquare.row + row) / 2, col: col };
                } else {
                    enPassantTarget = null;
                }
                
                const captured = !!capturedPiece;
                const castled = movingType === 'k' && Math.abs(col - selectedSquare.col) === 2;

                board[row][col] = board[selectedSquare.row][selectedSquare.col];
                board[selectedSquare.row][selectedSquare.col] = '';
                
                finishMove({ pieceType: movingType, captured, castled });
            } else if (piece && color === currentPlayer) {
                selectedSquare = { row, col };
                validMoves = getValidMoves(row, col);
                renderBoard();
            } else {
                selectedSquare = null;
                validMoves = [];
                renderBoard();
            }
        } else if (piece && color === currentPlayer) {
            selectedSquare = { row, col };
            validMoves = getValidMoves(row, col);
            renderBoard();
        }
    }

    function makeAIMove() {
        if (gameOver || !isAtLatestPosition()) return;
        const move = getBestMove();
        if (!move) return;

        const movingPiece = board[move.fromRow][move.fromCol];
        const movingType = movingPiece.toLowerCase();
        const movingColor = getPieceColor(movingPiece);
        lastMovedPieceType = movingType;
        lastMove = { from: { row: move.fromRow, col: move.fromCol }, to: { row: move.toRow, col: move.toCol } };

        let capturedPiece = board[move.toRow][move.toCol];
        if (movingType === 'p' && enPassantTarget && move.toRow === enPassantTarget.row && move.toCol === enPassantTarget.col) {
            capturedPiece = movingColor === 'white' ? board[move.toRow + 1][move.toCol] : board[move.toRow - 1][move.toCol];
        }
        if (capturedPiece) recordCapture(capturedPiece, movingColor);

        const aiCaptured = !!capturedPiece;
        const aiCastled = movingType === 'k' && Math.abs(move.toCol - move.fromCol) === 2;

        if (movingType === 'p' && (move.toRow === 0 || move.toRow === 7)) {
            board[move.toRow][move.toCol] = movingColor === 'white' ? 'Q' : 'q';
            board[move.fromRow][move.fromCol] = '';
        } else if (movingType === 'k') {
            if (move.fromCol === 4 && move.toCol === 6) {
                castlingRights[movingColor].kingSide = false;
                castlingRights[movingColor].queenSide = false;
                board[move.toRow][3] = board[move.toRow][7];
                board[move.toRow][7] = '';
            } else if (move.fromCol === 4 && move.toCol === 2) {
                castlingRights[movingColor].kingSide = false;
                castlingRights[movingColor].queenSide = false;
                board[move.toRow][5] = board[move.toRow][0];
                board[move.toRow][0] = '';
            } else {
                castlingRights[movingColor].kingSide = false;
                castlingRights[movingColor].queenSide = false;
            }
            board[move.toRow][move.toCol] = board[move.fromRow][move.fromCol];
            board[move.fromRow][move.fromCol] = '';
        } else if (movingType === 'r') {
            if (move.fromRow === (movingColor === 'white' ? 7 : 0) && move.fromCol === 0) {
                castlingRights[movingColor].queenSide = false;
            } else if (move.fromRow === (movingColor === 'white' ? 7 : 0) && move.fromCol === 7) {
                castlingRights[movingColor].kingSide = false;
            }
            board[move.toRow][move.toCol] = board[move.fromRow][move.fromCol];
            board[move.fromRow][move.fromCol] = '';
        } else if (movingType === 'p' && enPassantTarget && move.toRow === enPassantTarget.row && move.toCol === enPassantTarget.col) {
            if (movingColor === 'white') {
                board[move.toRow + 1][move.toCol] = '';
            } else {
                board[move.toRow - 1][move.toCol] = '';
            }
            board[move.toRow][move.toCol] = board[move.fromRow][move.fromCol];
            board[move.fromRow][move.fromCol] = '';
        } else {
            board[move.toRow][move.toCol] = board[move.fromRow][move.fromCol];
            board[move.fromRow][move.fromCol] = '';
        }
        
        if (movingType === 'p' && Math.abs(move.toRow - move.fromRow) === 2) {
            enPassantTarget = { row: (move.fromRow + move.toRow) / 2, col: move.fromCol };
        } else {
            enPassantTarget = null;
        }
        
        playMoveSound({ pieceType: movingType, captured: aiCaptured, castled: aiCastled });

        currentPlayer = 'white';
        selectedSquare = null;
        validMoves = [];
        lastMovedPieceType = null;
        updateTimerDisplay();
        renderBoard();
        recordPosition();

        if (!gameOver && isInCheck(board, currentPlayer)) GameSounds.play('check');
    }

    function startSoloGame(diff) {
        stopOnlineGame();
        gameMode = '1p';
        friendOpponentName = null;
        startGame(diff);
    }

    function startTwoPlayerGame() {
        stopOnlineGame();
        gameMode = '2p';
        friendOpponentName = null;
        startGame();
    }

    async function chessFetch(url, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getChessCsrf(),
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        };

        let res = await fetch(url, {
            ...options,
            headers,
            credentials: 'same-origin',
        });

        if (res.status === 419 && !options._retried) {
            headers['X-CSRF-TOKEN'] = getChessCsrf();
            res = await fetch(url, { ...options, headers, credentials: 'same-origin', _retried: true });
        }

        return res;
    }

    function updateRematchVisibility() {
        const show = gameMode !== 'online';
        const rematchBtn = document.getElementById('rematchBtn');
        const gameOverRematchBtn = document.getElementById('gameOverRematchBtn');
        if (rematchBtn) rematchBtn.style.display = show ? '' : 'none';
        if (gameOverRematchBtn) gameOverRematchBtn.style.display = show ? '' : 'none';
        document.getElementById('hintBtn').style.display = gameMode === '1p' ? '' : 'none';
    }

    async function loadPendingInvites() {
        if (!chessLoggedIn) return;
        try {
            const res = await chessFetch('{{ route('chess.games.pending') }}');
            if (res.ok) pendingInvites = await res.json();
        } catch (e) {
            pendingInvites = { incoming: [], outgoing: [], active: [] };
        }
    }

    function findInviteForFriend(friendId) {
        const all = [...(pendingInvites.incoming || []), ...(pendingInvites.outgoing || []), ...(pendingInvites.active || [])];
        return all.find(g => g.white.id === friendId || g.black.id === friendId);
    }

    async function initFriendsList() {
        if (!chessLoggedIn) return;
        await loadPendingInvites();
    }

    async function createChessRoom() {
        if (!chessLoggedIn) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        const btn = document.getElementById('createRoomBtn');
        if (btn?.disabled) return;
        const prevLabel = btn?.textContent || 'Create Room';
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Creating room…';
        }
        try {
            const res = await chessFetch('{{ route('chess.rooms.store') }}', { method: 'POST', body: '{}' });
            let data = {};
            try { data = await res.json(); } catch (e) { /* ignore */ }
            if (!res.ok) {
                showChessToast(data.message || `Could not create room (${res.status}).`, true);
                return;
            }
            createdRoomData = data;
            enterCreatedRoom();
        } catch (e) {
            showChessToast('Could not create room. Check your connection and try again.', true);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.textContent = prevLabel;
            }
        }
    }

    async function joinChessRoom() {
        if (!chessLoggedIn) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        const code = (document.getElementById('roomCodeInput')?.value || '').trim().toUpperCase();
        if (code.length !== 6) {
            showChessToast('Enter a 6-character room code.', true);
            return;
        }
        try {
            const res = await chessFetch('{{ route('chess.rooms.join') }}', {
                method: 'POST',
                body: JSON.stringify({ room_code: code }),
            });
            let data = {};
            try { data = await res.json(); } catch (e) { /* ignore */ }
            if (!res.ok) {
                showChessToast(data.message || 'Could not join room.', true);
                return;
            }
            closeChessRoomModal();
            friendOpponentName = data.white?.name || 'Friend';
            history.replaceState({}, '', data.play_url || ('/chess?game=' + data.token));
            enterOnlineGameUI(data);
            showChessToast(`Joined ${friendOpponentName}'s room! You play as Black.`);
            document.getElementById('gameArea')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (e) {
            showChessToast('Could not join room. Check the code and try again.', true);
        }
    }

    function enterCreatedRoom() {
        if (!createdRoomData) return;
        closeChessRoomModal();
        friendOpponentName = 'Friend';
        history.replaceState({}, '', createdRoomData.play_url || ('/chess?game=' + createdRoomData.token));
        enterOnlineGameUI(createdRoomData);
        showChessToast(`Room ready! Share code ${createdRoomData.room_code} with your friend.`);
        document.getElementById('gameArea')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function updateWaitingOverlay(gameData = null) {
        const overlay = document.getElementById('chessWaitingOverlay');
        const codeEl = document.getElementById('chessWaitingCode');
        const pedestal = document.getElementById('boardPedestal');
        if (!overlay || !pedestal) return;

        const status = gameData?.status ?? onlineGame?.status;
        const roomCode = gameData?.room_code ?? onlineGame?.roomCode;
        const waiting = gameMode === 'online' && status === 'pending' && myColor === 'white' && roomCode;

        if (waiting) {
            if (codeEl) codeEl.textContent = roomCode;
            overlay.classList.add('show');
            pedestal.classList.add('waiting-for-opponent');
        } else {
            overlay.classList.remove('show');
            pedestal.classList.remove('waiting-for-opponent');
        }
    }

    function copyRoomCode() {
        const code = createdRoomData?.room_code || document.getElementById('roomCodeDisplay')?.textContent?.trim();
        if (!code || code === '------') return;
        navigator.clipboard?.writeText(code).then(() => {
            showChessToast('Room code copied!');
        }).catch(() => alert('Room code: ' + code));
    }

    function openChessRoomModal() {
        if (!chessLoggedIn) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        createdRoomData = null;
        document.getElementById('roomCodeInput').value = '';
        document.getElementById('roomCodeDisplay').textContent = '------';
        showRoomView('roomChoiceView');
        document.getElementById('chessRoomModal')?.classList.add('show');
    }

    function closeChessRoomModal() {
        document.getElementById('chessRoomModal')?.classList.remove('show');
    }

    function showRoomView(viewId) {
        document.querySelectorAll('.chess-room-view').forEach(v => v.classList.remove('active'));
        document.getElementById(viewId)?.classList.add('active');
    }

    function showEnterCodeView() {
        showRoomView('roomJoinView');
        document.getElementById('roomCodeInput')?.focus();
    }

    let createdRoomData = null;

    async function openOnlineGame(token) {
        if (!chessLoggedIn) {
            window.location.href = '{{ route('login') }}?redirect=' + encodeURIComponent('/chess?game=' + token);
            return;
        }
        try {
            const res = await chessFetch(`/chess/games/${token}`);
            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Could not open game.');
                return;
            }
            history.replaceState({}, '', '/chess?game=' + token);
            enterOnlineGameUI(data);
        } catch (e) {
            alert('Could not open game.');
        }
    }

    function enterOnlineGameUI(gameData) {
        stopOnlineGame(false);
        gameMode = 'online';
        myColor = gameData.my_color;
        friendOpponentName = gameData.opponent?.name || 'Friend';
        onlineGame = {
            token: gameData.token,
            roomCode: gameData.room_code || null,
            version: gameData.version || 0,
            status: gameData.status,
            lastMessageId: 0,
            playUrl: gameData.play_url,
        };

        GameSounds.init();
        document.getElementById('playerSelect').style.display = 'none';
        document.getElementById('gameArea').classList.add('active', 'online-friends');
        updateRematchVisibility();
        updatePlayerLabelsOnline(gameData);
        updateBoardOrientation();
        updateOnlineInviteBar(gameData);

        if (gameData.status === 'active' && gameData.state) {
            applyingRemote = true;
            applyOnlineState(gameData);
            applyingRemote = false;
            resetHistory();
        } else {
            initBoard();
            renderBoard();
            resetHistory();
            document.getElementById('status').textContent = gameData.status === 'pending'
                ? (myColor === 'white'
                    ? (gameData.room_code
                        ? `Waiting for friend… Share code: ${gameData.room_code}`
                        : `Waiting for ${friendOpponentName} to join…`)
                    : 'Accept the invite to start.')
                : "White's turn";
        }

        updateWaitingOverlay(gameData);
        startOnlinePolling();
    }

    function updatePlayerLabelsOnline(gameData) {
        const whiteName = gameData.white?.name || 'White';
        const blackName = gameData.black?.name || 'Black';
        document.getElementById('white-role').textContent = whiteName + (myColor === 'white' ? ' (You)' : '');
        document.getElementById('black-role').textContent = blackName + (myColor === 'black' ? ' (You)' : '');
    }

    function updateBoardOrientation() {
        const frame = document.querySelector('.board-frame');
        if (!frame) return;
        if (myColor === 'black') frame.classList.add('board-flipped');
        else frame.classList.remove('board-flipped');
    }

    function updateOnlineInviteBar(gameData) {
        const bar = document.getElementById('onlineInviteBar');
        const text = document.getElementById('onlineInviteText');
        const acceptBtn = document.getElementById('acceptOnlineBtn');
        const copyBtn = document.getElementById('copyGameLinkBtn');
        if (!bar) return;

        if (gameData.status === 'pending') {
            bar.classList.add('show');
            if (myColor === 'black') {
                text.textContent = `${gameData.white.name} invited you to play as Black.`;
                acceptBtn.style.display = '';
                copyBtn.style.display = 'none';
            } else if (gameData.room_code) {
                text.textContent = `Room code: ${gameData.room_code} — share with your friend`;
                acceptBtn.style.display = 'none';
                copyBtn.style.display = '';
                copyBtn.textContent = 'Copy code';
            } else {
                text.textContent = `Share this link with ${friendOpponentName}:`;
                acceptBtn.style.display = 'none';
                copyBtn.style.display = '';
                copyBtn.textContent = 'Copy link';
            }
        } else {
            bar.classList.remove('show');
        }
    }

    function copyOnlineGameLink() {
        const code = onlineGame?.roomCode;
        if (code) {
            navigator.clipboard?.writeText(code).then(() => {
                GameSounds.play('click');
                document.getElementById('onlineInviteText').textContent = 'Code copied! Send it to your friend.';
            }).catch(() => alert('Room code: ' + code));
            return;
        }
        const url = onlineGame?.playUrl || window.location.href;
        navigator.clipboard?.writeText(url).then(() => {
            GameSounds.play('click');
            document.getElementById('onlineInviteText').textContent = 'Link copied! Send it to your friend.';
        }).catch(() => alert(url));
    }

    async function acceptOnlineInvite() {
        if (!onlineGame?.token) return;
        const res = await chessFetch(`/chess/games/${onlineGame.token}/accept`, { method: 'POST', body: '{}' });
        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Could not join.');
            return;
        }
        enterOnlineGameUI(data);
    }

    function applyOnlineState(gameData) {
        if (!gameData.state) return;
        const snap = {
            ...gameData.state,
            gameOver: gameData.game_over,
            gameOverInfo: gameData.game_over && gameData.game_over_title ? {
                title: gameData.game_over_title,
                msg: gameData.game_over_msg,
            } : null,
        };
        board = cloneBoard(snap.board);
        currentPlayer = snap.currentPlayer;
        castlingRights = JSON.parse(JSON.stringify(snap.castlingRights));
        enPassantTarget = snap.enPassantTarget ? { ...snap.enPassantTarget } : null;
        capturedByWhite = [...(snap.capturedByWhite || [])];
        capturedByBlack = [...(snap.capturedByBlack || [])];
        lastMove = snap.lastMove;
        whiteTime = snap.whiteTime ?? 600;
        blackTime = snap.blackTime ?? 600;
        gameOver = snap.gameOver;
        selectedSquare = null;
        validMoves = [];
        updateCapturedDisplay();
        updateTimerDisplay();
        closeGameOver();
        renderBoard(true);
        if (snap.statusText) document.getElementById('status').textContent = snap.statusText;
        if (gameData.game_over && snap.gameOverInfo) {
            showGameOver(snap.gameOverInfo.title, snap.gameOverInfo.msg);
        }
        onlineGame.version = gameData.version;
        onlineGame.status = gameData.status;
    }

    function startOnlinePolling() {
        if (onlinePollTimer) clearInterval(onlinePollTimer);
        const pollMs = (window.matchMedia('(max-width: 860px)').matches || window.matchMedia('(pointer: coarse)').matches) ? 4000 : 2000;
        pollOnlineGame();
        onlinePollTimer = setInterval(() => {
            if (!document.hidden) pollOnlineGame();
        }, pollMs);
    }

    async function pollOnlineGame() {
        if (!onlineGame?.token || document.hidden) return;
        try {
            const res = await chessFetch(`/chess/games/${onlineGame.token}/sync?since_message=${onlineGame.lastMessageId}`);
            if (!res.ok) return;
            const data = await res.json();
            const game = data.game;

            const wasPending = onlineGame.status === 'pending';

            if (game.version > onlineGame.version || game.status !== onlineGame.status) {
                if (game.status === 'active' && game.state) {
                    applyingRemote = true;
                    applyOnlineState(game);
                    applyingRemote = false;
                    if (positionHistory.length <= 1) resetHistory();
                    else recordPosition();
                    if (wasPending) {
                        const joinedName = game.black?.name || 'Your friend';
                        showChessToast(`${joinedName} joined! Game started.`);
                        GameSounds.play('start');
                        document.getElementById('status').textContent = game.state?.statusText || "White's turn";
                    }
                } else if (game.status === 'pending') {
                    onlineGame.status = 'pending';
                    updateOnlineInviteBar(game);
                }
            }

            onlineGame.status = game.status;
            if (game.room_code) onlineGame.roomCode = game.room_code;
            updateWaitingOverlay(game);

            if (game.status === 'finished' && game.game_over && !gameOver) {
                applyingRemote = true;
                applyOnlineState(game);
                applyingRemote = false;
            }

            updatePlayerLabelsOnline(game);
            updateOnlineInviteBar(game);

            (data.messages || []).forEach(msg => appendChessChatMessage(msg));
        } catch (e) { /* ignore poll errors */ }
    }

    function appendChessChatMessage(msg) {
        const list = document.getElementById('chessChatList');
        if (!list || !msg?.id) return;
        if (msg.id <= (onlineGame?.lastMessageId || 0)) return;
        onlineGame.lastMessageId = msg.id;

        const el = document.createElement('div');
        const isSystem = msg.body.includes(' joined the match');
        el.className = 'chess-chat-msg ' + (isSystem ? 'system' : (msg.mine ? 'mine' : 'theirs'));
        if (isSystem) {
            el.textContent = msg.body;
        } else {
            el.innerHTML = `<strong>${msg.user_name}</strong>${escapeHtml(msg.body)}`;
        }
        list.appendChild(el);
        list.scrollTop = list.scrollHeight;
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    async function sendChessChat(event) {
        event.preventDefault();
        if (!onlineGame?.token) return false;
        const input = document.getElementById('chessChatInput');
        const text = input.value.trim();
        if (!text) return false;
        input.value = '';
        const res = await chessFetch(`/chess/games/${onlineGame.token}/chat`, {
            method: 'POST',
            body: JSON.stringify({ message: text }),
        });
        if (res.ok) {
            const data = await res.json();
            appendChessChatMessage(data.message);
        }
        return false;
    }

    async function syncMoveToServer() {
        if (!onlineGame?.token || applyingRemote || onlineGame.status !== 'active') return;
        const payload = {
            version: onlineGame.version,
            state: snapshotState(),
            game_over: gameOver,
            winner: lastWinner,
            game_over_title: lastGameOverInfo?.title || null,
            game_over_msg: lastGameOverInfo?.msg || null,
        };
        try {
            const res = await chessFetch(`/chess/games/${onlineGame.token}/move`, {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (res.ok) {
                onlineGame.version = data.version;
                onlineGame.status = data.status;
            } else if (res.status === 409 && data.game) {
                applyingRemote = true;
                applyOnlineState(data.game);
                applyingRemote = false;
            }
        } catch (e) { /* ignore */ }
    }

    function stopOnlineGame(clearUrl = true) {
        if (onlinePollTimer) {
            clearInterval(onlinePollTimer);
            onlinePollTimer = null;
        }
        onlineGame = null;
        myColor = null;
        lastWinner = null;
        document.getElementById('gameArea')?.classList.remove('online-friends');
        document.getElementById('onlineInviteBar')?.classList.remove('show');
        document.getElementById('chessChatList').innerHTML = '';
        updateWaitingOverlay();
        document.querySelector('.board-frame')?.classList.remove('board-flipped');
        if (clearUrl && window.location.search.includes('game=')) {
            history.replaceState({}, '', '/chess');
        }
    }

    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    function updateTimerDisplay() {
        const whiteText = formatTime(whiteTime);
        const blackText = formatTime(blackTime);

        document.getElementById('white-timer').textContent = whiteText;
        document.getElementById('black-timer').textContent = blackText;

        const whiteMobile = document.getElementById('white-timer-mobile');
        const blackMobile = document.getElementById('black-timer-mobile');
        if (whiteMobile) {
            whiteMobile.textContent = whiteText;
            whiteMobile.style.setProperty('color', '#ffffff', 'important');
        }
        if (blackMobile) {
            blackMobile.textContent = blackText;
            blackMobile.style.setProperty('color', '#ffffff', 'important');
        }

        const whiteTimerEl = document.getElementById('white-timer');
        const blackTimerEl = document.getElementById('black-timer');
        if (currentPlayer === 'white') {
            whiteTimerEl.classList.add('active');
            blackTimerEl.classList.remove('active');
            whiteMobile?.classList.add('active');
            blackMobile?.classList.remove('active');
        } else {
            blackTimerEl.classList.add('active');
            whiteTimerEl.classList.remove('active');
            blackMobile?.classList.add('active');
            whiteMobile?.classList.remove('active');
        }
    }

    function startTimer() {
        if (gameMode === 'online') return;
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            if (gameOver) {
                clearInterval(timerInterval);
                return;
            }
            if (currentPlayer === 'white') {
                whiteTime--;
                if (whiteTime <= 0) {
                    whiteTime = 0;
                    gameOver = true;
                    clearInterval(timerInterval);
                    document.getElementById('status').textContent = 'Time out! Black wins!';
                    GameSounds.play('gameOver');
                    showGameOver("Time's up!", 'Black wins on the clock.');
                }
            } else {
                blackTime--;
                if (blackTime <= 0) {
                    blackTime = 0;
                    gameOver = true;
                    clearInterval(timerInterval);
                    document.getElementById('status').textContent = 'Time out! White wins!';
                    GameSounds.play('gameOver');
                    showGameOver("Time's up!", 'White wins on the clock.');
                }
            }
            updateTimerDisplay();
        }, 1000);
    }

    function updatePlayerLabels() {
        const blackRole = document.getElementById('black-role');
        const whiteRole = document.getElementById('white-role');
        if (gameMode === 'online') return;
        if (gameMode === '1p') {
            blackRole.textContent = 'AI';
            whiteRole.textContent = 'You';
        } else {
            blackRole.textContent = 'Player 2';
            whiteRole.textContent = 'Player 1';
        }
    }

    function startGame(diff = null) {
        GameSounds.init();
        GameSounds.play('start');
        if (diff !== null) difficulty = diff;
        whiteTime = 600;
        blackTime = 600;
        initBoard();
        updatePlayerLabels();
        document.getElementById('playerSelect').style.display = 'none';
        document.getElementById('gameArea').classList.add('active');
        document.getElementById('gameArea').classList.remove('online-friends');
        renderBoard();
        resetHistory();
        updateTimerDisplay();
        startTimer();
        updateRematchVisibility();
        updateReviewLabel();
    }

    function showSelect() {
        if (timerInterval) clearInterval(timerInterval);
        closeGameOver();
        document.getElementById('promotionModal').classList.remove('show');
        pendingPromotion = null;
        reviewingHistory = false;
        stopOnlineGame();
        gameMode = '2p';
        document.getElementById('playerSelect').style.display = 'block';
        document.getElementById('gameArea').classList.remove('active');
        updateRematchVisibility();
        const label = document.getElementById('moveReviewLabel');
        if (label) {
            label.textContent = '';
            label.classList.remove('reviewing');
        }
        initFriendsList();
    }

    function rematchGame() {
        if (gameMode === 'online') return;
        GameSounds.play('click');
        closeGameOver();
        document.getElementById('promotionModal').classList.remove('show');
        pendingPromotion = null;
        reviewingHistory = false;
        if (timerInterval) clearInterval(timerInterval);
        const diff = gameMode === '1p' ? difficulty : null;
        startGame(diff);
    }

    document.addEventListener('DOMContentLoaded', async () => {
        updateRematchVisibility();
        await initFriendsList();

        const params = new URLSearchParams(window.location.search);
        const gameToken = params.get('game');
        if (gameToken && chessLoggedIn) {
            openOnlineGame(gameToken);
        }
    });
</script>
@endsection