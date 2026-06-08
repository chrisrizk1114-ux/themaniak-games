/**
 * Shared Web Audio sound effects for The Maniak (themaniak.online).
 * Usage: GameSounds.play('jump'); GameSounds.init(); GameSounds.toggle();
 */
const GameSounds = (() => {
    let ctx = null;
    let enabled = true;

    function init() {
        if (!ctx) ctx = new (window.AudioContext || window.webkitAudioContext)();
        if (ctx.state === 'suspended') ctx.resume();
        return ctx;
    }

    function tone(freq, duration, type = 'sine', volume = 0.15, delay = 0, endFreq = null) {
        if (!enabled || !init()) return;
        const t = ctx.currentTime + delay;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, t);
        if (endFreq) osc.frequency.exponentialRampToValueAtTime(endFreq, t + duration);
        gain.gain.setValueAtTime(0, t);
        gain.gain.linearRampToValueAtTime(volume, t + 0.015);
        gain.gain.exponentialRampToValueAtTime(0.001, t + duration);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(t);
        osc.stop(t + duration + 0.05);
    }

    function noise(duration, volume = 0.08, delay = 0) {
        if (!enabled || !init()) return;
        const t = ctx.currentTime + delay;
        const bufferSize = ctx.sampleRate * duration;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) data[i] = Math.random() * 2 - 1;
        const src = ctx.createBufferSource();
        const gain = ctx.createGain();
        src.buffer = buffer;
        gain.gain.setValueAtTime(volume, t);
        gain.gain.exponentialRampToValueAtTime(0.001, t + duration);
        src.connect(gain);
        gain.connect(ctx.destination);
        src.start(t);
    }

    function chord(notes, duration = 0.35, volume = 0.1, delay = 0) {
        notes.forEach((n, i) => tone(n, duration, 'sine', volume * 0.7, delay + i * 0.04));
    }

    const presets = {
        click: () => tone(880, 0.06, 'square', 0.08),

        jump: () => {
            tone(220, 0.12, 'sine', 0.12, 0, 520);
            tone(330, 0.08, 'triangle', 0.06, 0.02);
        },
        coin: () => {
            tone(1046, 0.1, 'sine', 0.14);
            tone(1318, 0.12, 'sine', 0.1, 0.05);
        },
        hurt: () => {
            tone(180, 0.25, 'sawtooth', 0.12, 0, 80);
            noise(0.15, 0.06);
        },
        gameOver: () => {
            tone(392, 0.2, 'sine', 0.12, 0, 196);
            tone(294, 0.35, 'sine', 0.1, 0.15, 147);
        },

        placeX: () => tone(440, 0.1, 'square', 0.1),
        placeO: () => tone(330, 0.1, 'square', 0.1),
        win: () => chord([523, 659, 784, 1046], 0.4, 0.12),
        draw: () => { tone(349, 0.15); tone(349, 0.15, 'sine', 0.1, 0.2); },

        whack: () => {
            noise(0.06, 0.18);
            tone(120, 0.08, 'square', 0.15, 0, 60);
        },
        miss: () => tone(200, 0.12, 'sine', 0.08, 0, 150),
        start: () => chord([392, 523, 659], 0.3, 0.1),
        tick: () => tone(600, 0.04, 'square', 0.05),
        timeUp: () => {
            tone(440, 0.15, 'sine', 0.1, 0, 220);
            tone(330, 0.25, 'sine', 0.1, 0.12, 165);
        },

        dice: () => {
            for (let i = 0; i < 6; i++) noise(0.04, 0.06, i * 0.07);
        },
        diceLand: () => {
            noise(0.08, 0.12);
            tone(180, 0.1, 'triangle', 0.1, 0, 120);
        },
        ladder: () => {
            tone(392, 0.12, 'sine', 0.1, 0, 784);
            tone(523, 0.15, 'sine', 0.08, 0.08, 1046);
        },
        snake: () => {
            tone(400, 0.2, 'sawtooth', 0.1, 0, 150);
            tone(200, 0.25, 'sawtooth', 0.08, 0.1, 80);
        },
        celebrate: () => chord([523, 659, 784], 0.5, 0.12),

        roll: () => {
            noise(0.35, 0.05);
            tone(80, 0.4, 'sine', 0.06, 0, 40);
        },
        pinHit: () => {
            noise(0.05, 0.14);
            tone(280, 0.08, 'triangle', 0.1, 0, 180);
        },
        pinChain: () => tone(350, 0.06, 'triangle', 0.08),
        strike: () => {
            chord([523, 659, 784, 1046], 0.5, 0.14);
            noise(0.2, 0.1, 0.1);
        },
        spare: () => chord([440, 554, 659], 0.35, 0.1),
        gutter: () => tone(120, 0.2, 'sine', 0.08, 0, 60),

        move: () => tone(300, 0.07, 'sine', 0.08, 0, 400),
        capture: () => {
            noise(0.05, 0.1);
            tone(200, 0.12, 'square', 0.1, 0, 350);
        },
        pawnMove: () => tone(330, 0.08, 'sine', 0.1, 0, 440),
        check: () => {
            tone(800, 0.15, 'sine', 0.12, 0, 500);
            tone(600, 0.2, 'sine', 0.1, 0.1, 400);
        },
        checkmate: () => {
            tone(400, 0.2, 'sine', 0.12, 0, 200);
            tone(300, 0.25, 'sine', 0.1, 0.15, 150);
            tone(200, 0.35, 'sine', 0.1, 0.3, 100);
        },
        castle: () => chord([349, 440, 523], 0.25, 0.09),

        flip: () => tone(520, 0.06, 'sine', 0.1, 0, 780),
        match: () => chord([523, 659, 784], 0.2, 0.1),
        noMatch: () => tone(220, 0.12, 'sine', 0.08, 0, 180),
        deal: () => {
            for (let i = 0; i < 4; i++) noise(0.03, 0.06, i * 0.05);
        },
        blackjack: () => chord([392, 523, 659], 0.35, 0.12),
        bust: () => {
            tone(300, 0.15, 'sawtooth', 0.1, 0, 100);
            noise(0.1, 0.08);
        },
    };

    return {
        init,
        play(name) {
            init();
            if (presets[name]) presets[name]();
        },
        isEnabled: () => enabled,
        setEnabled(v) { enabled = !!v; },
        toggle() {
            enabled = !enabled;
            if (enabled) presets.click();
            return enabled;
        },
    };
})();
