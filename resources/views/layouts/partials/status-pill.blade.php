@php
    $isSelf = $self ?? false;
    $online = $user->isOnline();
    if ($isSelf) {
        $label = $online ? 'You are online' : 'You are offline';
    } else {
        $label = $online ? 'Online' : 'Offline';
    }
@endphp

<span
    class="status-pill {{ $online ? 'status-pill--online' : 'status-pill--offline' }}{{ $isSelf ? ' status-pill--self' : '' }}"
    @if($isSelf) id="myStatusPill" @else data-user-id="{{ $user->id }}" @endif
    title="{{ $isSelf ? ($online ? 'You are online' : 'You are offline') : ($user->name.' is '.($online ? 'online' : 'offline')) }}"
>
    <span class="status-pill-dot"></span>
    <span class="status-pill-label">{{ $label }}</span>
</span>
