<x-layouts.storefront title="Our Products" seo-description="Crafted for flavour. Made with care. Explore muzarwa chilli sauces, chilli oil, and passion fruit juice.">
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
                    <li class="text-white/90" aria-current="page">Products</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Our Products</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-100/90 sm:text-base">
                Crafted for flavour. Made with care.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl space-y-8 px-4 py-12 lg:px-8 lg:py-16">
        <article id="akanovela-chilli-sauce" class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200/90 bg-white" data-animate="left">
            <div class="grid lg:grid-cols-2 lg:items-center">
                <x-storefront.product-shot src="images/storefront/akanovela-chilli-sauce.jpg" alt="Akanovela Chilli Sauce jar" :cover="true" class="aspect-[4/3] lg:aspect-square" />
                <div class="p-8 lg:p-10">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#BD4B2D]">Chilli</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Akanovela Chilli Sauce</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        A bold, flavour-rich chilli sauce with balanced heat, crafted using carefully selected chillies and natural spices.
                    </p>
                    <ul class="mt-5 flex flex-wrap gap-2 text-sm text-slate-700">
                        @foreach (['Dipping', 'Marinades', 'Cooking', 'Grilled foods', 'Everyday meals'] as $use)
                            <li class="rounded-full bg-[#E8F0EA] px-3 py-1">{{ $use }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('storefront.shop') }}" class="mt-6 inline-flex rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Explore Product</a>
                </div>
            </div>
        </article>

        <article id="akanovela-chilli-oil" class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200/90 bg-white" data-animate="right">
            <div class="grid lg:grid-cols-2 lg:items-center">
                <x-storefront.product-shot src="images/storefront/akanovela-chilli-oil.jpg" alt="Akanovela Chilli Oil bottle" :cover="true" class="aspect-[4/3] lg:order-2 lg:aspect-square" />
                <div class="p-8 lg:p-10">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#CA9636]">Oil</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Akanovela Chilli Oil</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        A spicy and aromatic chilli oil designed to add depth, flavour, and character to your favourite meals.
                    </p>
                    <ul class="mt-5 flex flex-wrap gap-2 text-sm text-slate-700">
                        @foreach (['Pizza', 'Pasta', 'Grilled meat', 'Salads', 'Stir-fries', 'Drizzling'] as $use)
                            <li class="rounded-full bg-[#F7EFD4] px-3 py-1">{{ $use }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('storefront.shop') }}" class="mt-6 inline-flex rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Explore Product</a>
                </div>
            </div>
        </article>

        <article id="passion-fruit-juice" class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200/90 bg-white" data-animate="left">
            <div class="grid lg:grid-cols-2 lg:items-center">
                <x-storefront.product-shot src="images/storefront/itunda-squash.jpg" alt="Itunda Maracuja Squash 1 litre bottle" :cover="true" class="aspect-[4/3] lg:aspect-square" />
                <div class="p-8 lg:p-10">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#2A5C38]">Fruit</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Itunda Passion Fruit Squash</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        A refreshing passion fruit drink made from carefully selected fruit, delivering a naturally vibrant flavour.
                    </p>
                    <a href="{{ route('storefront.shop') }}" class="mt-6 inline-flex rounded-lg bg-[#CA9636] px-5 py-2.5 text-sm font-semibold text-[#3E3C38] hover:bg-[#B07F28]">Explore Product</a>
                </div>
            </div>
        </article>
    </div>
</x-layouts.storefront>
