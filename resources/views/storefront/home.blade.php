<x-layouts.storefront title="Home" :seo-title="config('brand.seo_title')" :seo-description="config('brand.seo_description')">
    <div id="storefront-home">
        @if (session('status'))
            <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        {{-- Hero --}}
        <section class="relative overflow-hidden bg-[#2A5C38] text-white">
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-[#CA9636]/15 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-white/5 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#163824]/40 via-transparent to-[#1F4A2C]/50" aria-hidden="true"></div>
            <x-storefront.banner-spice />

            <div class="relative mx-auto grid max-w-7xl items-center gap-8 px-4 pb-8 pt-14 sm:gap-10 sm:pb-10 sm:pt-16 lg:grid-cols-2 lg:gap-12 lg:px-8 lg:pb-12 lg:pt-20">
                <div class="js-hero-animate max-w-xl">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#CA9636]">Rwamagana, Rwanda</p>
                    <h1 class="mt-4 text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl lg:text-[3.25rem]">
                        Taste the Passion. Fuel Your Flavour.
                    </h1>
                    <p class="mt-5 text-base leading-relaxed text-white/90 sm:text-lg">
                        Discover bold, authentic flavours crafted in Rwanda. From our signature Akanovela chilli products to refreshing passion fruit juice, <span class="font-semibold">muzarwa ltd</span> brings together locally sourced ingredients, quality craftsmanship, and the rich taste of Rwanda.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a
                            href="{{ route('storefront.products') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-[#CA9636] px-6 py-3 text-sm font-semibold text-[#3E3C38] shadow-sm transition duration-200 hover:bg-[#B07F28] hover:shadow-md active:scale-[0.98]"
                        >
                            Explore Our Products
                        </a>
                        <a
                            href="{{ route('storefront.about') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-white/35 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition duration-200 hover:border-white/50 hover:bg-white/10 active:scale-[0.98]"
                        >
                            Our Story
                        </a>
                        @guest
                            <a
                                href="{{ route('admin.login') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/25 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition duration-200 hover:border-white/40 hover:bg-white/20 active:scale-[0.98]"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd" />
                                </svg>
                                Log In
                            </a>
                        @endguest
                    </div>
                </div>

                <div class="js-hero-animate relative mx-auto w-full max-w-md lg:max-w-none" style="animation-delay: 120ms;">
                    <div class="pointer-events-none absolute -inset-4 rounded-full bg-[#CA9636]/15 blur-3xl" aria-hidden="true"></div>
                    <div class="relative mx-auto aspect-[5/4] w-full max-w-[24rem] lg:ml-auto lg:mr-0 lg:max-w-none">
                        <div class="mz-hero-float-a absolute left-[4%] top-[2%] z-10 w-[56%] overflow-hidden rounded-3xl bg-[#ede7dc] shadow-[0_18px_40px_rgba(0,0,0,0.35),0_40px_80px_-20px_rgba(0,0,0,0.55)] ring-1 ring-black/10">
                            <img
                                src="{{ asset('images/storefront/akanovela-chilli-sauce.jpg') }}"
                                alt="Akanovela Chilli Sauce"
                                width="480"
                                height="600"
                                class="aspect-[4/5] w-full object-cover object-bottom brightness-[0.88] contrast-[1.05]"
                            >
                        </div>
                        <div class="mz-hero-float-b absolute right-[2%] top-[8%] z-20 w-[52%] overflow-hidden rounded-3xl bg-[#ede7dc] shadow-[0_22px_48px_rgba(0,0,0,0.4),0_48px_90px_-18px_rgba(0,0,0,0.6)] ring-1 ring-black/10">
                            <img
                                src="{{ asset('images/storefront/akanovela-chilli-oil.jpg') }}"
                                alt="Akanovela Chilli Oil"
                                width="400"
                                height="500"
                                class="aspect-[4/5] w-full object-cover object-bottom brightness-[0.88] contrast-[1.05]"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Mission & Vision --}}
        <section class="border-b border-[#2A5C38]/10 bg-[#f8fafc]">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
                <div class="mx-auto max-w-2xl text-center" data-animate>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">What drives us</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Mission &amp; Vision</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Locally grown. Carefully crafted. Built to share authentic Rwandan flavour.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-2" data-stagger>
                    <article class="rounded-2xl border border-[#2A5C38]/15 bg-gradient-to-br from-[#E8F0EA] to-white p-8 shadow-sm ring-1 ring-[#2A5C38]/10 sm:p-10">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#2A5C38] text-white shadow-md shadow-[#2A5C38]/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Our Mission</p>
                                <h3 class="mt-1 text-xl font-bold tracking-tight text-slate-900">Why we make what we make</h3>
                            </div>
                        </div>
                        <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                            To create high-quality food and beverage products using locally sourced ingredients while creating value for farmers, customers, partners, and communities.
                        </p>
                    </article>

                    <article class="rounded-2xl border border-[#CA9636]/30 bg-gradient-to-br from-[#FBF6EA] to-white p-8 shadow-sm ring-1 ring-[#CA9636]/20 sm:p-10">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#CA9636] text-[#3E3C38] shadow-md shadow-[#CA9636]/25">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#CA9636]">Our Vision</p>
                                <h3 class="mt-1 text-xl font-bold tracking-tight text-slate-900">Where we are headed</h3>
                            </div>
                        </div>
                        <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                            To build one of East Africa's trusted food brands, known for authentic flavour, quality products, and meaningful local impact.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        {{-- Who we are --}}
        <section class="border-b border-[#2A5C38]/10 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
                <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div data-animate="left">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">About Us</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">Our Story</h2>
                        <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                            muzarwa LTD was founded in Rwamagana, Rwanda, with a vision to create food products that celebrate the country's rich agricultural resources and distinctive flavours.
                        </p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            What started with a passion for quality chilli products has grown into a food brand focused on creating memorable flavours from locally sourced ingredients.
                        </p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            Through our products, partnerships, and relationships with farmers, we aim to build a business that creates value from farm to consumer.
                        </p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            Today, our portfolio includes the Akanovela chilli range and refreshing passion fruit products, with ambitions to continue developing innovative food products for Rwanda and beyond.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a
                                href="{{ route('storefront.about') }}"
                                class="inline-flex items-center rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1F4A2C] active:scale-[0.98]"
                            >
                                Our story
                            </a>
                            <a
                                href="{{ route('storefront.contact') }}"
                                class="inline-flex items-center rounded-lg border border-slate-300/90 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 transition hover:border-[#2A5C38]/35 hover:bg-[#f7fbfc] active:scale-[0.98]"
                            >
                                Get in touch
                            </a>
                        </div>
                    </div>
                    <figure class="mx-auto w-full max-w-sm overflow-hidden rounded-2xl bg-[#e8eaee] shadow-xl shadow-slate-900/10 ring-1 ring-black/5 lg:mx-0 lg:justify-self-end" data-animate="right">
                        <img
                            src="{{ asset('images/storefront/who-we-are.jpg') }}"
                            alt="Akanovela muzarwa chilli sauce"
                            width="677"
                            height="903"
                            loading="lazy"
                            decoding="async"
                            class="aspect-[4/5] w-full object-cover object-bottom"
                        >
                    </figure>
                </div>
            </div>
        </section>

        {{-- Product highlights --}}
        <section class="border-b border-[#2A5C38]/10 bg-gradient-to-b from-[#E8F0EA] via-[#EEF3EC] to-[#F4F1EA]">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
                <div class="mx-auto max-w-2xl text-center" data-animate>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Crafted for flavour</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Product highlights</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">A Rwandan food brand built around chilli, fruit, and community.</p>
                </div>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 xl:grid-cols-3" data-stagger>
                    @forelse ($highlights as $product)
                        @include('storefront._shop-product-card', ['layout' => 'grid', 'bestSellerIds' => $bestSellerIds])
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white/80 px-6 py-12 text-center text-sm text-slate-600">
                            Products added in admin will appear here.
                        </div>
                    @endforelse
                </div>
                <div class="mt-12 flex flex-col items-center gap-3" data-animate>
                    <a
                        href="{{ route('storefront.shop') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2A5C38] px-7 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1F4A2C] hover:shadow-md active:scale-[0.98]"
                    >
                        Visit the shop for more products
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12" />
                        </svg>
                    </a>
                    <p class="text-xs text-slate-500">Browse the full catalog, filter by category, and order online.</p>
                </div>
            </div>
        </section>

        {{-- Best sellers --}}
        <section class="border-y border-[#2A5C38]/10 bg-gradient-to-b from-[#e4f0f2] via-[#ecf5f6] to-[#f2f8f9]">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-24">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-animate>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Customer favorites</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Best sellers</h2>
                    </div>
                    <a href="{{ route('storefront.shop') }}" class="text-sm font-semibold text-[#2A5C38] transition hover:text-[#1F4A2C]">Shop full catalog →</a>
                </div>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                    @forelse ($bestSellers as $product)
                        @include('storefront._shop-product-card', ['product' => $product, 'layout' => 'grid', 'bestSellerIds' => $bestSellerIds])
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white/80 px-6 py-12 text-center text-sm text-slate-600">
                            Best sellers will appear here once products have recorded sales.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Value proposition + image --}}
        <section class="border-y border-slate-200/60 bg-gradient-to-b from-white via-[#fafcfd] to-[#EEF3EC]">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-24">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center lg:gap-16">
                    <div class="min-w-0" data-animate="left">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Why muzarwa</p>
                        <h2 class="mt-3 max-w-xl text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Quality, sourcing, and packaging you can stand behind</h2>
                        <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
                            Standardized recipes, rigorous hygiene, and partnerships with local farmers mean reliable flavor for households and retail shelves alike.
                        </p>

                        <ul class="mt-10 grid gap-4 sm:grid-cols-2" data-stagger>
                            <li class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm ring-1 ring-slate-900/[0.03] transition duration-200 hover:border-[#2A5C38]/25 hover:shadow-md">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#2A5C38] text-white shadow-md shadow-[#2A5C38]/20">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.415 0l-3.25-3.25a1 1 0 111.414-1.42l2.543 2.544 6.543-6.544a1 1 0 011.415 0z" clip-rule="evenodd" /></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">Premium quality control</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Batch consistency and strict processing standards.</p>
                                </div>
                            </li>
                            <li class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm ring-1 ring-slate-900/[0.03] transition duration-200 hover:border-[#CA9636]/40 hover:shadow-md">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#fff4b8] to-[#CA9636] text-[#3E3C38] shadow-md shadow-[#c9a227]/20 ring-1 ring-white/50">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" /></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">Locally sourced chili</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Freshness and fair impact across the supply chain.</p>
                                </div>
                            </li>
                            <li class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm ring-1 ring-slate-900/[0.03] transition duration-200 hover:border-[#CA9636]/40 hover:shadow-md">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#fff4b8] to-[#CA9636] text-[#3E3C38] shadow-md shadow-[#c9a227]/20 ring-1 ring-white/50">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c.321-.772 1.443-.772 1.764 0l1.553 3.735a1 1 0 00.843.616l4.032.323c.833.067 1.17 1.108.536 1.654l-3.072 2.64a1 1 0 00-.323.997l.938 3.935c.194.815-.703 1.46-1.416 1.02l-3.455-2.11a1 1 0 00-1.043 0l-3.455 2.11c-.713.44-1.61-.205-1.416-1.02l.938-3.935a1 1 0 00-.323-.997l-3.072-2.64c-.634-.546-.297-1.587.536-1.654l4.032-.323a1 1 0 00.843-.616l1.553-3.735z" clip-rule="evenodd" /></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">Akanovela chilli range</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Sauce, oil, and fruit products for everyday cooking.</p>
                                </div>
                            </li>
                            <li class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-sm ring-1 ring-slate-900/[0.03] transition duration-200 hover:border-[#2A5C38]/25 hover:shadow-md">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#2A5C38] text-white shadow-md shadow-[#2A5C38]/20">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M1.503 4.75A1.75 1.75 0 013.253 3h13.494a1.75 1.75 0 011.75 1.75V15a1.75 1.75 0 01-1.75 1.75H3.253A1.75 1.75 0 011.503 15V4.75zM4.003 5.5a.25.25 0 00-.25.25v8.5c0 .138.112.25.25.25h12.494a.25.25 0 00.25-.25v-8.5a.25.25 0 00-.25-.25H4.003z" clip-rule="evenodd" /><path d="M5.503 7.25a.75.75 0 01.75-.75h7.494a.75.75 0 010 1.5H6.253a.75.75 0 01-.75-.75zm0 3a.75.75 0 01.75-.75h4.494a.75.75 0 010 1.5H6.253a.75.75 0 01-.75-.75z" /></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">Safe, attractive packaging</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Hygiene and shelf presence built for trust.</p>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-10 flex flex-wrap gap-3">
                            <a href="{{ route('storefront.shop') }}" class="inline-flex rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-[#2A5C38]/20 transition hover:bg-[#1F4A2C] active:scale-[0.98]">Shop now</a>
                            <a href="{{ route('storefront.contact') }}" class="inline-flex rounded-lg border border-slate-300/90 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 transition hover:border-[#2A5C38]/35 hover:bg-[#f7fbfc] active:scale-[0.98]">Request wholesale info</a>
                        </div>
                    </div>
                    <figure class="overflow-hidden rounded-2xl bg-[#c0c6d4] shadow-xl shadow-slate-900/10 ring-1 ring-black/5" data-animate="right">
                        <img
                            src="{{ asset('images/storefront/product-range.jpg') }}"
                            alt="muzarwa Itunda squash and Akanovela chilli range"
                            width="768"
                            height="714"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-contain object-bottom"
                        >
                    </figure>
                </div>
            </div>
        </section>

        {{-- Gallery --}}
        <section class="border-y border-slate-200/70 bg-[#f8fafc]">
            <div class="mx-auto max-w-7xl px-4 py-14 lg:px-8 lg:py-20">
                <div class="mx-auto max-w-2xl text-center" data-animate>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Behind the brand</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Gallery</h2>
                </div>
                @if ($galleryAlbums->isNotEmpty())
                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                        @foreach ($galleryAlbums as $album)
                            @include('storefront._gallery-album-card', [
                                'album' => $album,
                                'albumNumber' => $loop->iteration,
                            ])
                        @endforeach
                    </div>
                    <div class="mt-10 flex justify-center">
                        <a
                            href="{{ route('storefront.gallery') }}"
                            class="inline-flex items-center rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-medium text-slate-800 transition hover:border-slate-400 hover:bg-slate-50"
                        >
                            View full gallery
                        </a>
                    </div>
                @else
                    <div class="mt-10 rounded-xl border border-dashed border-slate-300/90 bg-white px-6 py-10 text-center text-sm text-slate-500">
                        Upload photos in Admin → E-Commerce → Catalog → Gallery.
                    </div>
                @endif
            </div>
        </section>

        {{-- Testimonials --}}
        <section class="border-y border-[#CA9636]/25 bg-gradient-to-b from-[#fdf5eb] via-[#fef7ef] to-[#faf0e4]">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-24">
                <div class="mx-auto max-w-2xl text-center" data-animate>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Sample feedback</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">What Our Customers Say</h2>
                    <p class="mt-3 text-sm text-slate-600">Placeholder notes for the brand voice. Replace these in admin once verified customer quotes are available.</p>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-3" data-stagger>
                    <figure class="flex h-full flex-col rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                        <blockquote class="flex-1 text-sm leading-relaxed text-slate-600">“The chilli sauce has a warm, balanced heat that works well with everyday meals.”</blockquote>
                        <figcaption class="mt-5 text-sm font-semibold text-slate-900">Sample customer note</figcaption>
                    </figure>
                    <figure class="flex h-full flex-col rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                        <blockquote class="flex-1 text-sm leading-relaxed text-slate-600">“Clear labelling and consistent flavour make it easy to recommend on the shelf.”</blockquote>
                        <figcaption class="mt-5 text-sm font-semibold text-slate-900">Sample retail note</figcaption>
                    </figure>
                    <figure class="flex h-full flex-col rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                        <blockquote class="flex-1 text-sm leading-relaxed text-slate-600">“muzarwa is building products around local ingredients and farmer partnerships.”</blockquote>
                        <figcaption class="mt-5 text-sm font-semibold text-slate-900">Sample partner note</figcaption>
                    </figure>
                </div>
            </div>
        </section>

        {{-- Partners --}}
        <section class="border-b border-[#2A5C38]/10 bg-gradient-to-b from-[#e8f2f4] to-[#f4f9fa]">
            <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-animate>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Trusted presence</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Partner supermarkets</h2>
                    </div>
                </div>
                <div class="mt-10 flex flex-wrap items-center gap-4" data-stagger>
                    @foreach ($partners as $partner)
                        @if ($partner->logo_path)
                            @php($logo = '<img src="' . asset('storage/' . $partner->logo_path) . '" alt="' . e($partner->name) . '" title="' . e($partner->name) . '" class="h-12 w-auto max-w-[160px] object-contain">')
                        @else
                            @php($logo = '<span class="rounded-xl border border-white/80 bg-white/90 px-5 py-2.5 text-sm font-medium text-slate-800 shadow-sm shadow-slate-900/[0.04] transition duration-200 hover:border-[#2A5C38]/30 hover:bg-white hover:shadow-md">' . e($partner->name) . '</span>')
                        @endif

                        @if ($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer" class="transition duration-200 hover:opacity-80">{!! $logo !!}</a>
                        @else
                            {!! $logo !!}
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Closing CTA --}}
        <section class="border-t border-[#1F4A2C] bg-[#2A5C38] px-4 py-16 text-white lg:px-8 lg:py-20">
            <div class="mx-auto flex max-w-7xl flex-col items-start gap-8 md:flex-row md:items-center md:justify-between" data-animate>
                <div class="max-w-xl">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Taste the passion. Fuel your flavour.</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-200 sm:text-base">
                        Shop the live catalog, or get in touch about wholesale, events, and farmer partnerships.
                    </p>
                </div>
                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap">
                    <a href="{{ route('storefront.shop') }}" class="inline-flex items-center justify-center rounded-lg bg-[#CA9636] px-6 py-3 text-sm font-semibold text-[#3E3C38] shadow-sm transition hover:bg-[#B07F28] active:scale-[0.98]">Shop now</a>
                    <a href="{{ route('storefront.contact') }}" class="inline-flex items-center justify-center rounded-lg border border-white/35 px-6 py-3 text-sm font-semibold transition hover:bg-white/10 active:scale-[0.98]">Contact us</a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.storefront>
