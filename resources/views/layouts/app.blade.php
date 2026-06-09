<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#050510">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="Mini Games — free browser games at themaniak.online. Bowling, cards, chess, arcade & more.">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini Games') — themaniak.online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Rajdhani:wght@500;600&display=swap" media="(min-width: 861px)">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Rajdhani:wght@500;600&display=swap" rel="stylesheet" media="(min-width: 861px)">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4" defer></script>
    @endif
    @if (request()->is('chess', 'platformer', 'tic-tac-toe', 'whack-a-mole', 'board-game', 'uno'))
        <script src="{{ asset('js/game-sounds.js') }}" defer></script>
    @endif
    <link rel="stylesheet" href="{{ asset('css/mobile-games.css') }}?v=20260611">
    @stack('head')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rajdhani', Arial, sans-serif;
            background: linear-gradient(165deg, #050510 0%, #0c0c22 45%, #080818 100%);
            color: white;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .nav-bar {
            --nav-h: 76px;
            position: sticky;
            top: 0;
            z-index: 2000;
            height: var(--nav-h);
            overflow: visible;
            background: rgba(5, 8, 20, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 4px 30px rgba(0,0,0,0.35), 0 0 40px rgba(0,240,255,0.04);
        }

        .nav-bar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00f0ff, #ff2d6a, #ffd54a, transparent);
            opacity: 0.65;
        }

        .nav-inner {
            max-width: 1320px;
            height: 100%;
            margin: 0 auto;
            padding: 0 clamp(1rem, 3vw, 2rem);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: relative;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        .nav-logo-icon {
            font-size: 1.65rem;
            filter: drop-shadow(0 0 10px rgba(255,45,106,0.5));
            animation: logo-pulse 3s ease-in-out infinite;
        }

        @keyframes logo-pulse {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.08) rotate(-4deg); }
        }

        .nav-logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(1rem, 2.5vw, 1.35rem);
            font-weight: 800;
            letter-spacing: 0.06em;
            background: linear-gradient(90deg, #00f0ff, #fff, #ff2d6a);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: logo-shimmer 5s linear infinite;
        }

        @keyframes logo-shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .nav-toggle {
            display: none;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            font-size: 1.35rem;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: border-color 0.2s, background 0.2s;
        }

        .nav-toggle:hover {
            border-color: rgba(0,240,255,0.4);
            background: rgba(0,240,255,0.08);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: clamp(0.25rem, 1.5vw, 0.5rem);
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            border: 1px solid transparent;
            transition: all 0.25s ease;
            white-space: nowrap;
            background: none;
            cursor: pointer;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.07);
            border-color: rgba(255,255,255,0.12);
        }

        .nav-link.active {
            border-color: rgba(0,240,255,0.35);
            box-shadow: 0 0 16px rgba(0,240,255,0.12);
        }

        .nav-link--games .nav-chevron {
            font-size: 0.65rem;
            opacity: 0.6;
            transition: transform 0.25s;
        }

        .nav-dropdown-wrap.open .nav-chevron,
        .nav-dropdown-wrap:hover .nav-chevron {
            transform: rotate(180deg);
        }

        .nav-cta {
            margin-left: 0.35rem;
            padding: 0.5rem 1.15rem !important;
            background: linear-gradient(135deg, #ff2d6a, #a855f7) !important;
            border-color: rgba(255,255,255,0.15) !important;
            color: #fff !important;
            box-shadow: 0 4px 18px rgba(255,45,106,0.35);
        }

        .nav-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(255,45,106,0.45) !important;
            background: linear-gradient(135deg, #ff4080, #b86ef7) !important;
        }

        .nav-link--auth {
            border-color: rgba(0,240,255,0.18);
            color: rgba(255,255,255,0.85);
        }

        .nav-link--auth:hover {
            border-color: rgba(0,240,255,0.35);
            color: #fff;
        }

        .nav-link--signup {
            background: rgba(168,85,247,0.15) !important;
            border-color: rgba(168,85,247,0.35) !important;
            color: #fff !important;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.75rem 0.35rem 0.45rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .nav-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.72rem;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #00f0ff, #a855f7);
        }

        .nav-user-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .nav-user-online-dot {
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid rgba(8, 12, 28, 0.95);
            background: #22c55e;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
        }

        .nav-user-online-dot.is-offline {
            background: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        }

        .nav-user-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: rgba(255,255,255,0.9);
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .nav-logout-form {
            margin: 0;
        }

        .nav-logout-btn {
            padding: 0.45rem 0.85rem !important;
            font-size: 0.95rem !important;
        }

        /* Notifications */
        .nav-notify-btn {
            position: relative;
            font-size: 1.15rem;
            padding: 0.45rem 0.75rem !important;
        }

        .nav-notify-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: #ef4444;
            color: #fff;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.62rem;
            font-weight: 800;
            line-height: 18px;
            text-align: center;
            box-shadow: 0 0 12px rgba(239,68,68,0.6);
            animation: notify-pulse 2s ease-in-out infinite;
        }

        @keyframes notify-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }

        .nav-notify-dropdown {
            min-width: min(320px, 92vw);
            left: auto;
            right: 0;
            transform: none;
        }

        .nav-notify-panel {
            padding: 0.75rem;
        }

        .nav-notify-empty {
            padding: 1.25rem 0.75rem;
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.95rem;
        }

        .nav-notify-list {
            display: grid;
            gap: 0.35rem;
            margin-bottom: 0.65rem;
        }

        .nav-notify-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 0.75rem;
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .nav-notify-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.85rem;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #ff2d6a, #a855f7);
            flex-shrink: 0;
        }

        .nav-notify-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .nav-notify-name {
            font-weight: 700;
            color: #fff;
            font-size: 0.95rem;
        }

        .nav-notify-msg {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
        }

        .nav-notify-viewall {
            display: block;
            text-align: center;
            padding: 0.65rem;
            border-radius: 10px;
            background: rgba(0,240,255,0.08);
            border: 1px solid rgba(0,240,255,0.22);
            color: #67e8f9;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .nav-notify-viewall:hover {
            background: rgba(0,240,255,0.14);
            color: #fff;
        }

        #notificationsDropdown.open .nav-dropdown,
        #notificationsDropdown:hover .nav-dropdown {
            display: block;
        }

        /* Friend request toast */
        .friend-toast {
            position: fixed;
            top: calc(var(--nav-h, 76px) + 12px);
            right: clamp(0.75rem, 3vw, 1.5rem);
            z-index: 2500;
            max-width: min(420px, calc(100vw - 1.5rem));
            animation: toast-in 0.35s ease;
        }

        @keyframes toast-in {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .friend-toast-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 16px;
            background: rgba(8, 12, 28, 0.96);
            border: 1px solid rgba(255,45,106,0.35);
            box-shadow: 0 16px 50px rgba(0,0,0,0.45), 0 0 30px rgba(255,45,106,0.15);
            backdrop-filter: blur(14px);
        }

        .friend-toast-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .friend-toast-text {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .friend-toast-text strong {
            color: #fff;
            font-size: 0.95rem;
        }

        .friend-toast-text span {
            color: rgba(255,255,255,0.55);
            font-size: 0.88rem;
        }

        .friend-toast-btn {
            padding: 0.45rem 0.85rem;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff2d6a, #a855f7);
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .friend-toast-close {
            background: none;
            border: none;
            color: rgba(255,255,255,0.45);
            font-size: 1rem;
            cursor: pointer;
            padding: 0.25rem;
            flex-shrink: 0;
        }

        .friend-toast-close:hover {
            color: #fff;
        }

        .friend-toast.hidden {
            display: none;
        }

        /* Online / offline status */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .status-pill-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-pill--online {
            color: #bbf7d0;
            background: rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.35);
        }

        .status-pill--online .status-pill-dot {
            background: #22c55e;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
        }

        .status-pill--offline {
            color: #fecaca;
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.35);
        }

        .status-pill--offline .status-pill-dot {
            background: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        }

        .status-pill--self {
            font-size: 0.78rem;
        }

        .nav-status-wrap {
            display: flex;
            align-items: center;
        }

        .nav-dropdown-wrap {
            position: relative;
        }

        .nav-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            min-width: min(520px, 92vw);
            padding-top: 8px;
            z-index: 2100;
        }

        .nav-dropdown-panel {
            padding: 0.85rem;
            border-radius: 18px;
            background: rgba(8, 12, 28, 0.96);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.55), 0 0 40px rgba(0,240,255,0.06);
            animation: drop-in 0.22s ease;
        }

        @keyframes drop-in {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .nav-dropdown-wrap:hover .nav-dropdown,
        .nav-dropdown-wrap.open .nav-dropdown {
            display: block;
        }

        .nav-dropdown-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            padding: 0.15rem 0.5rem 0.65rem;
        }

        .nav-games-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.4rem;
        }

        .nav-game-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: 12px;
            text-decoration: none;
            color: rgba(255,255,255,0.85);
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .nav-game-link:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.1);
            transform: translateX(3px);
        }

        .nav-game-link.active {
            background: rgba(0,240,255,0.08);
            border-color: rgba(0,240,255,0.25);
        }

        .nav-game-icon {
            font-size: 1.4rem;
            width: 2rem;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-game-info {
            min-width: 0;
        }

        .nav-game-name {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #fff;
            line-height: 1.2;
        }

        .nav-game-tag {
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav-game-link--bowling:hover .nav-game-name { color: #f472b6; }
        .nav-game-link--runner:hover .nav-game-name { color: #67e8f9; }
        .nav-game-link--neon:hover .nav-game-name { color: #ff2d6a; }
        .nav-game-link--snl:hover .nav-game-name { color: #4ade80; }
        .nav-game-link--chess:hover .nav-game-name { color: #d4a853; }
        .nav-game-link--mole:hover .nav-game-name { color: #fbbf24; }
    .nav-game-link--uno:hover .nav-game-name { color: #ef4444; }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Mobile nav */
        @media (max-width: 860px) {
            .nav-toggle {
                display: flex;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: var(--nav-h);
                left: 0;
                right: 0;
                flex-direction: column;
                align-items: stretch;
                gap: 0.35rem;
                padding: 1rem;
                background: rgba(5, 8, 20, 0.97);
                backdrop-filter: blur(16px);
                border-bottom: 1px solid rgba(255,255,255,0.08);
                box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            }

            .nav-menu.open {
                display: flex;
            }

            .nav-link {
                justify-content: center;
                padding: 0.65rem 1rem;
            }

            .nav-cta {
                margin-left: 0;
                text-align: center;
                justify-content: center;
            }

            .nav-dropdown-wrap {
                width: 100%;
            }

            .nav-dropdown {
                position: static;
                transform: none;
                display: none;
                min-width: 0;
                width: 100%;
                margin-top: 0;
                padding-top: 0;
            }

            .nav-dropdown-wrap.open .nav-dropdown {
                display: block;
            }

            .nav-games-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Mobile performance — blur/animations/fonts are costly on phones */
        @media (max-width: 860px), (pointer: coarse) {
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            .nav-bar,
            .nav-menu,
            .friend-toast,
            .chess-toast {
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
            }

            .nav-bar {
                background: rgba(5, 8, 20, 0.98);
            }

            .nav-logo-icon {
                animation: none;
            }

            .nav-logo-text {
                animation: none;
            }

            .nav-notify-badge {
                animation: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        #site-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: linear-gradient(165deg, #050510 0%, #0c0c22 45%, #080818 100%);
            color: #fff;
            font-family: 'Rajdhani', Arial, sans-serif;
            transition: opacity 0.25s ease;
        }

        #site-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #site-loader-icon {
            font-size: 2.5rem;
            animation: loader-pulse 1.2s ease-in-out infinite;
        }

        #site-loader-text {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.85);
        }

        @keyframes loader-pulse {
            0%, 100% { transform: scale(1); opacity: 0.85; }
            50% { transform: scale(1.08); opacity: 1; }
        }
    </style>
</head>
<body>
    <div id="site-loader" aria-live="polite" aria-busy="true">
        <span id="site-loader-icon">🎮</span>
        <span id="site-loader-text">Loading Mini Games…</span>
    </div>
    <script>
        (function () {
            const hide = () => {
                const el = document.getElementById('site-loader');
                if (!el) return;
                el.classList.add('hidden');
                el.setAttribute('aria-busy', 'false');
                setTimeout(() => el.remove(), 300);
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hide, { once: true });
            } else {
                hide();
            }
        })();
    </script>
    @php
        $games = [
            ['url' => '/galaxy-bowling', 'icon' => '🎳', 'name' => 'Galaxy Bowling', 'tag' => 'Sports', 'class' => 'bowling', 'path' => 'galaxy-bowling'],
            ['url' => '/platformer', 'icon' => '🎮', 'name' => 'Sky Runner', 'tag' => 'Action', 'class' => 'runner', 'path' => 'platformer'],
            ['url' => '/tic-tac-toe', 'icon' => '✕○', 'name' => 'Neon Grid', 'tag' => 'Puzzle', 'class' => 'neon', 'path' => 'tic-tac-toe'],
            ['url' => '/board-game', 'icon' => '🎲', 'name' => 'Snakes & Ladders', 'tag' => 'Board', 'class' => 'snl', 'path' => 'board-game'],
            ['url' => '/chess', 'icon' => '♟️', 'name' => 'Royal Chess', 'tag' => 'Strategy', 'class' => 'chess', 'path' => 'chess'],
            ['url' => '/whack-a-mole', 'icon' => '🔨', 'name' => 'Mole Mayhem', 'tag' => 'Arcade', 'class' => 'mole', 'path' => 'whack-a-mole'],
            ['url' => '/uno', 'icon' => '🃏', 'name' => 'Cosmic UNO', 'tag' => 'Cards', 'class' => 'uno', 'path' => 'uno'],
        ];
    @endphp

    <nav class="nav-bar">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-logo">
                <span class="nav-logo-icon">🎮</span>
                <span class="nav-logo-text">Mini Games<small style="display:block;font-size:0.55em;letter-spacing:0.12em;opacity:0.65;font-weight:600;">themaniak.online</small></span>
            </a>

            <button class="nav-toggle" id="navToggle" type="button" aria-label="Toggle menu">☰</button>

            <ul class="nav-menu" id="navMenu">
                <li>
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">🏠 Home</a>
                </li>
                <li class="nav-dropdown-wrap" id="gamesDropdown">
                    <button type="button" class="nav-link nav-link--games" id="gamesToggle">
                        🕹️ Games <span class="nav-chevron">▼</span>
                    </button>
                    <div class="nav-dropdown">
                        <div class="nav-dropdown-panel">
                            <p class="nav-dropdown-title">Pick a game</p>
                            <div class="nav-games-grid">
                                @foreach ($games as $game)
                                <a href="{{ url($game['url']) }}"
                                   class="nav-game-link nav-game-link--{{ $game['class'] }} {{ request()->is($game['path']) ? 'active' : '' }}">
                                    <span class="nav-game-icon">{{ $game['icon'] }}</span>
                                    <span class="nav-game-info">
                                        <span class="nav-game-name">{{ $game['name'] }}</span>
                                        <span class="nav-game-tag">{{ $game['tag'] }}</span>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </li>
                @auth
                @if (!auth()->user()->isOwner())
                <li>
                    <a href="{{ route('feedback.create') }}" class="nav-link {{ request()->routeIs('feedback.create') || request()->routeIs('feedback.store') ? 'active' : '' }}">💬 Feedback</a>
                </li>
                @endif
                @else
                <li>
                    <a href="{{ route('feedback.create') }}" class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}">💬 Feedback</a>
                </li>
                @endauth
                @auth
                @include('layouts.partials.friend-notifications')
                @if (auth()->user()->isOwner())
                <li>
                    <a href="{{ route('owner.index') }}" class="nav-link {{ request()->routeIs('owner.*') ? 'active' : '' }}">
                        👑 Owner
                        @if (($unreadFeedbackCount ?? 0) > 0)
                            <span class="nav-notify-badge" style="position:static;display:inline-block;min-width:16px;height:16px;line-height:16px;font-size:0.58rem;margin-left:0.25rem;">{{ ($unreadFeedbackCount ?? 0) > 9 ? '9+' : ($unreadFeedbackCount ?? 0) }}</span>
                        @endif
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('friends.index') }}" class="nav-link {{ request()->routeIs('friends.*') ? 'active' : '' }}">👥 Friends</a>
                </li>
                <li>
                    <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        💬 Chat
                        @if (($chatUnreadCount ?? 0) > 0)
                            <span class="nav-notify-badge" style="position:static;display:inline-block;min-width:16px;height:16px;line-height:16px;font-size:0.58rem;margin-left:0.25rem;">{{ ($chatUnreadCount ?? 0) > 9 ? '9+' : ($chatUnreadCount ?? 0) }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <div class="nav-user" title="{{ auth()->user()->email }}">
                        <span class="nav-user-avatar-wrap">
                            <span class="nav-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span
                                class="nav-user-online-dot {{ auth()->user()->isOnline() ? 'is-online' : 'is-offline' }}"
                                id="navUserOnlineDot"
                                title="{{ auth()->user()->isOnline() ? 'Online' : 'Offline' }}"
                            ></span>
                        </span>
                        <span class="nav-user-name">{{ auth()->user()->name }}</span>
                    </div>
                </li>
                <li>
                    <a href="{{ route('logout') }}" class="nav-link nav-logout-btn">Logout</a>
                </li>
                @else
                <li>
                    <a href="{{ route('login') }}" class="nav-link nav-link--auth {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                </li>
                <li>
                    <a href="{{ route('register') }}" class="nav-link nav-link--signup {{ request()->routeIs('register') ? 'active' : '' }}">Sign Up</a>
                </li>
                @endauth
            </ul>
        </div>
    </nav>

    @auth
        @include('layouts.partials.friend-request-toast')
        @include('layouts.partials.chess-invite-toast')
        @if (auth()->user()->isOwner())
            @include('layouts.partials.feedback-toast')
        @endif
    @endauth

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');
            const gamesToggle = document.getElementById('gamesToggle');
            const gamesDropdown = document.getElementById('gamesDropdown');
            const notificationsToggle = document.getElementById('notificationsToggle');
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            const friendRequestToast = document.getElementById('friendRequestToast');
            const friendRequestToastClose = document.getElementById('friendRequestToastClose');
            const friendRequestCount = {{ $friendRequestCount ?? 0 }};
            const chessInviteToast = document.getElementById('chessInviteToast');
            const chessInviteToastClose = document.getElementById('chessInviteToastClose');
            const chessInviteCount = {{ $chessInviteCount ?? 0 }};
            const feedbackToast = document.getElementById('feedbackToast');
            const feedbackToastClose = document.getElementById('feedbackToastClose');
            const unreadFeedbackCount = {{ $unreadFeedbackCount ?? 0 }};

            if (feedbackToast && unreadFeedbackCount > 0) {
                const fbStorageKey = 'feedbackNotifyDismissedCount';
                const fbDismissed = sessionStorage.getItem(fbStorageKey);
                if (fbDismissed === String(unreadFeedbackCount)) {
                    feedbackToast.classList.add('hidden');
                }
                feedbackToastClose?.addEventListener('click', () => {
                    feedbackToast.classList.add('hidden');
                    sessionStorage.setItem(fbStorageKey, String(unreadFeedbackCount));
                });
            }

            navToggle?.addEventListener('click', (e) => {
                e.stopPropagation();
                navMenu.classList.toggle('open');
                navToggle.textContent = navMenu.classList.contains('open') ? '✕' : '☰';
            });

            gamesToggle?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                gamesDropdown.classList.toggle('open');
                notificationsDropdown?.classList.remove('open');
            });

            notificationsToggle?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                notificationsDropdown?.classList.toggle('open');
                gamesDropdown?.classList.remove('open');
            });

            if (friendRequestToast && friendRequestCount > 0) {
                const storageKey = 'friendNotifyDismissedCount';
                const dismissed = sessionStorage.getItem(storageKey);
                if (dismissed === String(friendRequestCount)) {
                    friendRequestToast.classList.add('hidden');
                }
                friendRequestToastClose?.addEventListener('click', () => {
                    friendRequestToast.classList.add('hidden');
                    sessionStorage.setItem(storageKey, String(friendRequestCount));
                });
            }

            if (chessInviteToast && chessInviteCount > 0) {
                const chessStorageKey = 'chessNotifyDismissedCount';
                const chessDismissed = sessionStorage.getItem(chessStorageKey);
                if (chessDismissed === String(chessInviteCount)) {
                    chessInviteToast.classList.add('hidden');
                }
                chessInviteToastClose?.addEventListener('click', () => {
                    chessInviteToast.classList.add('hidden');
                    sessionStorage.setItem(chessStorageKey, String(chessInviteCount));
                });
            }

            document.addEventListener('click', (e) => {
                if (gamesDropdown && !gamesDropdown.contains(e.target)) {
                    gamesDropdown.classList.remove('open');
                }
                if (notificationsDropdown && !notificationsDropdown.contains(e.target)) {
                    notificationsDropdown.classList.remove('open');
                }
                if (navMenu && navToggle && !navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                    navMenu.classList.remove('open');
                    navToggle.textContent = '☰';
                }
            });

            @auth
            const navUserOnlineDot = document.getElementById('navUserOnlineDot');
            const pingUrl = @json(route('presence.ping'));
            const csrf = @json(csrf_token());
            const isMobileDevice = window.matchMedia('(max-width: 860px)').matches || window.matchMedia('(pointer: coarse)').matches;
            const netInfo = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            const isSlowNetwork = !!(netInfo && (netInfo.saveData || ['slow-2g', '2g', '3g'].includes(netInfo.effectiveType)));

            function pollMs(desktopMs, mobileMs) {
                const base = isMobileDevice ? mobileMs : desktopMs;
                return Math.round(isSlowNetwork ? base * 1.5 : base);
            }

            function startPoll(fn, desktopMs, mobileMs) {
                let timer = null;
                const tick = () => {
                    if (!document.hidden && navigator.onLine) fn();
                };
                const arm = () => {
                    if (timer) clearInterval(timer);
                    timer = setInterval(tick, pollMs(desktopMs, mobileMs));
                };
                tick();
                arm();
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        if (timer) {
                            clearInterval(timer);
                            timer = null;
                        }
                    } else {
                        tick();
                        if (!timer) arm();
                    }
                });
            }

            function setSelfStatus(online) {
                if (!navUserOnlineDot) return;
                navUserOnlineDot.classList.toggle('is-online', online);
                navUserOnlineDot.classList.toggle('is-offline', !online);
                navUserOnlineDot.title = online ? 'Online' : 'Offline';
            }

            function updateSelfStatusFromNetwork() {
                setSelfStatus(navigator.onLine);
            }

            window.addEventListener('online', updateSelfStatusFromNetwork);
            window.addEventListener('offline', updateSelfStatusFromNetwork);

            async function pingPresence() {
                if (!navigator.onLine || document.hidden) return;
                try {
                    await fetch(pingUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    setSelfStatus(true);
                } catch {
                    /* keep last known state */
                }
            }

            updateSelfStatusFromNetwork();
            pingPresence();
            startPoll(pingPresence, 15000, 45000);

            const friendsPresenceUrl = @json(route('presence.friends'));
            const chessInviteCheckUrl = @json(route('chess.games.invites.check'));
            const friendRequestCountLive = {{ $friendRequestCount ?? 0 }};
            let lastKnownChessInviteCount = {{ $chessInviteCount ?? 0 }};
            let lastKnownChessInviteToken = @json(($chessInviteNotifications ?? collect())->first()?->token);
            let liveFeedbackCount = {{ auth()->user()->isOwner() ? ($unreadFeedbackCount ?? 0) : 0 }};

            function updateFriendPresenceOnPage(friends) {
                (friends || []).forEach(f => {
                    document.querySelectorAll(`[data-friend-id="${f.id}"]`).forEach(card => {
                        const dot = card.querySelector('.friend-online-dot, .friend-offline-dot');
                        const label = card.querySelector('.friend-pick-meta small');
                        if (dot) {
                            dot.classList.toggle('friend-online-dot', f.online);
                            dot.classList.toggle('friend-offline-dot', !f.online);
                        }
                        if (label) label.textContent = f.online ? 'Online' : 'Offline';
                    });
                    document.querySelectorAll(`.status-pill[data-user-id="${f.id}"]`).forEach(pill => {
                        pill.classList.toggle('status-pill--online', f.online);
                        pill.classList.toggle('status-pill--offline', !f.online);
                        const lbl = pill.querySelector('.status-pill-label');
                        if (lbl) lbl.textContent = f.online ? 'Online' : 'Offline';
                        pill.title = `${f.name} is ${f.online ? 'online' : 'offline'}`;
                    });
                });
            }

            async function pollFriendsPresence() {
                if (!navigator.onLine || document.hidden) return;
                try {
                    const res = await fetch(friendsPresenceUrl, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    updateFriendPresenceOnPage(data.friends || []);
                } catch {
                    /* ignore */
                }
            }

            startPoll(pollFriendsPresence, 8000, 30000);

            function updateNavNotifyBadge(total) {
                const badge = document.getElementById('navNotifyBadge');
                if (!badge) return;
                if (total > 0) {
                    badge.textContent = total > 9 ? '9+' : String(total);
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            function refreshNotifyBadge(chessCount = lastKnownChessInviteCount) {
                updateNavNotifyBadge(friendRequestCountLive + chessCount + liveFeedbackCount);
            }

            function showLiveChessInviteToast(fromName, playUrl) {
                let toast = document.getElementById('chessLiveToast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'chessLiveToast';
                    toast.className = 'friend-toast chess-toast';
                    toast.innerHTML = `
                        <div class="friend-toast-inner">
                            <span class="friend-toast-icon">♟</span>
                            <div class="friend-toast-text">
                                <strong>Chess invite!</strong>
                                <span id="chessLiveToastMsg"></span>
                            </div>
                            <a href="#" class="friend-toast-btn" id="chessLiveToastLink">Join now</a>
                            <button type="button" class="friend-toast-close" id="chessLiveToastClose" aria-label="Dismiss">✕</button>
                        </div>`;
                    document.body.appendChild(toast);
                    toast.querySelector('#chessLiveToastClose')?.addEventListener('click', () => toast.classList.add('hidden'));
                }
                const msg = toast.querySelector('#chessLiveToastMsg');
                if (msg) msg.textContent = `${fromName} invited you to Royal Chess.`;
                const link = toast.querySelector('#chessLiveToastLink');
                if (link) link.href = playUrl;
                toast.classList.remove('hidden');
                document.getElementById('chessInviteToast')?.classList.remove('hidden');
                if (typeof GameSounds !== 'undefined') {
                    try { GameSounds.init(); GameSounds.play('start'); } catch (e) { /* ignore */ }
                }
            }

            async function pollChessInvites() {
                if (!navigator.onLine || document.hidden) return;
                try {
                    const res = await fetch(chessInviteCheckUrl, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const count = data.count || 0;
                    const latest = (data.incoming || [])[0];
                    const latestToken = latest?.token || data.latest_token || null;
                    const isNewInvite = latestToken && latestToken !== lastKnownChessInviteToken;
                    if (isNewInvite && latest) {
                        showLiveChessInviteToast(latest.from_name, latest.play_url);
                    } else if (count > lastKnownChessInviteCount && latest) {
                        showLiveChessInviteToast(latest.from_name, latest.play_url);
                    }
                    lastKnownChessInviteCount = count;
                    if (latestToken) lastKnownChessInviteToken = latestToken;
                    refreshNotifyBadge(count);
                } catch {
                    /* ignore */
                }
            }

            refreshNotifyBadge();
            pollChessInvites();
            startPoll(pollChessInvites, 8000, 30000);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    pingPresence();
                    pollFriendsPresence();
                    pollChessInvites();
                }
            });

            @if (auth()->user()->isOwner())
            const feedbackCheckUrl = @json(route('owner.feedback.check'));
            const feedbackPageUrl = @json(route('owner.feedback'));
            let lastKnownFeedbackId = {{ ($feedbackNotifications ?? collect())->first()?->id ?? 0 }};

            function showLiveFeedbackToast(name, subject) {
                let toast = document.getElementById('feedbackLiveToast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'feedbackLiveToast';
                    toast.className = 'friend-toast';
                    toast.innerHTML = `
                        <div class="friend-toast-inner" style="border-color:rgba(255,213,74,0.35);">
                            <span class="friend-toast-icon">💬</span>
                            <div class="friend-toast-text">
                                <strong>New feedback!</strong>
                                <span id="feedbackLiveToastMsg"></span>
                            </div>
                            <a href="${feedbackPageUrl}" class="friend-toast-btn">View</a>
                            <button type="button" class="friend-toast-close" id="feedbackLiveToastClose" aria-label="Dismiss">✕</button>
                        </div>`;
                    document.body.appendChild(toast);
                    toast.querySelector('#feedbackLiveToastClose')?.addEventListener('click', () => toast.remove());
                }
                const msg = toast.querySelector('#feedbackLiveToastMsg');
                if (msg) {
                    msg.textContent = subject ? `${name}: ${subject}` : `${name} sent new feedback.`;
                }
                toast.classList.remove('hidden');
            }

            async function pollFeedback() {
                if (!navigator.onLine) return;
                try {
                    const res = await fetch(feedbackCheckUrl, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (data.latest_id && data.latest_id > lastKnownFeedbackId) {
                        showLiveFeedbackToast(data.latest_name || 'Someone', data.latest_subject);
                        lastKnownFeedbackId = data.latest_id;
                    }
                    liveFeedbackCount = data.unread_count || 0;
                    refreshNotifyBadge();
                } catch {
                    /* ignore */
                }
            }

            pollFeedback();
            startPoll(pollFeedback, 30000, 90000);
            @endif
            @endauth
        });
    </script>
    @stack('scripts')
</body>
</html>
