@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.chat-page) {
        max-width: none;
        padding: clamp(1rem, 3vw, 2rem);
    }

    .chat-page {
        --cyan: #00f0ff;
        --pink: #ff2d6a;
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .chat-header {
        margin-bottom: 1.25rem;
    }

    .chat-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(1.6rem, 4vw, 2.2rem);
        font-weight: 800;
        margin-bottom: 0.35rem;
    }

    .chat-subtitle {
        color: rgba(255,255,255,0.55);
    }

    .chat-layout {
        display: grid;
        grid-template-columns: minmax(220px, 280px) 1fr;
        gap: 1rem;
        min-height: min(70vh, 620px);
    }

    @media (max-width: 768px) {
        .chat-layout {
            grid-template-columns: 1fr;
            min-height: auto;
        }
        .chat-sidebar { max-height: 220px; overflow-y: auto; }
    }

    .chat-sidebar,
    .chat-panel {
        background: rgba(8, 12, 28, 0.88);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 16px 50px rgba(0,0,0,0.35);
    }

    .chat-sidebar-head,
    .chat-panel-head {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-family: 'Orbitron', sans-serif;
        font-size: 0.75rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.45);
    }

    .chat-friend {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border: none;
        background: transparent;
        color: #fff;
        width: 100%;
        text-align: left;
        cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        text-decoration: none;
    }

    .chat-friend:hover,
    .chat-friend.active {
        background: rgba(0,240,255,0.08);
    }

    .chat-friend-avatar {
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--cyan), var(--pink));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }

    .chat-friend-meta {
        flex: 1;
        min-width: 0;
    }

    .chat-friend-name {
        font-weight: 700;
        font-size: 1rem;
    }

    .chat-friend-status {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.45);
    }

    .chat-unread {
        min-width: 1.25rem;
        height: 1.25rem;
        border-radius: 999px;
        background: var(--pink);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.35rem;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        min-height: 320px;
        max-height: min(55vh, 520px);
    }

    .chat-panel-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .chat-msg {
        max-width: 78%;
        padding: 0.65rem 0.85rem;
        border-radius: 14px;
        font-size: 0.98rem;
        line-height: 1.4;
        word-break: break-word;
    }

    .chat-msg.mine {
        align-self: flex-end;
        background: rgba(0,240,255,0.15);
        border: 1px solid rgba(0,240,255,0.25);
    }

    .chat-msg.theirs {
        align-self: flex-start;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .chat-msg strong {
        display: block;
        font-size: 0.72rem;
        opacity: 0.65;
        margin-bottom: 0.2rem;
    }

    .chat-empty {
        margin: auto;
        text-align: center;
        color: rgba(255,255,255,0.45);
        padding: 2rem;
    }

    .chat-form {
        display: flex;
        gap: 0.65rem;
        padding: 0.85rem 1rem;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .chat-input {
        flex: 1;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: #fff;
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.05rem;
    }

    .chat-input:focus {
        outline: none;
        border-color: rgba(0,240,255,0.45);
    }

    .chat-send {
        padding: 0.75rem 1.2rem;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--cyan), #0088ff);
        color: #050510;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        cursor: pointer;
    }

    .online-dot {
        display: inline-block;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: #4ade80;
        margin-right: 0.25rem;
    }
</style>

<div class="chat-page">
    <div class="chat-header">
        <h1 class="chat-title">💬 Friend Chat</h1>
        <p class="chat-subtitle">Message your friends in real time. Works on mobile and desktop.</p>
    </div>

    @if ($friends->isEmpty())
        <div class="chat-panel">
            <div class="chat-empty">
                No friends yet — add friends from the <a href="{{ route('friends.index') }}" style="color:#00f0ff;">Friends page</a> first.
            </div>
        </div>
    @else
        <div class="chat-layout">
            <aside class="chat-sidebar">
                <div class="chat-sidebar-head">Friends</div>
                @foreach ($friends as $friend)
                <a href="{{ route('chat.index', ['friend' => $friend['id']]) }}"
                   class="chat-friend {{ ($activeFriendId ?? null) === $friend['id'] ? 'active' : '' }}"
                   data-friend-id="{{ $friend['id'] }}">
                    <span class="chat-friend-avatar">{{ strtoupper(substr($friend['name'], 0, 1)) }}</span>
                    <span class="chat-friend-meta">
                        <span class="chat-friend-name">{{ $friend['name'] }}</span>
                        <span class="chat-friend-status">
                            @if ($friend['online'])
                                <span class="online-dot"></span>Online
                            @else
                                Offline
                            @endif
                        </span>
                    </span>
                    @if ($friend['unread'] > 0)
                        <span class="chat-unread">{{ $friend['unread'] > 9 ? '9+' : $friend['unread'] }}</span>
                    @endif
                </a>
                @endforeach
            </aside>

            <section class="chat-panel">
                @if ($activeFriend)
                <div class="chat-panel-head">Chat with {{ $activeFriend['name'] }}</div>
                <div class="chat-panel-body">
                    <div class="chat-messages" id="chatMessages"></div>
                    <form class="chat-form" id="chatForm">
                        @csrf
                        <input type="text" class="chat-input" id="chatInput" placeholder="Type a message…" maxlength="1000" autocomplete="off" required>
                        <button type="submit" class="chat-send">Send</button>
                    </form>
                </div>
                @else
                <div class="chat-empty">Select a friend to start chatting.</div>
                @endif
            </section>
        </div>
    @endif
</div>

@if ($activeFriend)
<script>
    const chatFriendId = {{ $activeFriend['id'] }};
    const chatMessagesUrl = @json(route('chat.messages', $activeFriend['id']));
    const chatSendUrl = @json(route('chat.store', $activeFriend['id']));
    const chatPollUrl = @json(route('chat.poll'));
    const chatCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let chatLastId = 0;

    const chatList = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');

    function appendChatMsg(msg) {
        if (!msg?.id || msg.id <= chatLastId) return;
        chatLastId = msg.id;
        const el = document.createElement('div');
        el.className = 'chat-msg ' + (msg.mine ? 'mine' : 'theirs');
        el.innerHTML = `<strong>${msg.mine ? 'You' : msg.user_name}</strong>${escapeHtml(msg.body)}`;
        chatList.appendChild(el);
        chatList.scrollTop = chatList.scrollHeight;
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    async function loadChatMessages() {
        const res = await fetch(chatMessagesUrl, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        const data = await res.json();
        chatList.innerHTML = '';
        chatLastId = 0;
        (data.messages || []).forEach(appendChatMsg);
    }

    async function pollChat() {
        if (document.hidden) return;
        const res = await fetch(`${chatPollUrl}?friend=${chatFriendId}&since=${chatLastId}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        const data = await res.json();
        (data.messages || []).forEach(appendChatMsg);
    }

    chatForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;
        chatInput.value = '';
        const res = await fetch(chatSendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message: text }),
        });
        if (res.ok) {
            const data = await res.json();
            appendChatMsg(data.message);
        }
    });

    loadChatMessages();
    setInterval(pollChat, 3000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) pollChat(); });
</script>
@endif
@endsection
