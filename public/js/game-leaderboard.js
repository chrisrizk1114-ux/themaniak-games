/**
 * Global leaderboard for The Maniak scored games.
 * Usage: GameLeaderboard.mount('#gameLeaderboard', 'whack-a-mole');
 */
const GameLeaderboard = (() => {
    const medal = ['🥇', '🥈', '🥉'];
    const rowClass = ['game-lb-row--gold', 'game-lb-row--silver', 'game-lb-row--bronze'];

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function render(container, data) {
        const unit = data.unit || 'pts';
        let listHtml = '';

        if (!data.top?.length) {
            listHtml = '<p class="game-lb-empty">No scores yet — be the first!</p>';
        } else {
            listHtml = '<ol class="game-lb-list">' + data.top.map((row, i) => `
                <li class="game-lb-row ${rowClass[i] || ''}">
                    <span class="game-lb-rank">${medal[i] || '#' + row.rank}</span>
                    <span class="game-lb-name">${escapeHtml(row.name)}</span>
                    <span class="game-lb-score">${row.score} ${unit}</span>
                </li>
            `).join('') + '</ol>';
        }

        let youHtml = '';
        if (!data.logged_in) {
            youHtml = '<div class="game-lb-you"><a href="/login" style="color:#67e8f9;">Log in</a> to save scores & see your rank</div>';
        } else if (data.you?.rank) {
            youHtml = `<div class="game-lb-you">You: <strong>#${data.you.rank}</strong> · ${data.you.score} ${unit}</div>`;
        } else {
            youHtml = `<div class="game-lb-you">You: <strong>unranked</strong> — play to get on the board!</div>`;
        }

        container.innerHTML = `
            <div class="game-lb-title">🏆 Top 3 Players</div>
            ${listHtml}
            ${youHtml}
        `;
    }

    async function load(game) {
        const res = await fetch(`/leaderboard/${encodeURIComponent(game)}`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Leaderboard unavailable');
        return res.json();
    }

    async function mount(selector, game) {
        const container = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!container) return;

        container.dataset.game = game;
        container.classList.add('game-lb');

        try {
            render(container, await load(game));
        } catch {
            container.innerHTML = '<p class="game-lb-empty">Leaderboard unavailable</p>';
        }
    }

    async function submit(game, score) {
        const res = await fetch(`/leaderboard/${encodeURIComponent(game)}`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
            },
            credentials: 'same-origin',
            body: JSON.stringify({ score: Math.max(0, Math.floor(score)) }),
        });

        if (res.status === 401) return null;
        if (!res.ok) throw new Error('Could not save score');

        const data = await res.json();
        const container = document.querySelector(`.game-lb[data-game="${game}"]`);
        if (container) render(container, data);
        return data;
    }

    async function recordWin(game) {
        return submit(game, 1);
    }

    return { mount, load, submit, recordWin };
})();
