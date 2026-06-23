/**
 * Shared Web Audio sound effects for The Maniak (themaniak.online).
 * Usage: GameSounds.play('jump'); GameSounds.init(); GameSounds.toggle();
 */
const GameSounds = (() => {
    let ctx = null;
    let master = null;
    let enabled = true;
    const lastPlay = {};
    const VOL = 1.75;

    function buildOutputChain() {
        const highpass = ctx.createBiquadFilter();
        highpass.type = 'highpass';
        highpass.frequency.value = 90;
        highpass.Q.value = 0.7;

        master = ctx.createGain();
        master.gain.value = 1.05;

        const compressor = ctx.createDynamicsCompressor();
        compressor.threshold.value = -20;
        compressor.knee.value = 18;
        compressor.ratio.value = 4;
        compressor.attack.value = 0.002;
        compressor.release.value = 0.12;

        highpass.connect(master);
        master.connect(compressor);
        compressor.connect(ctx.destination);
        return highpass;
    }

    let output = null;

    function init() {
        if (!ctx) {
            ctx = new (window.AudioContext || window.webkitAudioContext)();
            output = buildOutputChain();
        }
        if (ctx.state === 'suspended') ctx.resume();
        return ctx;
    }

    function resume() {
        init();
        if (ctx.state === 'suspended') return ctx.resume();
        return Promise.resolve();
    }

    function connectOut(node) {
        node.connect(output || ctx.destination);
    }

    function tone(freq, duration, type = 'sine', volume = 0.15, delay = 0, endFreq = null) {
        if (!enabled || !init()) return;
        const t = ctx.currentTime + delay;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        const peak = volume * VOL;
        osc.type = type;
        osc.frequency.setValueAtTime(freq, t);
        if (endFreq) osc.frequency.exponentialRampToValueAtTime(endFreq, t + duration);
        gain.gain.setValueAtTime(0, t);
        gain.gain.linearRampToValueAtTime(peak, t + 0.008);
        gain.gain.exponentialRampToValueAtTime(0.0008, t + duration);
        osc.connect(gain);
        connectOut(gain);
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
        const filter = ctx.createBiquadFilter();
        filter.type = 'highpass';
        filter.frequency.value = 700;
        src.buffer = buffer;
        gain.gain.setValueAtTime(volume * VOL, t);
        gain.gain.exponentialRampToValueAtTime(0.0008, t + duration);
        src.connect(filter);
        filter.connect(gain);
        connectOut(gain);
        src.start(t);
    }

    function chord(notes, duration = 0.35, volume = 0.1, delay = 0) {
        notes.forEach((n, i) => tone(n, duration, 'sine', volume * 0.75, delay + i * 0.04));
    }

    const presets = {
        click: () => tone(920, 0.07, 'square', 0.12),

        jump: () => {
            tone(300, 0.13, 'sine', 0.2, 0, 720);
            tone(480, 0.09, 'triangle', 0.14, 0.015);
        },
        coin: () => {
            tone(1175, 0.11, 'sine', 0.22);
            tone(1568, 0.13, 'sine', 0.16, 0.04);
        },
        hurt: () => {
            tone(200, 0.22, 'sawtooth', 0.16, 0, 90);
            noise(0.12, 0.09);
        },
        gameOver: () => {
            tone(392, 0.22, 'sine', 0.16, 0, 196);
            tone(294, 0.38, 'sine', 0.14, 0.14, 147);
        },

        placeX: () => tone(440, 0.1, 'square', 0.14),
        placeO: () => tone(330, 0.1, 'square', 0.14),
        win: () => chord([523, 659, 784, 1046], 0.4, 0.16),
        draw: () => { tone(349, 0.15, 'sine', 0.14); tone(349, 0.15, 'sine', 0.12, 0.2); },

        whack: () => {
            noise(0.06, 0.22);
            tone(140, 0.08, 'square', 0.18, 0, 70);
        },
        miss: () => tone(220, 0.12, 'sine', 0.12, 0, 160),
        start: () => chord([392, 523, 659], 0.32, 0.15),
        tick: () => tone(640, 0.04, 'square', 0.08),
        timeUp: () => {
            tone(440, 0.15, 'sine', 0.14, 0, 220);
            tone(330, 0.25, 'sine', 0.12, 0.12, 165);
        },

        dice: () => {
            for (let i = 0; i < 6; i++) noise(0.04, 0.08, i * 0.07);
        },
        diceLand: () => {
            noise(0.08, 0.14);
            tone(200, 0.1, 'triangle', 0.14, 0, 130);
        },
        ladder: () => {
            tone(392, 0.12, 'sine', 0.14, 0, 784);
            tone(523, 0.15, 'sine', 0.12, 0.08, 1046);
        },
        snake: () => {
            tone(400, 0.2, 'sawtooth', 0.14, 0, 150);
            tone(200, 0.25, 'sawtooth', 0.12, 0.1, 80);
        },
        celebrate: () => chord([523, 659, 784], 0.5, 0.16),

        roll: () => {
            noise(0.35, 0.07);
            tone(90, 0.4, 'sine', 0.09, 0, 45);
        },
        pinHit: () => {
            noise(0.05, 0.18);
            tone(300, 0.08, 'triangle', 0.14, 0, 190);
        },
        pinChain: () => tone(380, 0.06, 'triangle', 0.12),
        strike: () => {
            chord([523, 659, 784, 1046], 0.5, 0.18);
            noise(0.2, 0.12, 0.1);
        },
        spare: () => chord([440, 554, 659], 0.35, 0.14),
        gutter: () => tone(130, 0.2, 'sine', 0.12, 0, 65),

        move: () => tone(320, 0.07, 'sine', 0.12, 0, 420),
        capture: () => {
            noise(0.05, 0.12);
            tone(220, 0.12, 'square', 0.14, 0, 360);
        },
        pawnMove: () => tone(350, 0.08, 'sine', 0.14, 0, 460),
        check: () => {
            tone(820, 0.15, 'sine', 0.16, 0, 520);
            tone(620, 0.2, 'sine', 0.14, 0.1, 410);
        },
        checkmate: () => {
            tone(400, 0.2, 'sine', 0.16, 0, 200);
            tone(300, 0.25, 'sine', 0.14, 0.15, 150);
            tone(200, 0.35, 'sine', 0.12, 0.3, 100);
        },
        castle: () => chord([349, 440, 523], 0.25, 0.13),

        flip: () => tone(540, 0.06, 'sine', 0.14, 0, 800),
        match: () => chord([523, 659, 784], 0.2, 0.14),
        noMatch: () => tone(230, 0.12, 'sine', 0.12, 0, 185),
        deal: () => {
            for (let i = 0; i < 4; i++) noise(0.03, 0.08, i * 0.05);
        },
        blackjack: () => chord([392, 523, 659], 0.35, 0.16),
        bust: () => {
            tone(300, 0.15, 'sawtooth', 0.14, 0, 100);
            noise(0.1, 0.1);
        },
    };

    const debounceMs = { coin: 55, tick: 45 };

    return {
        init,
        resume,
        play(name) {
            init();
            const wait = debounceMs[name];
            if (wait) {
                const now = performance.now();
                if (lastPlay[name] && now - lastPlay[name] < wait) return;
                lastPlay[name] = now;
            }
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
