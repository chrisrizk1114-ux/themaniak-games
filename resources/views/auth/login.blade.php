@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.auth-page) {
        max-width: none;
        padding: clamp(2rem, 5vw, 4rem) clamp(1rem, 3vw, 2rem);
        min-height: calc(100vh - 76px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-page {
        --pink: #ff2d6a;
        --cyan: #00f0ff;
        --gold: #ffd54a;
        --purple: #a855f7;
        width: min(100%, 440px);
        position: relative;
    }

    .auth-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse at 20% 15%, rgba(255,45,106,0.16) 0%, transparent 42%),
            radial-gradient(ellipse at 80% 25%, rgba(0,240,255,0.12) 0%, transparent 40%),
            radial-gradient(ellipse at 50% 85%, rgba(168,85,247,0.1) 0%, transparent 45%);
    }

    .auth-card {
        position: relative;
        z-index: 1;
        padding: clamp(1.75rem, 4vw, 2.5rem);
        border-radius: 24px;
        background: rgba(8, 12, 28, 0.88);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 24px 70px rgba(0,0,0,0.45), 0 0 50px rgba(0,240,255,0.06);
        backdrop-filter: blur(14px);
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        background: rgba(0,240,255,0.08);
        border: 1px solid rgba(0,240,255,0.22);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--cyan);
        margin-bottom: 1rem;
    }

    .auth-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.6rem, 4vw, 2rem);
        font-weight: 800;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
        background: linear-gradient(90deg, #fff, var(--cyan));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .auth-subtitle {
        color: rgba(255,255,255,0.55);
        font-size: 1.05rem;
        margin-bottom: 1.75rem;
    }

    .auth-field {
        margin-bottom: 1.15rem;
    }

    .auth-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.65);
        margin-bottom: 0.45rem;
    }

    .auth-input {
        width: 100%;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: #fff;
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.05rem;
        font-weight: 600;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }

    .auth-input:focus {
        outline: none;
        border-color: rgba(0,240,255,0.45);
        box-shadow: 0 0 0 3px rgba(0,240,255,0.12);
        background: rgba(255,255,255,0.06);
    }

    .auth-input::placeholder {
        color: rgba(255,255,255,0.28);
    }

    .auth-error {
        margin-top: 0.4rem;
        font-size: 0.9rem;
        color: #f87171;
        font-weight: 600;
    }

    .auth-alert {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        font-weight: 600;
        font-size: 0.95rem;
        background: rgba(248,113,113,0.12);
        border: 1px solid rgba(248,113,113,0.28);
        color: #fecaca;
    }

    .auth-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .auth-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255,255,255,0.65);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
    }

    .auth-check input {
        width: 1rem;
        height: 1rem;
        accent-color: var(--cyan);
    }

    .auth-submit {
        width: 100%;
        padding: 0.95rem 1.25rem;
        border: none;
        border-radius: 14px;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #fff;
        cursor: pointer;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        box-shadow: 0 8px 28px rgba(255,45,106,0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .auth-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(255,45,106,0.45);
    }

    .auth-footer {
        margin-top: 1.5rem;
        text-align: center;
        color: rgba(255,255,255,0.55);
        font-size: 1rem;
    }

    .auth-footer a {
        color: var(--cyan);
        font-weight: 700;
        text-decoration: none;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .auth-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.35rem 0;
        color: rgba(255,255,255,0.28);
        font-size: 0.78rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.1);
    }

    .auth-guest-link {
        display: block;
        text-align: center;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        font-weight: 700;
        transition: border-color 0.2s, background 0.2s;
    }

    .auth-guest-link:hover {
        border-color: rgba(0,240,255,0.3);
        background: rgba(0,240,255,0.06);
        color: #fff;
    }
</style>

<div class="auth-page">
    <div class="auth-card">
        <span class="auth-badge">🎮 Player Login</span>
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to save scores and track your progress across the arcade.</p>

        @if ($errors->any())
            <div class="auth-alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="auth-alert" style="background:rgba(34,197,94,0.12);border-color:rgba(34,197,94,0.35);color:#bbf7d0;">
                {{ session('status') }}
            </div>
        @endif

        @if (str_contains(session('url.intended') ?? '', 'feedback'))
            <div class="auth-alert" style="background:rgba(168,85,247,0.12);border-color:rgba(168,85,247,0.35);color:#e9d5ff;">
                Please log in to send feedback to the site owner.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="auth-field">
                <label class="auth-label" for="email">Email</label>
                <input
                    class="auth-input"
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    required
                    autofocus
                >
                @error('email')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="password">Password</label>
                <input
                    class="auth-input"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-row">
                <label class="auth-check">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="auth-submit">Sign In</button>
        </form>

        <a href="{{ url('/') }}" class="auth-guest-link" style="margin-top: 1rem;">Continue as guest</a>

        <p class="auth-footer">
            New here?
            <a href="{{ route('register') }}">Create an account</a>
        </p>
    </div>
</div>
@endsection
