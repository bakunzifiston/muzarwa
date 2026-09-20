@props([
    'linkClass' => null,
])

@php
    $isMobile = filled($linkClass ?? null);
    $shopActive = request()->routeIs('storefront.shop')
        || request()->routeIs('storefront.product')
        || request()->routeIs('storefront.cart')
        || request()->routeIs('storefront.wishlist');
    $shopPages = [
        ['route' => 'storefront.cart', 'label' => 'Cart', 'active' => request()->routeIs('storefront.cart')],
        ['route' => 'storefront.wishlist', 'label' => 'Wishlist', 'active' => request()->routeIs('storefront.wishlist')],
    ];
    $items = [
        ['route' => 'storefront.home', 'label' => 'Home', 'active' => request()->routeIs('storefront.home')],
        ['route' => 'storefront.about', 'label' => 'About Us', 'active' => request()->routeIs('storefront.about')],
        ['route' => 'storefront.products', 'label' => 'Products', 'active' => request()->routeIs('storefront.products')],
        ['route' => 'storefront.contact', 'label' => 'Contact', 'active' => request()->routeIs('storefront.contact')],
    ];
@endphp

@foreach ($items as $item)
    @if ($item['label'] === 'Contact')
        @if ($isMobile)
            <div class="flex flex-col gap-1">
                <a
                    href="{{ route('storefront.shop') }}"
                    @class([$linkClass, 'font-semibold text-brand' => $shopActive])
                >Shop</a>
                @foreach ($shopPages as $shopPage)
                    <a
                        href="{{ route($shopPage['route']) }}"
                        @class([
                            'rounded-lg py-1.5 pl-4 pr-2 text-sm hover:bg-cream-light',
                            'font-semibold text-brand' => $shopPage['active'],
                        ])
                    >{{ $shopPage['label'] }}</a>
                @endforeach
            </div>
        @else
            <div class="group relative">
                <a
                    href="{{ route('storefront.shop') }}"
                    @class([
                        'inline-flex items-center gap-1 hover:text-brand',
                        'font-semibold text-brand' => $shopActive,
                    ])
                >
                    Shop
                    <svg class="h-3.5 w-3.5 opacity-60" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </a>
                <div class="invisible absolute left-0 top-full z-[120] w-44 pt-2 opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                    <div class="rounded-xl border border-cream bg-white p-2 text-sm font-medium text-ink shadow-lg">
                        @foreach ($shopPages as $shopPage)
                            <a
                                href="{{ route($shopPage['route']) }}"
                                @class([
                                    'block rounded-lg px-3 py-2 hover:bg-cream-light',
                                    'font-semibold text-brand' => $shopPage['active'],
                                ])
                            >{{ $shopPage['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    <a
        href="{{ route($item['route']) }}"
        @class([
            $linkClass ?? 'hover:text-brand',
            'font-semibold text-brand' => $item['active'],
        ])
    >{{ $item['label'] }}</a>
@endforeach
