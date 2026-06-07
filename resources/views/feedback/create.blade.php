@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.feedback-page) {
        max-width: none;
        padding: clamp(1.5rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem);
    }

    .feedback-page {
        --cyan: #00f0ff;
        --pink: #ff2d6a;
        --purple: #a855f7;
        --gold: #ffd54a;
        max-width: 640px;
        margin: 0 auto;
        position: relative;
    }

    .feedback-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse at 20% 10%, rgba(168,85,247,0.12) 0%, transparent 42%),
            radial-gradient(ellipse at 80% 20%, rgba(0,240,255,0.1) 0%, transparent 40%);
    }

    .feedback-header {
        position: relative;
        z-index: 1;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .feedback-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        background: rgba(168,85,247,0.1);
        border: 1px solid rgba(168,85,247,0.3);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--purple);
        margin-bottom: 0.75rem;
    }

    .feedback-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.8rem, 4vw, 2.3rem);
        font-weight: 800;
        margin-bottom: 0.35rem;
    }

    .feedback-subtitle {
        color: rgba(255,255,255,0.55);
        font-size: 1.05rem;
    }

    .feedback-card {
        position: relative;
        z-index: 1;
        padding: 1.5rem 1.75rem;
        border-radius: 20px;
        background: rgba(8, 12, 28, 0.88);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 16px 50px rgba(0,0,0,0.35);
        backdrop-filter: blur(14px);
    }

    .feedback-alert {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .feedback-alert--success {
        background: rgba(34,197,94,0.12);
        border: 1px solid rgba(34,197,94,0.35);
        color: #bbf7d0;
    }

    .feedback-alert--error {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.35);
        color: #fecaca;
    }

    .feedback-field {
        margin-bottom: 1rem;
    }

    .feedback-field label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 700;
        color: rgba(255,255,255,0.75);
        font-size: 0.95rem;
    }

    .feedback-field input,
    .feedback-field textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.05);
        color: #fff;
        font-family: 'Rajdhani', sans-serif;
        font-size: 1rem;
    }

    .feedback-field textarea {
        min-height: 140px;
        resize: vertical;
    }

    .feedback-field input:focus,
    .feedback-field textarea:focus {
        outline: none;
        border-color: rgba(168,85,247,0.45);
        box-shadow: 0 0 0 3px rgba(168,85,247,0.12);
    }

    .feedback-submit {
        width: 100%;
        padding: 0.9rem 1.25rem;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--purple), var(--pink));
        color: #fff;
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 6px 24px rgba(168,85,247,0.35);
        transition: transform 0.2s;
    }

    .feedback-submit:hover {
        transform: translateY(-1px);
    }

    .feedback-hint {
        margin-top: 1rem;
        text-align: center;
        color: rgba(255,255,255,0.4);
        font-size: 0.9rem;
    }
</style>

<div class="feedback-page">
    <header class="feedback-header">
        <div class="feedback-badge">💬 Feedback</div>
        <h1 class="feedback-title">Tell us what you think</h1>
        <p class="feedback-subtitle">Bug reports, game ideas, or anything about The Maniak — we read every message.</p>
    </header>

    <div class="feedback-card">
        @if (session('success'))
            <div class="feedback-alert feedback-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="feedback-alert feedback-alert--error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('feedback.store') }}">
            @csrf

            @guest
            <div class="feedback-field">
                <label for="name">Your name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $name) }}" required>
            </div>
            <div class="feedback-field">
                <label for="email">Your email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>
            </div>
            @else
            <p style="margin-bottom:1rem;color:rgba(255,255,255,0.55);">
                Sending as <strong style="color:#fff;">{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})
            </p>
            @endguest

            <div class="feedback-field">
                <label for="subject">Subject <span style="font-weight:500;color:rgba(255,255,255,0.35);">(optional)</span></label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" maxlength="120" placeholder="e.g. Lebanese 400 bug, new game idea…">
            </div>

            <div class="feedback-field">
                <label for="message">Message</label>
                <textarea id="message" name="message" required minlength="10" maxlength="5000" placeholder="Describe your feedback in detail…">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="feedback-submit">Send Feedback</button>
        </form>

        <p class="feedback-hint">The site owner is notified when you submit.</p>
    </div>
</div>
@endsection
