<x-layouts.storefront title="Our Products" seo-description="Crafted for flavour. Made with care. Explore muzarwa chilli sauces, chilli oil, and passion fruit juice.">
    <section class="relative overflow-hidden border-b border-[#1F4A2C]/30 bg-[#2A5C38] text-white">
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:py-16 lg:px-8">
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

    <div class="mx-auto max-w-7xl space-y-10 px-4 py-12 lg:px-8 lg:py-16">
        <article id="akanovela-chilli-sauce" class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
            <div class="grid gap-8 p-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#BD4B2D]">Chilli</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Akanovela Chilli Sauce</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        A bold, flavour-rich chilli sauce with balanced heat, crafted using carefully selected chillies and natural spices.
                    </p>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-500">Perfect for</p>
                    <ul class="mt-2 flex flex-wrap gap-2 text-sm text-slate-700">
                        @foreach (['Dipping', 'Marinades', 'Cooking', 'Grilled foods', 'Everyday meals'] as $use)
                            <li class="rounded-full bg-[#E8F0EA] px-3 py-1">{{ $use }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('storefront.shop') }}" class="mt-6 inline-flex rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Explore Product</a>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-[#E8F0EA] to-[#F7EFD4] p-8 text-sm text-slate-600">
                    Shop the live catalog for current sizes and availability.
                </div>
            </div>
        </article>

        <article id="akanovela-chilli-oil" class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
            <div class="grid gap-8 p-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#CA9636]">Oil</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Akanovela Chilli Oil</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        A spicy and aromatic chilli oil designed to add depth, flavour, and character to your favourite meals.
                    </p>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-500">Perfect for</p>
                    <ul class="mt-2 flex flex-wrap gap-2 text-sm text-slate-700">
                        @foreach (['Pizza', 'Pasta', 'Grilled meat', 'Salads', 'Stir-fries', 'Drizzling'] as $use)
                            <li class="rounded-full bg-[#F7EFD4] px-3 py-1">{{ $use }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('storefront.shop') }}" class="mt-6 inline-flex rounded-lg bg-[#2A5C38] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Explore Product</a>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-[#F7EFD4] to-[#E8F0EA] p-8 text-sm text-slate-600">
                    Drizzle, cook, or finish a dish with a little extra heat.
                </div>
            </div>
        </article>

        <article id="passion-fruit-juice" class="scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
            <div class="grid gap-8 p-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#2A5C38]">Fruit</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Passion Fruit Juice</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        A refreshing passion fruit drink made from carefully selected fruit, delivering a naturally vibrant flavour.
                    </p>
                    <a href="{{ route('storefront.shop') }}" class="mt-6 inline-flex rounded-lg bg-[#CA9636] px-5 py-2.5 text-sm font-semibold text-[#3E3C38] hover:bg-[#B07F28]">Explore Product</a>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-[#EDE7DC] to-[#E8F0EA] p-8 text-sm text-slate-600">
                    Browse the shop for current fruit products in the muzarwa catalog.
                </div>
            </div>
        </article>
    </div>
</x-layouts.storefront>
