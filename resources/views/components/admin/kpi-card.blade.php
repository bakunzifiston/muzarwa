@props([
    'label',
    'value',
    'unit' => null,
    'icon' => 'chart',
    'tone' => 'teal',
    'href' => null,
])

@php
    $toneMap = match ($tone) {
        'gold' => [
            'card' => 'border-[#ead9b0] bg-[#faf4e8]',
            'icon' => 'bg-[#CA9636] text-white',
            'value' => 'text-[#8a6420]',
        ],
        'slate' => [
            'card' => 'border-slate-200 bg-white',
            'icon' => 'bg-slate-100 text-slate-600',
            'value' => 'text-slate-900',
        ],
        'emerald' => [
            'card' => 'border-[#c5e3cf] bg-[#eaf6ee]',
            'icon' => 'bg-[#2A5C38] text-white',
            'value' => 'text-[#2A5C38]',
        ],
        'rose' => [
            'card' => 'border-[#e8cfc4] bg-[#f8eee9]',
            'icon' => 'bg-[#BD4B2D] text-white',
            'value' => 'text-[#BD4B2D]',
        ],
        'violet' => [
            'card' => 'border-[#ddd4ec] bg-[#f4eef8]',
            'icon' => 'bg-[#6d5a9a] text-white',
            'value' => 'text-[#4c3d6e]',
        ],
        default => [
            'card' => 'border-[#d4e5d8] bg-[#eef5f0]',
            'icon' => 'bg-[#2A5C38] text-white',
            'value' => 'text-slate-900',
        ],
    };

    $classes = [
        'dashboard-kpi',
        $toneMap['card'],
        $href ? 'dashboard-kpi-link' : null,
    ];
@endphp

<{{ $href ? 'a' : 'article' }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class($classes) }}
>
    <div class="flex items-center justify-between gap-3">
        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $label }}</p>
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $toneMap['icon'] }}">
            @switch($icon)
                @case('users')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    @break
                @case('box')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    @break
                @case('factory')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    @break
                @case('banknotes')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    @break
                @case('cart')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    @break
                @case('trend')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    @break
                @case('wallet')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    @break
                @case('clock')
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @break
                @default
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
            @endswitch
        </span>
    </div>
    <p class="dashboard-kpi-value {{ $toneMap['value'] }}">
        <span>{{ $value }}</span>
        @if ($unit)
            <span class="dashboard-kpi-unit">{{ $unit }}</span>
        @endif
    </p>
</{{ $href ? 'a' : 'article' }}>
