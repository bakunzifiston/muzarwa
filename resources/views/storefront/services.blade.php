<x-layouts.storefront title="Partner With muzarwa" seo-description="Partner with muzarwa on wholesale, co-branding, distribution, events, and farmer partnerships.">
    <section class="relative overflow-hidden border-b border-[#1F4A2C]/30 bg-[#2A5C38] text-white">
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:py-16 lg:px-8">
            <nav class="text-xs font-medium text-[#CA9636]/95" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <li><a href="{{ route('storefront.home') }}" class="transition hover:text-white">Home</a></li>
                    <li class="text-white/50" aria-hidden="true">/</li>
                    <li class="text-white/90" aria-current="page">Services</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Partner With muzarwa</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-100/90 sm:text-base">
                Beyond our products, we work with businesses, distributors, retailers, hospitality organizations, and farmers to create opportunities around locally produced food products.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-16">
        <div class="grid gap-6 md:grid-cols-2">
            @php
                $services = [
                    ['title' => 'Wholesale Supply', 'body' => 'We supply restaurants, cafés, supermarkets, retailers, and hospitality businesses with our products for commercial use and resale.'],
                    ['title' => 'White Label & Co-Branding', 'body' => 'We can work with suitable business partners to develop customized or co-branded food products according to agreed requirements.'],
                    ['title' => 'Export & Distribution Partnerships', 'body' => 'We work with distribution partners interested in bringing Rwandan food products to new markets.'],
                    ['title' => 'Events & Product Sampling', 'body' => 'Our products can be featured through food events, exhibitions, cooking demonstrations, tasting sessions, and promotional activations.'],
                    ['title' => 'Farmer Partnerships', 'body' => 'We work with local farmers and agricultural partners to strengthen the supply of quality raw materials and create sustainable value within the local food system.'],
                ];
            @endphp
            @foreach ($services as $index => $service)
                <article class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm {{ $loop->last ? 'md:col-span-2' : '' }}">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#2A5C38]">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                    <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $service['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $service['body'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mt-12 rounded-2xl border border-[#2A5C38]/20 bg-[#E8F0EA] px-6 py-8 text-center">
            <p class="text-sm text-slate-700">Ready to talk through a partnership?</p>
            <a href="{{ route('storefront.contact') }}" class="mt-4 inline-flex rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Get in touch</a>
        </div>
    </div>
</x-layouts.storefront>
