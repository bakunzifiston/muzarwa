@props([
    'active' => false,
    'href' => '#',
    'icon' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'admin-nav-link',
        'admin-nav-link-active' => $active,
    ]) }}
>
    @if ($icon)
        <x-admin.nav-icon :name="$icon" />
    @endif
    <span>{{ $slot }}</span>
</a>
