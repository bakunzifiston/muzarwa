@props([
    'size' => 'md',
])

@php
    $box = match ($size) {
        'lg' => 'h-24 w-24 sm:h-28 sm:w-28',
        'sm' => 'h-14 w-14',
        default => 'h-16 w-16 sm:h-20 sm:w-20',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <x-storefront.product-shot src="images/storefront/akanovela-chilli-sauce.jpg" alt="Akanovela Chilli Sauce" class="{{ $box }} rounded-lg ring-1 ring-black/5" />
    <x-storefront.product-shot src="images/storefront/akanovela-chilli-oil.jpg" alt="Akanovela Chilli Oil" class="{{ $box }} rounded-lg ring-1 ring-black/5" />
    <x-storefront.product-shot src="images/storefront/itunda-squash.jpg" alt="Itunda Passion Fruit Squash" class="{{ $box }} rounded-lg ring-1 ring-black/5" />
</div>
