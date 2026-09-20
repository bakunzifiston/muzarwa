<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $brandName = config('brand.name', 'muzarwa');
        $pageTitle = $seoTitle ?? (($title ?? 'Home') . ' | ' . $brandName);
        $pageDescription = $seoDescription ?? config('brand.seo_description');
        $canonical = url()->current();
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ $brandName }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="en_RW">
    <meta property="og:image" content="{{ asset('images/muzarwa-logo.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ asset('images/muzarwa-logo.jpg') }}">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('brand.name'),
            'url' => config('brand.website_url'),
            'email' => config('brand.email'),
            'telephone' => config('brand.phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Rwamagana',
                'addressCountry' => 'RW',
            ],
            'logo' => asset('images/muzarwa-logo.jpg'),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-light text-ink antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-[100] border-b border-black/[0.06] bg-cream">
            <div class="h-1 w-full bg-gradient-to-r from-brand via-mango to-chili"></div>
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2 lg:px-8">
                <a href="{{ route('storefront.home') }}" class="flex shrink-0 items-center">
                    <x-brand-logo class="h-14 w-auto max-w-[10.5rem] object-contain sm:h-16 sm:max-w-[12rem]" alt="muzarwa" />
                </a>

                <nav class="hidden items-center gap-5 text-sm font-medium text-ink/80 lg:flex" aria-label="Primary">
                    <x-storefront.nav-links />
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="relative z-[110] rounded-lg bg-brand px-3 py-2 text-sm font-medium text-white hover:bg-brand-dark sm:px-4">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('admin.login') }}" class="relative z-[110] rounded-lg bg-brand px-3 py-2 text-sm font-medium text-white hover:bg-brand-dark sm:px-4">
                            Login
                        </a>
                    @endauth

                    <details class="relative lg:hidden">
                        <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-cream bg-white text-ink" aria-label="Open menu">
                            <span class="sr-only">Menu</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
                        </summary>
                        <nav class="absolute right-0 z-[120] mt-2 w-56 rounded-xl border border-cream bg-white p-3 text-sm font-medium shadow-lg" aria-label="Mobile">
                            <div class="flex flex-col gap-2">
                                <x-storefront.nav-links link-class="rounded-lg px-2 py-1.5 hover:bg-cream-light" />
                            </div>
                        </nav>
                    </details>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="mt-16 border-t border-brand-dark bg-brand text-white">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-4 lg:px-8" data-stagger>
                <div>
                    <x-brand-logo class="mb-4 h-20 w-auto max-w-[12rem] rounded-xl bg-cream object-contain p-1.5" alt="muzarwa" />
                    <h3 class="text-base font-semibold lowercase tracking-wide">{{ config('brand.name') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-200">{{ config('brand.blurb') }}</p>
                </div>
                <div>
                    <h3 class="text-base font-semibold">Quick Links</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-200">
                        <li><a href="{{ route('storefront.home') }}" class="hover:text-mango">Home</a></li>
                        <li><a href="{{ route('storefront.about') }}" class="hover:text-mango">About Us</a></li>
                        <li><a href="{{ route('storefront.products') }}" class="hover:text-mango">Products</a></li>
                        <li><a href="{{ route('storefront.shop') }}" class="hover:text-mango">Shop</a></li>
                        <li><a href="{{ route('storefront.cart') }}" class="hover:text-mango">Cart</a></li>
                        <li><a href="{{ route('storefront.wishlist') }}" class="hover:text-mango">Wishlist</a></li>
                        <li><a href="{{ route('storefront.contact') }}" class="hover:text-mango">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-base font-semibold">Contact</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-200">
                        <li>{{ $primaryAddress->value ?? config('brand.location') }}</li>
                        <li>
                            <a href="tel:+{{ preg_replace('/\D/', '', $primaryPhone->value ?? config('brand.phone_digits')) }}" class="hover:text-mango">
                                {{ $primaryPhone->value ?? config('brand.phone') }}
                            </a>
                        </li>
                        <li>
                            <a href="mailto:{{ $primaryEmail->value ?? config('brand.email') }}" class="hover:text-mango">
                                {{ $primaryEmail->value ?? config('brand.email') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ config('brand.website_url') }}" class="hover:text-mango">{{ config('brand.website') }}</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-base font-semibold">Social</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-200">
                        <li>Facebook</li>
                        <li>Instagram</li>
                        <li>Twitter/X</li>
                        <li>
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $primaryWhatsapp->value ?? config('brand.phone_digits')) }}" class="hover:text-mango" target="_blank" rel="noopener noreferrer">
                                WhatsApp
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 px-4 py-5 text-center text-xs text-white/70 lg:px-8">
                © {{ date('Y') }} muzarwa. All rights reserved.
            </div>
        </footer>

        <x-storefront.whatsapp-float />
    </div>
</body>
</html>
