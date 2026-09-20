<x-layouts.storefront title="Our Story" seo-description="muzarwa began in Rwamagana with a vision to capture the authentic flavours of Rwanda in every bottle.">
    <div id="storefront-about">
        <section class="relative overflow-hidden text-white">
            <div class="absolute inset-0">
                <img src="{{ asset('images/storefront/about-team.png') }}" alt="" class="h-full w-full object-cover" aria-hidden="true">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#2A5C38]/90 via-[#2A5C38]/80 to-[#163824]/95"></div>
            <div class="relative mx-auto flex min-h-[min(52vh,22rem)] max-w-7xl flex-col justify-end px-4 pb-16 pt-20 sm:pb-20 lg:px-8 lg:pb-24 lg:pt-28">
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#CA9636]">About us</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-bold leading-tight sm:text-5xl lg:text-[3.25rem]">
                    Our Story
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/90">
                    muzarwa began in Rwamagana with a vision to capture the rich and authentic flavours of Rwanda in every bottle.
                </p>
            </div>
        </section>

        <section class="border-b border-slate-200/80 bg-white">
            <div class="mx-auto max-w-3xl px-4 py-14 lg:px-8 lg:py-20">
                <p class="text-base leading-relaxed text-slate-600 sm:text-lg">
                    What started with a passion for great taste has grown into a brand focused on quality, local ingredients, and meaningful partnerships with farmers.
                </p>
                <p class="mt-6 text-base leading-relaxed text-slate-600 sm:text-lg">
                    Through collaboration with local producers, muzarwa creates chilli sauces, infused oils, and fruit-based products designed to bring authentic Rwandan flavour to homes, restaurants, and markets.
                </p>
            </div>
        </section>

        <section class="border-b border-[#2A5C38]/10 bg-gradient-to-b from-[#F4F1EA] via-white to-[#fffef8]">
            <div class="mx-auto max-w-7xl px-4 py-14 lg:px-8 lg:py-20">
                <div class="grid gap-6 md:grid-cols-2">
                    <article class="rounded-2xl border border-[#2A5C38]/20 bg-white p-8 shadow-sm">
                        <h2 class="text-lg font-bold tracking-tight text-[#163824]">Vision</h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            To become a trusted East African brand for bold, natural chilli products and refreshing fruit-based products, while promoting local value creation and farmer empowerment.
                        </p>
                    </article>
                    <article class="rounded-2xl border border-[#CA9636]/30 bg-white p-8 shadow-sm">
                        <h2 class="text-lg font-bold tracking-tight text-[#3E3C38]">Mission</h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-700">
                            To create high-quality, delicious food products using locally sourced ingredients while supporting Rwandan farmers and delivering an exceptional customer experience.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="border-t border-[#1F4A2C] bg-[#2A5C38] px-4 py-14 text-white lg:px-8 lg:py-16">
            <div class="mx-auto flex max-w-7xl flex-col items-center gap-8 text-center md:flex-row md:justify-between md:text-left">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold sm:text-3xl">Explore the range</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-200 md:text-base">
                        Discover Akanovela chilli products and fruit-based flavours, or talk to us about working together.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-3 md:justify-end">
                    <a href="{{ route('storefront.products') }}" class="inline-flex items-center rounded-lg bg-[#CA9636] px-5 py-3 text-sm font-semibold text-[#3E3C38] hover:bg-[#B07F28]">Explore Our Products</a>
                    <a href="{{ route('storefront.services') }}" class="inline-flex items-center rounded-lg border border-white/35 px-5 py-3 text-sm font-semibold hover:bg-white/10">Partner With muzarwa</a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.storefront>
