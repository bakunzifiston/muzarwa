<x-layouts.storefront title="Our Story" seo-description="muzarwa began in Rwamagana with a vision to capture authentic Rwandan flavour in every bottle, and to partner with businesses, distributors, and farmers.">
    <div id="storefront-about">
        <section class="relative overflow-hidden border-b border-[#1F4A2C]/30 bg-[#2A5C38] text-white">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#CA9636]/15 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-white/5 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#163824]/40 via-transparent to-[#1F4A2C]/50" aria-hidden="true"></div>
            <x-storefront.banner-spice />
            <div class="js-hero-animate relative mx-auto max-w-7xl px-4 py-12 sm:py-16 lg:px-8">
                <nav class="text-xs font-medium text-[#CA9636]/95" aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <li><a href="{{ route('storefront.home') }}" class="transition hover:text-white">Home</a></li>
                        <li class="text-white/50" aria-hidden="true">/</li>
                        <li class="text-white/90" aria-current="page">About</li>
                    </ol>
                </nav>
                <p class="mt-6 text-sm font-medium uppercase tracking-[0.2em] text-[#CA9636]">Rwamagana, Rwanda</p>
                <h1 class="mt-3 max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">Our Story</h1>
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-slate-100/90 sm:text-base">
                    muzarwa began in Rwamagana with a vision to capture the rich and authentic flavours of Rwanda in every bottle.
                </p>
            </div>
        </section>

        <section class="border-b border-slate-200/80 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-14 lg:px-8 lg:py-20">
                <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div data-animate="left">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Who we are</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Crafted in Rwanda, made with care</h2>
                        <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                            What started with a passion for great taste has grown into a brand focused on quality, local ingredients, and meaningful partnerships with farmers.
                        </p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            Through collaboration with local producers, muzarwa creates chilli sauces, infused oils, and fruit-based products designed to bring authentic Rwandan flavour to homes, restaurants, and markets.
                        </p>
                    </div>
                    <figure class="overflow-hidden rounded-2xl bg-[#c0c6d4] ring-1 ring-black/5" data-animate="right">
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

        <section class="border-b border-slate-200/80 bg-[#f8fafc]">
            <div class="mx-auto max-w-7xl px-4 py-14 lg:px-8 lg:py-20">
                <div class="grid overflow-hidden rounded-2xl border border-slate-200/90 bg-white md:grid-cols-2" data-stagger>
                    <article class="border-b border-slate-200/90 p-8 md:border-b-0 md:border-r md:p-10">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Vision</p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            To become a trusted East African brand for bold, natural chilli products and refreshing fruit-based products, while promoting local value creation and farmer empowerment.
                        </p>
                    </article>
                    <article class="p-8 md:p-10">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#CA9636]">Mission</p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            To create high-quality, delicious food products using locally sourced ingredients while supporting Rwandan farmers and delivering an exceptional customer experience.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section id="partner" class="scroll-mt-28 border-b border-slate-200/80 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-14 lg:px-8 lg:py-20">
                <div class="max-w-2xl" data-animate>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Partnerships</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Partner With muzarwa</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                        Beyond our products, we work with businesses, distributors, retailers, hospitality organizations, and farmers to create opportunities around locally produced food products.
                    </p>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                    @php
                        $services = [
                            ['title' => 'Wholesale Supply', 'body' => 'We supply restaurants, cafés, supermarkets, retailers, and hospitality businesses with our products for commercial use and resale.'],
                            ['title' => 'White Label & Co-Branding', 'body' => 'We can work with suitable business partners to develop customized or co-branded food products according to agreed requirements.'],
                            ['title' => 'Export & Distribution', 'body' => 'We work with distribution partners interested in bringing Rwandan food products to new markets.'],
                            ['title' => 'Events & Sampling', 'body' => 'Our products can be featured through food events, exhibitions, cooking demonstrations, tasting sessions, and promotional activations.'],
                            ['title' => 'Farmer Partnerships', 'body' => 'We work with local farmers and agricultural partners to strengthen the supply of quality raw materials and create sustainable value within the local food system.'],
                        ];
                    @endphp
                    @foreach ($services as $index => $service)
                        <article class="rounded-2xl border border-slate-200/90 bg-[#f8fafc] p-6">
                            <p class="text-xs font-semibold text-[#2A5C38]">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                            <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $service['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $service['body'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="border-t border-[#1F4A2C] bg-[#2A5C38] px-4 py-14 text-white lg:px-8 lg:py-16">
            <div class="mx-auto flex max-w-7xl flex-col items-start gap-8 md:flex-row md:items-center md:justify-between" data-animate>
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Ready to talk through a partnership?</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-200 sm:text-base">
                        Explore the range, or get in touch about wholesale, events, and working together.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('storefront.products') }}" class="inline-flex items-center rounded-lg bg-[#CA9636] px-5 py-3 text-sm font-semibold text-[#3E3C38] hover:bg-[#B07F28]">Explore Our Products</a>
                    <a href="{{ route('storefront.contact') }}" class="inline-flex items-center rounded-lg border border-white/35 px-5 py-3 text-sm font-semibold hover:bg-white/10">Get in touch</a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.storefront>
