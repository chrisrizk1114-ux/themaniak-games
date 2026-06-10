@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.profile-page) {
        max-width: none;
        padding: clamp(1.5rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem);
    }

    .profile-page {
        --cyan: #00f0ff;
        --pink: #ff2d6a;
        --purple: #a855f7;
        max-width: 520px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .profile-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse at 20% 10%, rgba(0,240,255,0.12) 0%, transparent 42%),
            radial-gradient(ellipse at 80% 30%, rgba(168,85,247,0.1) 0%, transparent 40%);
    }

    .profile-card {
        position: relative;
        z-index: 1;
        padding: clamp(1.5rem, 4vw, 2rem);
        border-radius: 24px;
        background: rgba(8, 12, 28, 0.88);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 24px 70px rgba(0,0,0,0.4);
    }

    .profile-badge {
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
        margin-bottom: 0.85rem;
    }

    .profile-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.6rem, 4vw, 2rem);
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .profile-subtitle {
        color: rgba(255,255,255,0.5);
        margin-bottom: 1.5rem;
    }

    .profile-avatar-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .profile-avatar-wrap {
        position: relative;
    }

    .profile-avatar-wrap .user-avatar--xl {
        box-shadow: 0 0 40px rgba(0,240,255,0.2);
    }

    .profile-avatar-wrap .nav-user-online-dot {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 16px;
        height: 16px;
    }

    .profile-name {
        font-size: 1.35rem;
        font-weight: 700;
        text-align: center;
    }

    .profile-email {
        font-size: 0.95rem;
        color: rgba(255,255,255,0.45);
        text-align: center;
        word-break: break-word;
    }

    .profile-upload-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
    }

    .profile-file-input {
        width: 100%;
        max-width: 320px;
        padding: 0.65rem;
        border-radius: 12px;
        border: 1px dashed rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.04);
        color: rgba(255,255,255,0.75);
        font-size: 0.9rem;
    }

    .profile-file-input::file-selector-button {
        margin-right: 0.75rem;
        padding: 0.45rem 0.85rem;
        border: none;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: #fff;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        cursor: pointer;
    }

    .profile-btn {
        padding: 0.75rem 1.25rem;
        border: none;
        border-radius: 12px;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .profile-btn--primary {
        color: #fff;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        box-shadow: 0 6px 20px rgba(255,45,106,0.3);
    }

    .profile-btn--primary:hover {
        transform: translateY(-1px);
    }

    .profile-btn--danger {
        color: #fecaca;
        background: rgba(248,113,113,0.15);
        border: 1px solid rgba(248,113,113,0.35);
    }

    .profile-hint {
        font-size: 0.82rem;
        color: rgba(255,255,255,0.4);
        text-align: center;
        line-height: 1.5;
    }

    .profile-meta {
        display: grid;
        gap: 0.75rem;
    }

    .profile-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
    }

    .profile-meta-label {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.4);
    }

    .profile-meta-value {
        font-weight: 600;
        color: #fff;
        text-align: right;
    }

    .profile-alert {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .profile-alert--success {
        background: rgba(74,222,128,0.12);
        border: 1px solid rgba(74,222,128,0.28);
        color: #bbf7d0;
    }

    .profile-alert--error {
        background: rgba(248,113,113,0.12);
        border: 1px solid rgba(248,113,113,0.28);
        color: #fecaca;
    }
</style>

<div class="profile-page">
    <div class="profile-card">
        <span class="profile-badge">👤 Profile</span>
        <h1 class="profile-title">Your Profile</h1>
        <p class="profile-subtitle">Update how you appear to friends across Mini Games.</p>

        @if (session('success'))
            <div class="profile-alert profile-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="profile-alert profile-alert--error">{{ $errors->first() }}</div>
        @endif

        <div class="profile-avatar-section">
            <div class="profile-avatar-wrap">
                @include('layouts.partials.user-avatar', ['user' => $user, 'size' => 'xl'])
                <span
                    class="nav-user-online-dot {{ $user->isOnline() ? 'is-online' : 'is-offline' }}"
                    title="{{ $user->isOnline() ? 'Online' : 'Offline' }}"
                ></span>
            </div>
            <div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-email">{{ $user->email }}</div>
            </div>
            @include('layouts.partials.status-pill', ['user' => $user, 'self' => true])
        </div>

        <form class="profile-upload-form" method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
            @csrf
            <input
                class="profile-file-input"
                type="file"
                name="avatar"
                accept="image/jpeg,image/png,image/gif,image/webp"
                required
            >
            <button type="submit" class="profile-btn profile-btn--primary">Save Profile Picture</button>
            <p class="profile-hint">JPG, PNG, GIF, or WebP · Max 2 MB</p>
        </form>

        @if ($user->avatar_path)
        <form method="POST" action="{{ route('profile.avatar.destroy') }}" style="margin-top:1rem;text-align:center;">
            @csrf
            @method('DELETE')
            <button type="submit" class="profile-btn profile-btn--danger">Remove Picture</button>
        </form>
        @endif

        <div class="profile-meta" style="margin-top:1.5rem;">
            <div class="profile-meta-row">
                <span class="profile-meta-label">Account</span>
                <span class="profile-meta-value">{{ $user->isOwner() ? 'Owner' : 'Player' }}</span>
            </div>
            <div class="profile-meta-row">
                <span class="profile-meta-label">Member since</span>
                <span class="profile-meta-value">{{ $user->created_at->format('M j, Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
