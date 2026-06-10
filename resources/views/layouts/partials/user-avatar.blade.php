@props([
    'user',
    'size' => 'md',
    'class' => '',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'user-avatar--sm',
        'lg' => 'user-avatar--lg',
        'xl' => 'user-avatar--xl',
        default => 'user-avatar--md',
    };
@endphp

<span {{ $attributes->merge(['class' => "user-avatar {$sizeClass} {$class}"]) }}>
    @if ($user->avatarUrl())
        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
    @else
        <span class="user-avatar-initial">{{ $user->avatarInitial() }}</span>
    @endif
</span>
