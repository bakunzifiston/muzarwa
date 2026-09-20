@props([
    'alt' => 'muzarwa',
])

<img
    src="{{ asset('images/muzarwa-logo.jpg') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => 'h-14 w-auto object-contain']) }}
>
