@php
    $items = [
        ['route' => 'storefront.home', 'label' => 'Home', 'active' => request()->routeIs('storefront.home')],
        ['route' => 'storefront.about', 'label' => 'About Us', 'active' => request()->routeIs('storefront.about')],
        ['route' => 'storefront.products', 'label' => 'Products', 'active' => request()->routeIs('storefront.products')],
        ['route' => 'storefront.services', 'label' => 'Services', 'active' => request()->routeIs('storefront.services')],
        ['route' => 'storefront.shop', 'label' => 'Shop', 'active' => request()->routeIs('storefront.shop') || request()->routeIs('storefront.product')],
        ['route' => 'storefront.contact', 'label' => 'Contact', 'active' => request()->routeIs('storefront.contact')],
    ];
@endphp

@foreach ($items as $item)
    <a
        href="{{ route($item['route']) }}"
        @class([
            $linkClass ?? 'hover:text-brand',
            'font-semibold text-brand' => $item['active'],
        ])
    >{{ $item['label'] }}</a>
@endforeach
