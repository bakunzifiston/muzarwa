@props([
    'onDark' => false,
])

@php
    $tabs = [
        [
            'route' => 'storefront.shop',
            'label' => 'Catalog',
            'active' => request()->routeIs('storefront.shop') || request()->routeIs('storefront.product'),
        ],
        [
            'route' => 'storefront.cart',
            'label' => 'Cart',
            'active' => request()->routeIs('storefront.cart'),
        ],
        [
            'route' => 'storefront.wishlist',
            'label' => 'Wishlist',
            'active' => request()->routeIs('storefront.wishlist'),
        ],
    ];
@endphp

<nav class="mt-6 flex flex-wrap gap-2" aria-label="Shop section">
    @foreach ($tabs as $tab)
        <a
            href="{{ route($tab['route']) }}"
            @class([
                'rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-wide transition',
                'bg-white text-[#2A5C38] shadow-sm' => $tab['active'] && $onDark,
                'bg-[#2A5C38] text-white' => $tab['active'] && ! $onDark,
                'border border-white/30 text-white/90 hover:bg-white/10' => ! $tab['active'] && $onDark,
                'border border-slate-200 text-slate-600 hover:border-[#2A5C38]/30 hover:text-[#2A5C38]' => ! $tab['active'] && ! $onDark,
            ])
        >{{ $tab['label'] }}</a>
    @endforeach
</nav>
