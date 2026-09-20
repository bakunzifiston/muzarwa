<x-layouts.storefront title="Gallery">
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
                    <li class="text-white/90" aria-current="page">Gallery</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Gallery</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-100/90 sm:text-base">
                Browse albums from production, events, and life at muzarwa.
            </p>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-16">
            @if ($albums->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                    @foreach ($albums as $album)
                        @include('storefront._gallery-album-card', ['album' => $album])
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-200 bg-[#f8fafc] px-6 py-16 text-center text-sm text-slate-500">
                    No gallery albums yet.
                </div>
            @endif
        </div>
    </section>
</x-layouts.storefront>
