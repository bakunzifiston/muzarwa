<x-layouts.storefront :title="$details->name">
    @php
        $canAddToCart = (float) ($details->sellable_qty ?? 0) >= 1;
        $minQty = (int) ($details->min_order_qty ?? 5);
        $inquiryUrl = route('storefront.contact') . '?subject=' . urlencode('Product Inquiry: ' . $details->name);
    @endphp

    <section class="border-b border-slate-200/80 bg-[#f8fafc]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:py-12 lg:px-8">
            <nav class="text-xs font-medium text-[#2A5C38]" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <li><a href="{{ route('storefront.home') }}" class="text-slate-600 transition hover:text-[#2A5C38]">Home</a></li>
                    <li class="text-slate-400" aria-hidden="true">/</li>
                    <li><a href="{{ route('storefront.shop') }}" class="text-slate-600 transition hover:text-[#2A5C38]">Shop</a></li>
                    <li class="text-slate-400" aria-hidden="true">/</li>
                    <li class="font-semibold text-slate-900" aria-current="page">{{ $details->name }}</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $details->name }}</h1>
            <x-storefront.shop-subnav />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 lg:px-8 lg:py-14">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif

        <div class="grid gap-10 lg:grid-cols-2 lg:items-start">
            <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white">
                @if ($details->image_path)
                    <img src="{{ $details->image_url }}" alt="{{ $details->name }}" class="aspect-square w-full object-cover">
                @else
                    <div class="flex aspect-square items-center justify-center bg-[#f8fafc] text-sm text-slate-500">
                        No product image
                    </div>
                @endif
            </div>
            <div>
                <p class="text-sm leading-relaxed text-slate-600">{{ $details->description ?: 'A muzarwa product, crafted for flavour.' }}</p>
                <div class="mt-6 space-y-2 text-sm text-slate-700">
                    <p><span class="font-medium text-slate-900">Price:</span> RWF {{ number_format((float) $details->price, 2) }}</p>
                    <p><span class="font-medium text-slate-900">Barcode:</span> {{ $details->barcode }}</p>
                    <p><span class="font-medium text-slate-900">Minimum online order:</span> {{ $minQty }} units</p>
                </div>

                @if ($canAddToCart)
                    <div class="mt-6 flex gap-3">
                        <form method="POST" action="{{ route('storefront.cart.add', $details->id) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="number" min="1" name="quantity" value="1" class="w-16 rounded-lg border border-slate-300 px-2 py-2 text-sm">
                            <button class="rounded-lg bg-[#2A5C38] px-4 py-2 text-sm font-medium text-white hover:bg-[#1F4A2C]">Add to Cart</button>
                        </form>
                        <form method="POST" action="{{ route('storefront.wishlist.add', $details->id) }}">
                            @csrf
                            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Wishlist</button>
                        </form>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Need fewer than {{ $minQty }} units? Find us at
                        <a href="#partner-stores" class="font-medium text-[#2A5C38] underline decoration-[#2A5C38]/30 underline-offset-2 hover:decoration-[#2A5C38]">partner supermarkets</a> near you.
                    </p>
                @else
                    <div class="mt-6 rounded-xl border border-[#2A5C38]/15 bg-[#E8F0EA] p-5">
                        <p class="text-sm font-medium text-slate-800">This product is currently made to order.</p>
                        <p class="mt-1 text-sm text-slate-600">Submit an inquiry and our team will get back to you within 24 hours.</p>
                        <a
                            href="{{ $inquiryUrl }}"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[#CA9636] px-5 py-2.5 text-sm font-semibold text-[#3E3C38] shadow-sm transition hover:bg-[#B07F28]"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            Inquire About This Product
                        </a>
                    </div>
                    <form method="POST" action="{{ route('storefront.wishlist.add', $details->id) }}" class="mt-3">
                        @csrf
                        <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Add to Wishlist</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Partner stores section --}}
        @if (isset($partners) && $partners->isNotEmpty())
            <div id="partner-stores" class="mt-10 rounded-2xl border border-slate-200/90 bg-white p-6">
                <h3 class="text-base font-semibold text-slate-900">Find us at partner supermarkets</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($partners as $partner)
                        @if ($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-[#2A5C38]/30 hover:bg-white hover:shadow-sm">{{ $partner->name }}</a>
                        @else
                            <span class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700">{{ $partner->name }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-8 grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6">
                <h2 class="text-base font-semibold text-slate-900">Product variants</h2>
                <div class="mt-3 space-y-2 text-sm text-slate-700">
                    @forelse ($variants as $variant)
                        <p>{{ $variant->name }} ({{ $variant->barcode }})</p>
                    @empty
                        <p>No additional variants available yet.</p>
                    @endforelse
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6">
                <h2 class="text-base font-semibold text-slate-900">Reviews & ratings</h2>
                <div class="mt-3 space-y-3 text-sm">
                    @forelse ($reviews as $review)
                        <article class="rounded-lg bg-slate-50 p-3">
                            <p class="font-medium">{{ $review->customer_name }} - {{ $review->rating }}/5</p>
                            <p class="text-slate-600">{{ $review->comment }}</p>
                        </article>
                    @empty
                        <p class="text-slate-600">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layouts.storefront>
