@props([
    'src',
    'alt' => '',
    'cover' => false,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden bg-[#eef0f2]']) }}>
    <img
        src="{{ asset($src) }}"
        alt="{{ $alt }}"
        loading="lazy"
        decoding="async"
        class="h-full w-full {{ $cover ? 'object-cover object-[center_75%]' : 'object-contain object-center' }}"
    >
</div>
