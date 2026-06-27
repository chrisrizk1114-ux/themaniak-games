@props([
    'size' => 'md',
    'speech' => null,
    'wave' => false,
    'class' => '',
])

@php
    $sizes = [
        'sm' => ['w' => 36, 'h' => 44, 'cls' => 'maniak-mascot--sm'],
        'md' => ['w' => 80, 'h' => 100, 'cls' => 'maniak-mascot--md'],
        'hero' => ['w' => 130, 'h' => 162, 'cls' => 'maniak-mascot--hero'],
        'loader' => ['w' => 76, 'h' => 95, 'cls' => 'maniak-mascot--loader'],
    ];
    $dim = $sizes[$size] ?? $sizes['md'];
    $waveClass = ($wave || $size === 'hero' || $size === 'loader') ? 'maniak-mascot--wave' : '';
    $gradId = 'mascotBody_' . $size . '_' . substr(md5((string) $speech), 0, 6);
@endphp

<div
    class="maniak-mascot {{ $dim['cls'] }} {{ $waveClass }} {{ $class }}"
    style="width: {{ $dim['w'] }}px; height: {{ $dim['h'] }}px;"
    aria-hidden="true"
    {{ $attributes }}
>
    @if ($speech)
        <div class="maniak-mascot-speech">{{ $speech }}</div>
    @endif
    <svg viewBox="0 0 64 80" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="The Maniak mascot">
        <ellipse cx="32" cy="76" rx="16" ry="4" fill="rgba(0,0,0,0.28)"/>
        <path class="mascot-scarf" d="M 18 30 Q 8 38 4 48" stroke="#f472b6" stroke-width="4" stroke-linecap="round" fill="none"/>
        <rect x="18" y="38" width="28" height="30" rx="8" fill="url(#{{ $gradId }})"/>
        <rect x="14" y="52" width="8" height="14" rx="3" fill="#0369a1"/>
        <rect x="42" y="52" width="8" height="14" rx="3" fill="#0369a1"/>
        <g class="mascot-arm-wave">
            <rect x="44" y="36" width="7" height="14" rx="3" fill="#0284c7" transform="rotate(12 47.5 43)"/>
        </g>
        <rect x="13" y="40" width="7" height="12" rx="3" fill="#0284c7" transform="rotate(-10 16.5 46)"/>
        <circle cx="32" cy="24" r="14" fill="#f8fafc"/>
        <path d="M 18 20 A 14 14 0 0 1 46 20 L 46 18 A 16 16 0 0 0 18 18 Z" fill="#0ea5e9"/>
        <rect x="36" y="20" width="14" height="7" rx="3.5" fill="#7dd3fc"/>
        <rect class="mascot-visor-glow" x="38" y="21.5" width="8" height="3" rx="1.5" fill="rgba(255,255,255,0.7)"/>
        <circle cx="38" cy="25" r="2.5" fill="#0f172a"/>
        <circle cx="28" cy="25" r="2.5" fill="#0f172a"/>
        <circle cx="39" cy="24.2" r="0.9" fill="#fff"/>
        <circle cx="29" cy="24.2" r="0.9" fill="#fff"/>
        <path d="M 27 31 Q 32 35 37 31" stroke="#0f172a" stroke-width="1.5" stroke-linecap="round" fill="none"/>
        <defs>
            <linearGradient id="{{ $gradId }}" x1="18" y1="38" x2="46" y2="68" gradientUnits="userSpaceOnUse">
                <stop stop-color="#38bdf8"/>
                <stop offset="1" stop-color="#0284c7"/>
            </linearGradient>
        </defs>
    </svg>
</div>
