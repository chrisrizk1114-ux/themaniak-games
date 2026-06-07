@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@400;600;700;800&display=swap');

    .main-content:has(.f400-page) { max-width: none; padding: 0; width: 100%; overflow: hidden; }
    body:has(.f400-page) { overflow: hidden; }

    .f400-page {
        --felt: #1a6b42;
        --felt-dark: #0f4a2d;
        --gold: #ffd54a;
        --gold-dim: #b8860b;
        --red: #ef4444;
        --heart: #dc2626;
        --nav-h: 76px;
        font-family: 'Outfit', sans-serif;
        height: calc(100svh - var(--nav-h));
        min-height: calc(100svh - var(--nav-h));
        background:
            radial-gradient(ellipse at 50% 30%, rgba(220,38,38,0.14) 0%, transparent 45%),
            radial-gradient(ellipse at 50% 40%, rgba(74,222,128,0.1) 0%, transparent 55%),
            radial-gradient(ellipse at 20% 80%, rgba(0,0,0,0.3) 0%, transparent 50%),
            linear-gradient(165deg, #0a2e1c 0%, var(--felt-dark) 25%, var(--felt) 55%, #145a38 100%);
        position: relative;
        overflow: hidden;
    }

    .f400-page::after {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .f400-wrap {
        position: relative;
        z-index: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 0.4rem 0.75rem 0.6rem;
    }

    .f400-hud {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .f400-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(1.5rem, 3.5vw, 2.2rem);
        letter-spacing: 0.14em;
        color: var(--gold);
        text-shadow: 0 2px 8px rgba(0,0,0,0.4);
    }

    .f400-status {
        flex: 1;
        text-align: center;
        font-weight: 700;
        font-size: clamp(0.85rem, 1.8vw, 1rem);
        color: rgba(255,255,255,0.9);
        min-width: 140px;
    }

    .f400-btns { display: flex; gap: 0.35rem; flex-wrap: wrap; }

    .f400-btn {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.78rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        border: 2px solid rgba(255,255,255,0.2);
        background: rgba(0,0,0,0.35);
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }

    .f400-btn:hover { border-color: rgba(255,213,74,0.5); transform: translateY(-1px); }
    .f400-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .f400-btn--gold { background: linear-gradient(135deg, #ca8a04, #eab308); border-color: rgba(255,213,74,0.5); color: #1a1a1a; }
    .f400-btn--bid { background: linear-gradient(135deg, #dc2626, #ef4444); border-color: rgba(239,68,68,0.5); }
    .f400-btn--rules {
        background: linear-gradient(135deg, #7f1d1d, #b91c1c);
        border-color: rgba(255,213,74,0.55);
        color: #fff;
        box-shadow: 0 0 14px rgba(220,38,38,0.35);
    }

    .f400-scoreboard {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        justify-content: center;
        flex-shrink: 0;
        margin: 0.25rem 0 0.35rem;
    }

    .f400-score-pill {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: rgba(0,0,0,0.45);
        border: 1px solid rgba(255,255,255,0.12);
        font-size: 0.72rem;
        font-weight: 700;
        color: rgba(255,255,255,0.85);
        letter-spacing: 0.04em;
    }

    .f400-score-pill--team { border-color: rgba(74,222,128,0.45); }
    .f400-score-pill--opp { border-color: rgba(239,68,68,0.4); }
    .f400-score-pill--contract { border-color: rgba(255,213,74,0.45); }
    .f400-score-pill .val { color: var(--gold); font-size: 0.95rem; font-weight: 800; }
    .f400-score-pill .val--heart { color: #fca5a5; }

    .f400-led-suit {
        position: absolute;
        bottom: 0.35rem;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.62rem;
        font-weight: 800;
        color: #fca5a5;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        text-align: center;
        max-width: 92%;
        line-height: 1.3;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        background: rgba(0,0,0,0.35);
    }

    .f400-table {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 2.2fr 1fr;
        grid-template-rows: minmax(80px, 1fr) minmax(120px, 1.4fr) minmax(130px, 1.2fr);
        grid-template-areas:
            ". north ."
            "west center east"
            ". south .";
        gap: 0.35rem;
        min-height: 0;
        align-items: center;
        justify-items: center;
    }

    .f400-seat { text-align: center; width: 100%; max-width: 220px; }
    .f400-seat--north { grid-area: north; }
    .f400-seat--west { grid-area: west; }
    .f400-seat--east { grid-area: east; }
    .f400-seat--south { grid-area: south; width: 100%; max-width: none; }

    .f400-avatar {
        width: clamp(44px, 8vw, 56px);
        height: clamp(44px, 8vw, 56px);
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.25);
        background: linear-gradient(145deg, #334155, #1e293b);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin: 0 auto 0.25rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
    }

    .f400-seat.active .f400-avatar {
        border-color: var(--gold);
        box-shadow: 0 0 20px rgba(255,213,74,0.45);
    }

    .f400-seat.partner .f400-avatar { border-color: rgba(74,222,128,0.6); }

    .f400-name {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 0.95rem;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.85);
    }

    .f400-bs {
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--red);
        letter-spacing: 0.06em;
        margin-top: 0.15rem;
    }

    .f400-bs span { color: rgba(255,255,255,0.5); font-weight: 600; }

    .f400-pile {
        display: flex;
        justify-content: center;
        gap: 2px;
        min-height: 52px;
        margin-top: 0.25rem;
    }

    .f400-card-back {
        width: 28px;
        height: 40px;
        border-radius: 4px;
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 1px 2px 4px rgba(0,0,0,0.3);
    }

    .f400-center {
        grid-area: center;
        width: 100%;
        max-width: 340px;
        aspect-ratio: 1;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background:
            radial-gradient(circle at 50% 45%, rgba(26,107,66,0.95) 0%, rgba(15,74,45,0.98) 55%, rgba(8,40,25,1) 100%);
        border: 4px solid rgba(255,213,74,0.35);
        box-shadow:
            inset 0 0 40px rgba(0,0,0,0.45),
            0 0 30px rgba(0,0,0,0.35),
            0 0 0 8px rgba(127,29,29,0.25);
    }

    .f400-trick {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .f400-trick-card {
        position: absolute;
        width: clamp(52px, 11vw, 68px);
        height: clamp(74px, 15vw, 96px);
        transform: translate(-50%, -50%);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .f400-trick-card.winner {
        z-index: 10;
        transform: translate(-50%, -50%) scale(1.12);
    }

    .f400-trick-card.winner .f400-card {
        border-color: var(--gold);
        box-shadow: 0 0 24px rgba(255,213,74,0.75), 0 8px 20px rgba(0,0,0,0.4);
    }

    .f400-trick-card.pos-north { top: 18%; left: 50%; }
    .f400-trick-card.pos-east { top: 50%; left: 82%; }
    .f400-trick-card.pos-south { top: 82%; left: 50%; }
    .f400-trick-card.pos-west { top: 50%; left: 18%; }

    .f400-trump-badge {
        position: absolute;
        top: -0.5rem;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        background: rgba(220,38,38,0.9);
        border: 1px solid rgba(255,255,255,0.3);
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #fff;
        white-space: nowrap;
    }

    .f400-hand {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.35rem;
        padding: 0.35rem 0;
        min-height: 90px;
    }

    .f400-card {
        width: clamp(48px, 10vw, 62px);
        height: clamp(68px, 14vw, 86px);
        border-radius: 7px;
        background: #fff;
        border: 2px solid #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.35);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        user-select: none;
        position: relative;
    }

    .f400-card:hover:not(.disabled) { transform: translateY(-8px); z-index: 5; }
    .f400-card.selected { transform: translateY(-14px); box-shadow: 0 8px 24px rgba(255,213,74,0.5); border-color: var(--gold); }
    .f400-card.disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
    .f400-card.playable { border-color: #4ade80; box-shadow: 0 0 12px rgba(74,222,128,0.35); cursor: pointer; }
    .f400-card.playable.selected { border-color: var(--gold); box-shadow: 0 8px 24px rgba(255,213,74,0.55); }

    .f400-card .rank { font-size: clamp(0.95rem, 2.2vw, 1.15rem); line-height: 1; }
    .f400-card .suit { font-size: clamp(1.1rem, 2.5vw, 1.35rem); line-height: 1; }

    .f400-card.s-H {
        color: #dc2626;
        background: linear-gradient(145deg, #fff 0%, #fff5f5 100%);
        border-color: rgba(220,38,38,0.45);
    }
    .f400-card.s-D { color: #dc2626; }
    .f400-card.s-C { color: #0f172a; }
    .f400-card.s-S { color: #0f172a; }

    .f400-card.boss::after {
        content: '★';
        position: absolute;
        top: 2px;
        right: 4px;
        font-size: 0.55rem;
        color: var(--gold);
    }

    .f400-card.trump-rank::before {
        content: attr(data-trump-rank);
        position: absolute;
        top: 2px;
        left: 4px;
        font-size: 0.5rem;
        font-weight: 800;
        color: var(--gold-dim);
        background: rgba(0,0,0,0.08);
        border-radius: 3px;
        padding: 0 3px;
        line-height: 1.2;
    }

    .f400-bid-panel {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem;
        background: rgba(0,0,0,0.35);
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        margin-top: 0.35rem;
    }

    .f400-bid-hint {
        margin: 0;
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(255,255,255,0.75);
        text-align: center;
    }

    .f400-bid-hint strong { color: var(--gold); }

    .f400-bid-btns {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: center;
    }

    .f400-bid-panel.hidden,
    .f400-play-bar.hidden,
    .hidden { display: none !important; }

    .f400-play-bar {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.35rem;
    }

    .f400-rules {
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(5,10,8,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .f400-rules.hidden { display: none; }

    .f400-rules-box {
        max-width: 440px;
        max-height: 85vh;
        overflow-y: auto;
        background: linear-gradient(145deg, #1a0a0a 0%, #145a38 40%, #0f4a2d 100%);
        border: 2px solid rgba(255,213,74,0.45);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        color: #fff;
        box-shadow: 0 20px 60px rgba(0,0,0,0.55);
    }

    .f400-rules-highlight {
        background: rgba(220,38,38,0.2);
        border: 1px solid rgba(252,165,165,0.35);
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.75rem;
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .f400-rules-highlight strong { color: #fca5a5; }

    .f400-rules-box h2 {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.6rem;
        letter-spacing: 0.1em;
        color: var(--gold);
        margin: 0 0 0.75rem;
    }

    .f400-rules-box ul { margin: 0; padding-left: 1.2rem; line-height: 1.55; font-size: 0.92rem; }
    .f400-rules-box li { margin-bottom: 0.45rem; }
    .f400-rules-box li strong { color: var(--gold); }

    .f400-toast {
        position: fixed;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.65rem 1.25rem;
        border-radius: 999px;
        background: rgba(0,0,0,0.8);
        border: 1px solid rgba(255,213,74,0.4);
        color: #fff;
        font-weight: 700;
        z-index: 50;
        animation: f400-pop 0.3s ease;
    }

    .f400-toast.hidden { display: none; }

    @keyframes f400-pop {
        from { opacity: 0; transform: translateX(-50%) translateY(10px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    @media (max-width: 768px) {
        .f400-wrap { padding: 0.25rem 0.35rem 0.4rem; }
        .f400-title { font-size: 1.25rem; }
        .f400-status { font-size: 0.72rem; min-width: 0; flex: 1 1 100%; order: 3; }
        .f400-hud { gap: 0.35rem; }
        .f400-btns { flex: 1; justify-content: flex-end; }
        .f400-btn { padding: 0.45rem 0.65rem; font-size: 0.7rem; min-height: 44px; }
        .f400-scoreboard { gap: 0.3rem; margin: 0.15rem 0; }
        .f400-score-pill { font-size: 0.62rem; padding: 0.28rem 0.55rem; }
        .f400-score-pill .val { font-size: 0.82rem; }
        .f400-table {
            grid-template-columns: 1fr 1.6fr 1fr;
            grid-template-rows: minmax(52px, 0.7fr) minmax(90px, 1.2fr) minmax(110px, 1fr);
            gap: 0.2rem;
        }
        .f400-seat { max-width: 100%; }
        .f400-avatar { width: 36px; height: 36px; font-size: 1.1rem; }
        .f400-name { font-size: 0.65rem; }
        .f400-bs { font-size: 0.58rem; }
        .f400-seat--west .f400-pile,
        .f400-seat--east .f400-pile { min-height: 36px; }
        .f400-center { max-width: 100%; border-width: 2px; }
        .f400-trump-badge { font-size: 0.55rem; padding: 0.15rem 0.45rem; white-space: normal; text-align: center; max-width: 95%; }
        .f400-led-suit { font-size: 0.52rem; bottom: 0.15rem; }
        .f400-trick-card { width: clamp(40px, 10vw, 52px); height: clamp(56px, 14vw, 72px); }
        .f400-hand {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            justify-content: flex-start;
            scroll-snap-type: x proximity;
            padding-bottom: 0.25rem;
            min-height: 82px;
        }
        .f400-hand .f400-card { scroll-snap-align: center; flex-shrink: 0; }
        .f400-card {
            width: clamp(44px, 12vw, 54px);
            height: clamp(62px, 17vw, 76px);
            min-width: 44px;
            min-height: 62px;
        }
        .f400-card:hover:not(.disabled) { transform: none; }
        .f400-card.selected { transform: translateY(-10px); }
        .f400-bid-btns { max-height: 120px; overflow-y: auto; }
        .f400-bid-hint { font-size: 0.75rem; }
        .f400-play-bar .f400-btn { min-width: 120px; min-height: 48px; }
        .f400-toast {
            bottom: max(0.75rem, env(safe-area-inset-bottom));
            max-width: 92vw;
            font-size: 0.78rem;
            text-align: center;
        }
    }

    @media (max-width: 600px) {
        .f400-card-back { width: 22px; height: 32px; }
        .f400-seat--west .f400-name,
        .f400-seat--east .f400-name { display: none; }
    }
</style>

<div class="f400-page">
    <div class="f400-wrap">
        <div class="f400-hud">
            <div class="f400-title">400 ♥</div>
            <div class="f400-status" id="statusText">Loading…</div>
            <div class="f400-btns">
                <button type="button" class="f400-btn" id="soundBtn">🔊</button>
                <button type="button" class="f400-btn f400-btn--rules" id="rulesBtn">♥ Rules</button>
                <button type="button" class="f400-btn" id="newGameBtn">New Game</button>
            </div>
        </div>

        <div class="f400-scoreboard">
            <div class="f400-score-pill f400-score-pill--team">
                <span>Your team tricks</span>
                <span class="val" id="teamTricksUs">0</span>
            </div>
            <div class="f400-score-pill f400-score-pill--opp">
                <span>Opponents tricks</span>
                <span class="val" id="teamTricksThem">0</span>
            </div>
            <div class="f400-score-pill f400-score-pill--contract hidden" id="contractPill">
                <span>Contract</span>
                <span class="val" id="contractText">—</span>
            </div>
            <div class="f400-score-pill">
                <span>Match score</span>
                <span class="val"><span id="matchUs">0</span> – <span id="matchThem">0</span></span>
            </div>
        </div>

        <div class="f400-table">
            <div class="f400-seat f400-seat--north partner" id="seat2">
                <div class="f400-avatar">🛡️</div>
                <div class="f400-name">Partner North</div>
                <div class="f400-bs">Tricks <span id="b2">0</span> / S <span id="sTeam">0</span></div>
                <div class="f400-pile" id="pile2"></div>
            </div>

            <div class="f400-seat f400-seat--west" id="seat1">
                <div class="f400-avatar">⚔️</div>
                <div class="f400-name">West</div>
                <div class="f400-bs">Tricks <span id="b1">0</span> / S <span id="sOpp">0</span></div>
                <div class="f400-pile" id="pile1"></div>
            </div>

            <div class="f400-center">
                <div class="f400-trump-badge">♥ HEARTS TRUMP — J♥ #1 · 9♥ #2</div>
                <div class="f400-led-suit hidden" id="ledSuitLabel"></div>
                <div class="f400-trick" id="trickArea"></div>
            </div>

            <div class="f400-seat f400-seat--east" id="seat3">
                <div class="f400-avatar">🎭</div>
                <div class="f400-name">East</div>
                <div class="f400-bs">Tricks <span id="b3">0</span> / S <span id="sOpp2">0</span></div>
                <div class="f400-pile" id="pile3"></div>
            </div>

            <div class="f400-seat f400-seat--south" id="seat0">
                <div class="f400-name">You (South) — Tricks <span id="b0">0</span> / Team S <span id="sTeam2">0</span></div>
                <div class="f400-bid-panel hidden" id="bidPanel">
                    <p class="f400-bid-hint" id="bidHint"></p>
                    <div class="f400-bid-btns" id="bidBtns"></div>
                </div>
                <div class="f400-play-bar hidden" id="playBar">
                    <button type="button" class="f400-btn f400-btn--gold" id="playBtn" disabled>Play card</button>
                </div>
                <div class="f400-hand" id="playerHand"></div>
            </div>
        </div>
    </div>

    <div class="f400-rules hidden" id="rulesOverlay">
        <div class="f400-rules-box">
            <h2>Lebanese 400 — Rules</h2>
            <div class="f400-rules-highlight">
                <strong>♥ Hearts always win</strong> — any heart beats any spade/diamond/club, even an Ace.
                When hearts are led, the <strong>highest heart</strong> wins — but <strong>J♥ is #1</strong> and <strong>9♥ is #2</strong> (they beat A♥!).
            </div>
            <ul>
                <li><strong>4 players</strong> — You + North vs West + East (partners sit across).</li>
                <li><strong>Trump order:</strong> J♥ 9♥ A♥ 10♥ K♥ Q♥ 8♥ 7♥ 6♥ 5♥ 4♥ 3♥ 2♥.</li>
                <li><strong>Other suits:</strong> A K Q J 10 9 8 7 6 5 4 3 2 — must follow the led suit if you can.</li>
                <li><strong>If you can’t follow</strong> — play any card (a heart trumps, or discard).</li>
                <li><strong>Trick count</strong> — goes to whoever wins the trick (you or your partner). Check <em>Your team tricks</em> at the top.</li>
                <li><strong>Bidding</strong> — opening bid from <strong>2</strong> to <strong>13</strong>, or pass. Each raise must beat the last bid.</li>
                <li><strong>Contract</strong> — winning bidder’s team must take at least that many tricks combined.</li>
                <li><strong>Card points</strong> (toward 400): A=11, 10=10, K=4, Q=3, J=2; last trick +10.</li>
                <li><strong>Bid bonus</strong> — make contract: +bid×10; fail: −bid×10.</li>
                <li><strong>First team to 400</strong> wins.</li>
            </ul>
            <button type="button" class="f400-btn f400-btn--gold" id="closeRules" style="margin-top:1rem;width:100%;">Got it!</button>
        </div>
    </div>

    <div class="f400-toast hidden" id="toast"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const SUITS = ['H', 'D', 'C', 'S'];
    const RANKS = ['2','3','4','5','6','7','8','9','10','J','Q','K','A'];
    const SUIT_SYM = { H: '♥', D: '♦', C: '♣', S: '♠' };
    const TRUMP = 'H';
    const TRUMP_ORDER = ['J','9','A','10','K','Q','8','7','6','5','4','3','2'];
    const NORMAL_ORDER = ['A','K','Q','J','10','9','8','7','6','5','4','3','2'];
    const CARD_PTS = { A: 11, '10': 10, K: 4, Q: 3, J: 2 };
    const TARGET = 400;
    const MIN_BID = 2;
    const MAX_BID = 13;
    const NAMES = ['You', 'West', 'Partner', 'East'];
    const POS_CLASS = ['pos-south', 'pos-west', 'pos-north', 'pos-east'];
    const TEAM = [0, 1, 0, 1]; // 0 = you+north, 1 = west+east

    const statusText = document.getElementById('statusText');
    const trickArea = document.getElementById('trickArea');
    const playerHand = document.getElementById('playerHand');
    const bidPanel = document.getElementById('bidPanel');
    const bidHint = document.getElementById('bidHint');
    const bidBtns = document.getElementById('bidBtns');
    const playBar = document.getElementById('playBar');
    const playBtn = document.getElementById('playBtn');
    const soundBtn = document.getElementById('soundBtn');
    const toastEl = document.getElementById('toast');
    const rulesOverlay = document.getElementById('rulesOverlay');
    const teamTricksUsEl = document.getElementById('teamTricksUs');
    const teamTricksThemEl = document.getElementById('teamTricksThem');
    const contractPill = document.getElementById('contractPill');
    const contractText = document.getElementById('contractText');
    const matchUsEl = document.getElementById('matchUs');
    const matchThemEl = document.getElementById('matchThem');
    const ledSuitLabel = document.getElementById('ledSuitLabel');

    const AI_THINK_MS = 1500;
    const TRICK_RESOLVE_MS = 1400;

    let hands = [[], [], [], []];
    let trick = [];
    let trickLeader = 0;
    let currentPlayer = 0;
    let phase = 'bidding';
    let highBid = 0;
    let highBidder = -1;
    let bids = [null, null, null, null];
    let bidTurn = 0;
    let passesInRow = 0;
    let tricksWon = [0, 0, 0, 0];
    let teamTricks = [0, 0];
    let teamScores = [0, 0];
    let teamCardPts = [0, 0];
    let tricksTakenThisHand = [0, 0, 0, 0];
    let ledSuit = null;
    let dealer = 0;
    let selectedCardId = null;
    let inputLocked = false;
    let aiTimer = null;

    function clearAiTimer() {
        if (aiTimer) {
            clearTimeout(aiTimer);
            aiTimer = null;
        }
    }

    function scheduleAi(fn) {
        clearAiTimer();
        inputLocked = true;
        if (phase === 'playing') {
            statusText.textContent = `${NAMES[currentPlayer]} thinking…`;
        } else if (phase === 'bidding') {
            statusText.textContent = `${NAMES[bidTurn]} thinking…`;
        }
        renderHand();
        renderBidPanel();
        aiTimer = setTimeout(() => {
            aiTimer = null;
            inputLocked = false;
            fn();
        }, AI_THINK_MS);
    }

    function shuffle(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    }

    function makeDeck() {
        const d = [];
        for (const s of SUITS) {
            for (const r of RANKS) {
                d.push({ suit: s, rank: r, id: r + s });
            }
        }
        return shuffle(d);
    }

    function isBoss(card) {
        return card.suit === TRUMP && (card.rank === 'J' || card.rank === '9');
    }

    function trumpRank(card) {
        if (card.suit !== TRUMP) return 0;
        const i = TRUMP_ORDER.indexOf(card.rank);
        return i >= 0 ? i + 1 : 0;
    }

    function cardLabel(card) {
        return card.rank + SUIT_SYM[card.suit];
    }

    function cardPower(card) {
        if (card.suit === TRUMP) {
            const i = TRUMP_ORDER.indexOf(card.rank);
            return i >= 0 ? 1000 - i : 0;
        }
        const i = NORMAL_ORDER.indexOf(card.rank);
        return i >= 0 ? 100 - i : 0;
    }

    function beats(a, b, lead) {
        const aT = a.suit === TRUMP;
        const bT = b.suit === TRUMP;
        if (aT && !bT) return true;
        if (!aT && bT) return false;
        if (aT && bT) return cardPower(a) > cardPower(b);
        if (a.suit === lead && b.suit !== lead) return true;
        if (a.suit !== lead && b.suit === lead) return false;
        if (a.suit === lead && b.suit === lead) return cardPower(a) > cardPower(b);
        return false;
    }

    function trickWinnerInfo() {
        if (!trick.length) return null;
        const lead = trick[0].card.suit;
        let best = 0;
        for (let i = 1; i < trick.length; i++) {
            if (beats(trick[i].card, trick[best].card, lead)) best = i;
        }
        return { index: best, player: trick[best].player, card: trick[best].card };
    }

    function trickWinner() {
        return trickWinnerInfo()?.player ?? 0;
    }

    function currentWinningCard() {
        if (!trick.length) return null;
        const lead = trick[0].card.suit;
        let best = trick[0];
        for (let i = 1; i < trick.length; i++) {
            if (beats(trick[i].card, best.card, lead)) best = trick[i];
        }
        return best.card;
    }

    function legalCards(player) {
        const hand = hands[player];
        if (!trick.length) return hand.slice();
        const lead = trick[0].card.suit;
        const has = hand.filter(c => c.suit === lead);
        if (has.length) return has;
        return hand.slice();
    }

    function cardPoints(card) {
        return CARD_PTS[card.rank] || 0;
    }

    function sfx(name) {
        GameSounds.init();
        GameSounds.play(name);
    }

    function playCardSound(card) {
        sfx(card.suit === TRUMP ? 'cardTrump' : 'cardPlay');
    }

    function showToast(msg) {
        toastEl.textContent = msg;
        toastEl.classList.remove('hidden');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(() => toastEl.classList.add('hidden'), 2800);
    }

    function renderCardEl(card, opts = {}) {
        const el = document.createElement('div');
        const rank = trumpRank(card);
        el.className = `f400-card s-${card.suit}${isBoss(card) ? ' boss' : ''}${rank ? ' trump-rank' : ''}${opts.playable ? ' playable' : ''}${opts.disabled ? ' disabled' : ''}${opts.selected ? ' selected' : ''}${opts.winner ? ' winner-card' : ''}`;
        if (rank) el.dataset.trumpRank = '#' + rank;
        el.innerHTML = `<span class="rank">${card.rank}</span><span class="suit">${SUIT_SYM[card.suit]}</span>`;
        el.dataset.id = card.id;
        return el;
    }

    function renderPiles() {
        for (let p = 1; p <= 3; p++) {
            const pile = document.getElementById('pile' + p);
            pile.innerHTML = '';
            const n = hands[p].length;
            const show = Math.min(n, 5);
            for (let i = 0; i < show; i++) {
                const b = document.createElement('div');
                b.className = 'f400-card-back';
                b.style.marginLeft = i ? '-12px' : '0';
                pile.appendChild(b);
            }
            if (n > 5) {
                const c = document.createElement('span');
                c.style.cssText = 'font-size:0.7rem;color:#fff;margin-left:4px;align-self:center;';
                c.textContent = n;
                pile.appendChild(c);
            }
        }
    }

    function renderTrick(highlightWinnerIdx = null) {
        trickArea.innerHTML = '';
        trick.forEach((t, idx) => {
            const wrap = document.createElement('div');
            wrap.className = `f400-trick-card ${POS_CLASS[t.player]}${highlightWinnerIdx === idx ? ' winner' : ''}`;
            wrap.appendChild(renderCardEl(t.card, { winner: highlightWinnerIdx === idx }));
            trickArea.appendChild(wrap);
        });
        updateLedSuitLabel();
    }

    function updateLedSuitLabel() {
        if (!trick.length) {
            ledSuitLabel.classList.add('hidden');
            ledSuitLabel.textContent = '';
            return;
        }
        const lead = trick[0].card;
        const sym = SUIT_SYM[lead.suit];
        if (lead.suit === TRUMP) {
            ledSuitLabel.textContent = `Led: ${sym} HEARTS — highest heart wins (J♥ beats A♥)`;
        } else {
            ledSuitLabel.textContent = `Led: ${sym} — follow suit or trump with ♥`;
        }
        ledSuitLabel.classList.remove('hidden');
    }

    function renderHand() {
        playerHand.innerHTML = '';
        const isYourTurn = phase === 'playing' && currentPlayer === 0 && !inputLocked;
        const legal = isYourTurn ? legalCards(0) : [];
        const legalIds = new Set(legal.map(c => c.id));

        if (isYourTurn) {
            playBar.classList.remove('hidden');
        } else {
            playBar.classList.add('hidden');
            selectedCardId = null;
        }

        hands[0].forEach(card => {
            const playable = isYourTurn && legalIds.has(card.id);
            const selected = selectedCardId === card.id;
            const el = renderCardEl(card, {
                playable,
                disabled: isYourTurn && !playable,
                selected,
            });
            if (playable) {
                el.addEventListener('click', () => selectCard(card.id));
            }
            playerHand.appendChild(el);
        });

        playBtn.disabled = !isYourTurn || !selectedCardId || !legalIds.has(selectedCardId);
    }

    function selectCard(cardId) {
        if (phase !== 'playing' || currentPlayer !== 0 || inputLocked) return;
        const legal = legalCards(0);
        if (!legal.find(c => c.id === cardId)) return;
        const wasSelected = selectedCardId === cardId;
        selectedCardId = wasSelected ? null : cardId;
        if (!wasSelected && selectedCardId) sfx('cardSelect');
        renderHand();
    }

    function playSelectedCard() {
        if (!selectedCardId || inputLocked) return;
        const card = hands[0].find(c => c.id === selectedCardId);
        if (!card) return;
        selectedCardId = null;
        sfx('click');
        playCard(0, card);
    }

    function updateBs() {
        for (let i = 0; i < 4; i++) {
            const bEl = document.getElementById('b' + i);
            if (bEl) bEl.textContent = tricksTakenThisHand[i];
        }
        document.getElementById('sTeam').textContent = teamScores[0];
        document.getElementById('sTeam2').textContent = teamScores[0];
        document.getElementById('sOpp').textContent = teamScores[1];
        document.getElementById('sOpp2').textContent = teamScores[1];
        teamTricksUsEl.textContent = teamTricks[0];
        teamTricksThemEl.textContent = teamTricks[1];
        matchUsEl.textContent = teamScores[0];
        matchThemEl.textContent = teamScores[1];
        if (phase === 'playing' || phase === 'handOver') {
            contractPill.classList.remove('hidden');
            const bidTeam = TEAM[highBidder];
            const teamName = bidTeam === 0 ? 'Your team' : 'Opponents';
            contractText.textContent = `${teamName} need ${highBid} — ${teamTricks[bidTeam]}/${highBid}`;
        } else {
            contractPill.classList.add('hidden');
        }
    }

    function setActiveSeat(p) {
        document.querySelectorAll('.f400-seat').forEach(s => s.classList.remove('active'));
        document.getElementById('seat' + p)?.classList.add('active');
    }

    function deal() {
        const deck = makeDeck();
        hands = [[], [], [], []];
        for (let i = 0; i < 52; i++) {
            hands[i % 4].push(deck[i]);
        }
        hands.forEach(h => h.sort((a, b) => {
            if (a.suit !== b.suit) return SUITS.indexOf(a.suit) - SUITS.indexOf(b.suit);
            if (a.suit === TRUMP) return TRUMP_ORDER.indexOf(a.rank) - TRUMP_ORDER.indexOf(b.rank);
            return NORMAL_ORDER.indexOf(a.rank) - NORMAL_ORDER.indexOf(b.rank);
        }));
        trick = [];
        tricksTakenThisHand = [0, 0, 0, 0];
        teamTricks = [0, 0];
        teamCardPts = [0, 0];
        highBid = 0;
        highBidder = -1;
        bids = [null, null, null, null];
        passesInRow = 0;
        bidTurn = (dealer + 1) % 4;
        phase = 'bidding';
        selectedCardId = null;
        inputLocked = false;
        clearAiTimer();
        playBar.classList.add('hidden');
        statusText.textContent = `${NAMES[bidTurn]} — bid ${MIN_BID}–${MAX_BID} tricks or pass`;
        bidPanel.classList.remove('hidden');
        renderBidPanel();
        renderPiles();
        renderHand();
        renderTrick();
        updateBs();
        setActiveSeat(bidTurn);
        sfx('deal');
        if (bidTurn !== 0) scheduleAi(aiBid);
    }

    function minNextBid() {
        return highBid === 0 ? MIN_BID : highBid + 1;
    }

    function renderBidPanel() {
        bidBtns.innerHTML = '';
        if (phase !== 'bidding' || bidTurn !== 0) {
            bidPanel.classList.add('hidden');
            return;
        }
        bidPanel.classList.remove('hidden');

        const min = minNextBid();
        if (highBid === 0) {
            bidHint.innerHTML = `Opening bid — choose <strong>${MIN_BID}</strong> to <strong>${MAX_BID}</strong> tricks, or pass`;
        } else {
            bidHint.innerHTML = `High bid is <strong>${highBid}</strong> — bid <strong>${min}</strong> or higher, or pass`;
        }

        const passBtn = document.createElement('button');
        passBtn.type = 'button';
        passBtn.className = 'f400-btn';
        passBtn.textContent = 'Pass';
        passBtn.addEventListener('click', () => submitBid(0, null));
        bidBtns.appendChild(passBtn);

        if (min > MAX_BID) return;

        for (let b = min; b <= MAX_BID; b++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'f400-btn f400-btn--bid';
            btn.textContent = 'Bid ' + b;
            btn.addEventListener('click', () => submitBid(0, b));
            bidBtns.appendChild(btn);
        }
    }

    function submitBid(player, amount) {
        if (phase !== 'bidding' || player !== bidTurn) return;
        if (amount !== null) {
            const min = minNextBid();
            if (amount < min || amount > MAX_BID) return;
        }
        bids[player] = amount;
        sfx(amount === null ? 'pass' : 'bid');
        if (amount === null) {
            passesInRow++;
        } else {
            highBid = amount;
            highBidder = player;
            passesInRow = 0;
        }
        bidTurn = (bidTurn + 1) % 4;
        if (highBid > 0 && passesInRow >= 3) {
            startPlay();
            return;
        }
        if (highBid === 0 && passesInRow >= 4) {
            showToast('All passed — new deal');
            dealer = (dealer + 1) % 4;
            deal();
            return;
        }
        statusText.textContent = `${NAMES[bidTurn]} — bid or pass (high: ${highBid || 'none'})`;
        renderBidPanel();
        setActiveSeat(bidTurn);
        if (bidTurn !== 0) scheduleAi(aiBid);
    }

    function aiBid() {
        if (phase !== 'bidding' || bidTurn === 0) return;
        const p = bidTurn;
        const trumpCount = hands[p].filter(c => c.suit === TRUMP).length;
        const bosses = hands[p].filter(isBoss).length;
        const strength = bosses * 2 + trumpCount;
        let bid = null;

        if (highBid === 0) {
            if (strength >= 2 && Math.random() > 0.35) {
                bid = Math.min(7, Math.max(MIN_BID, MIN_BID + Math.floor(strength / 2)));
            }
        } else if (highBid < MAX_BID && strength >= highBid - 1 && Math.random() > 0.5) {
            bid = highBid + 1;
            if (bid > 8 && Math.random() > 0.45) bid = null;
        }

        submitBid(p, bid);
    }

    function startPlay() {
        phase = 'playing';
        bidPanel.classList.add('hidden');
        selectedCardId = null;
        currentPlayer = highBidder;
        trickLeader = highBidder;
        trick = [];
        ledSuit = null;
        statusText.textContent = `${NAMES[highBidder]} won bid ${highBid} — leading`;
        sfx('click');
        renderHand();
        updateBs();
        setActiveSeat(currentPlayer);
        if (currentPlayer !== 0) scheduleAi(aiPlay);
    }

    function playCard(player, card) {
        if (inputLocked && player !== 0) return;
        const legal = legalCards(player);
        if (!legal.find(c => c.id === card.id)) return;

        clearAiTimer();
        inputLocked = false;
        selectedCardId = null;

        hands[player] = hands[player].filter(c => c.id !== card.id);
        trick.push({ player, card });
        playCardSound(card);
        if (trick.length === 1) ledSuit = card.suit;
        renderPiles();
        renderTrick();

        if (trick.length < 4) {
            currentPlayer = (player + 1) % 4;
            setActiveSeat(currentPlayer);
            statusText.textContent = `${NAMES[currentPlayer]} to play`;
            renderHand();
            if (currentPlayer !== 0) scheduleAi(aiPlay);
            return;
        }

        inputLocked = true;
        renderHand();

        const winInfo = trickWinnerInfo();
        const winner = winInfo.player;
        renderTrick(winInfo.index);

        let toastMsg;
        if (winner === 0) {
            toastMsg = `You won with ${cardLabel(winInfo.card)}! (+1 your team trick)`;
            sfx('trickWin');
        } else if (TEAM[winner] === 0) {
            toastMsg = `${NAMES[winner]} won with ${cardLabel(winInfo.card)} (+1 your team)`;
            sfx('trickWin');
        } else {
            toastMsg = `${NAMES[winner]} won with ${cardLabel(winInfo.card)} (+1 opponents)`;
            sfx('trickLose');
        }
        statusText.textContent = toastMsg;
        showToast(toastMsg);

        setTimeout(() => {
            tricksTakenThisHand[winner]++;
            teamTricks[TEAM[winner]]++;
            let trickPts = trick.reduce((s, t) => s + cardPoints(t.card), 0);
            if (hands[0].length === 0) trickPts += 10;
            teamCardPts[TEAM[winner]] += trickPts;
            trick = [];
            ledSuit = null;
            renderTrick();
            updateBs();

            if (hands[0].length === 0) {
                inputLocked = false;
                endHand();
                return;
            }

            currentPlayer = winner;
            trickLeader = winner;
            statusText.textContent = `${NAMES[winner]} leads next trick`;
            setActiveSeat(currentPlayer);
            inputLocked = false;
            renderHand();
            if (currentPlayer !== 0) scheduleAi(aiPlay);
        }, TRICK_RESOLVE_MS);
    }

    function aiPlay() {
        if (phase !== 'playing' || currentPlayer === 0) return;
        const legal = legalCards(currentPlayer);
        let pick = legal[0];
        if (trick.length) {
            const lead = trick[0].card.suit;
            const winning = currentWinningCard();
            const canWin = legal.filter(c => beats(c, winning, lead));
            if (canWin.length) {
                pick = canWin.sort((a, b) => cardPower(a) - cardPower(b))[0];
            } else {
                pick = legal.sort((a, b) => cardPower(a) - cardPower(b))[0];
            }
        } else {
            pick = legal.sort((a, b) => cardPower(b) - cardPower(a))[0];
        }
        playCard(currentPlayer, pick);
    }

    function endHand() {
        phase = 'handOver';
        const bidTeam = TEAM[highBidder];
        const made = teamTricks[bidTeam] >= highBid;
        const bidPts = made ? highBid * 10 : -highBid * 10;

        teamScores[0] += teamCardPts[0];
        teamScores[1] += teamCardPts[1];
        teamScores[bidTeam] += bidPts;
        teamScores[0] = Math.max(0, teamScores[0]);
        teamScores[1] = Math.max(0, teamScores[1]);

        const msg = made
            ? `Contract made (${teamTricks[bidTeam]}/${highBid} tricks)! Card pts + bid bonus → team +${teamCardPts[bidTeam] + bidPts}`
            : `Contract failed (${teamTricks[bidTeam]}/${highBid} tricks)! Bid penalty ${bidPts}`;
        sfx(made ? 'contractMade' : 'contractFail');
        showToast(msg);
        updateBs();

        if (teamScores[0] >= TARGET || teamScores[1] >= TARGET) {
            phase = 'gameOver';
            const win = teamScores[0] >= TARGET;
            sfx(win ? 'gameWin400' : 'gameOver');
            statusText.textContent = win ? '🎉 Your team wins! 400 reached!' : 'Opponents reach 400 — you lose!';
            return;
        }

        dealer = (dealer + 1) % 4;
        statusText.textContent = 'Next hand…';
        setTimeout(deal, 2200);
    }

    document.getElementById('rulesBtn').addEventListener('click', () => { sfx('click'); rulesOverlay.classList.remove('hidden'); });
    document.getElementById('closeRules').addEventListener('click', () => { sfx('click'); rulesOverlay.classList.add('hidden'); });
    rulesOverlay.addEventListener('click', (e) => {
        if (e.target === rulesOverlay) rulesOverlay.classList.add('hidden');
    });
    playBtn.addEventListener('click', playSelectedCard);
    soundBtn.addEventListener('click', () => {
        GameSounds.init();
        soundBtn.textContent = GameSounds.toggle() ? '🔊' : '🔇';
    });
    document.getElementById('newGameBtn').addEventListener('click', () => {
        clearAiTimer();
        inputLocked = false;
        selectedCardId = null;
        teamScores = [0, 0];
        dealer = 0;
        sfx('start');
        deal();
        showToast('New match — first to 400!');
    });

    GameSounds.init();
    if (!localStorage.getItem('f400_rules_seen')) {
        rulesOverlay.classList.remove('hidden');
        localStorage.setItem('f400_rules_seen', '1');
    }
    deal();
});
</script>
@endsection
