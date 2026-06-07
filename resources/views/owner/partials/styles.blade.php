<style>
    .main-content:has(.owner-page) {
        max-width: none;
        padding: clamp(1.5rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem);
    }

    .owner-page {
        --cyan: #00f0ff;
        --pink: #ff2d6a;
        --purple: #a855f7;
        --gold: #ffd54a;
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
    }

    .owner-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse at 10% 5%, rgba(255,213,74,0.1) 0%, transparent 40%),
            radial-gradient(ellipse at 90% 15%, rgba(168,85,247,0.12) 0%, transparent 42%);
    }

    .owner-header {
        position: relative;
        z-index: 1;
        margin-bottom: 1.75rem;
    }

    .owner-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        background: rgba(255,213,74,0.1);
        border: 1px solid rgba(255,213,74,0.35);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 0.75rem;
    }

    .owner-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 800;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }

    .owner-subtitle {
        color: rgba(255,255,255,0.55);
        font-size: 1.05rem;
    }

    .owner-nav {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .owner-nav-link {
        padding: 0.5rem 1rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .owner-nav-link:hover,
    .owner-nav-link.active {
        color: #fff;
        border-color: rgba(255,213,74,0.4);
        background: rgba(255,213,74,0.08);
    }

    .owner-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1.25rem;
    }

    .owner-card {
        padding: 1.35rem 1.5rem;
        border-radius: 20px;
        background: rgba(8, 12, 28, 0.88);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 16px 50px rgba(0,0,0,0.35);
        backdrop-filter: blur(14px);
    }

    .owner-card-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.85rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.45);
        margin-bottom: 1rem;
    }

    .owner-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.85rem;
    }

    .owner-stat {
        padding: 1rem;
        border-radius: 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        text-align: center;
    }

    .owner-stat-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--cyan);
        line-height: 1.1;
    }

    .owner-stat-label {
        margin-top: 0.35rem;
        font-size: 0.82rem;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .owner-capabilities {
        display: grid;
        gap: 0.65rem;
    }

    .owner-capability {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
    }

    .owner-capability-icon {
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .owner-capability strong {
        display: block;
        color: #fff;
        margin-bottom: 0.15rem;
    }

    .owner-capability span {
        color: rgba(255,255,255,0.5);
        font-size: 0.92rem;
    }

    .owner-alert {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .owner-alert--success {
        background: rgba(34,197,94,0.12);
        border: 1px solid rgba(34,197,94,0.35);
        color: #bbf7d0;
    }

    .owner-alert--error {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.35);
        color: #fecaca;
    }

    .owner-table-wrap {
        overflow-x: auto;
    }

    .owner-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .owner-table th,
    .owner-table td {
        padding: 0.75rem 0.65rem;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .owner-table th {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.68rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.4);
    }

    .owner-role-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .owner-role-badge--owner {
        background: rgba(255,213,74,0.15);
        border: 1px solid rgba(255,213,74,0.35);
        color: var(--gold);
    }

    .owner-role-badge--user {
        background: rgba(0,240,255,0.1);
        border: 1px solid rgba(0,240,255,0.25);
        color: var(--cyan);
    }

    .owner-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .owner-btn {
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        border: 1px solid transparent;
        font-family: 'Rajdhani', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }

    .owner-btn--gold {
        background: rgba(255,213,74,0.12);
        border-color: rgba(255,213,74,0.35);
        color: var(--gold);
    }

    .owner-btn--cyan {
        background: rgba(0,240,255,0.1);
        border-color: rgba(0,240,255,0.3);
        color: var(--cyan);
    }

    .owner-btn--danger {
        background: rgba(239,68,68,0.12);
        border-color: rgba(239,68,68,0.35);
        color: #fca5a5;
    }

    .owner-search {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .owner-search input {
        flex: 1;
        padding: 0.65rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.05);
        color: #fff;
        font-family: 'Rajdhani', sans-serif;
        font-size: 1rem;
    }

    .owner-search button {
        padding: 0.65rem 1.15rem;
        border-radius: 12px;
        border: 1px solid rgba(255,213,74,0.35);
        background: rgba(255,213,74,0.12);
        color: var(--gold);
        font-weight: 700;
        cursor: pointer;
    }

    .owner-pagination {
        margin-top: 1rem;
    }

    .owner-pagination nav {
        display: flex;
        justify-content: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .owner-pagination a,
    .owner-pagination span {
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .owner-pagination span[aria-current="page"] {
        background: rgba(255,213,74,0.15);
        border-color: rgba(255,213,74,0.35);
        color: var(--gold);
    }
</style>
