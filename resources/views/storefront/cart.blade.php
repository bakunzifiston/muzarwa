<x-layouts.storefront title="Your Cart">
    <section class="border-b border-slate-200/80 bg-[#f8fafc]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:py-12 lg:px-8">
            <nav class="text-xs font-medium text-[#2A5C38]" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <li><a href="{{ route('storefront.home') }}" class="text-slate-600 transition hover:text-[#2A5C38]">Home</a></li>
                    <li class="text-slate-400" aria-hidden="true">/</li>
                    <li><a href="{{ route('storefront.shop') }}" class="text-slate-600 transition hover:text-[#2A5C38]">Shop</a></li>
                    <li class="text-slate-400" aria-hidden="true">/</li>
                    <li class="font-semibold text-slate-900" aria-current="page">Cart</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Your cart</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Review your items and quantities before checkout.
            </p>
            <x-storefront.shop-subnav />
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 lg:flex lg:gap-10 lg:px-8 lg:py-14">
        <div class="min-w-0 flex-1">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            @if ($rows->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-16 text-center">
                    <p class="text-lg font-semibold text-slate-900">Your cart is empty</p>
                    <p class="mt-2 text-sm text-slate-600">Add products from the shop and they will appear here.</p>
                    <a href="{{ route('storefront.shop') }}" class="mt-8 inline-flex rounded-xl bg-[#2A5C38] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1F4A2C]">
                        Browse products
                    </a>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach ($rows as $row)
                        @php
                            $product = $row['product'];
                            $qty = (int) $row['quantity'];
                            $unit = (float) $row['unit_price'];
                            $line = (float) $row['line_total'];
                            $available = max(0, (float) ($product->sellable_qty ?? 0));
                            $minQty = (int) ($product->min_order_qty ?? 5);
                            $exceedsStock = $available > 0 ? $qty > $available : true;
                            $canDec = $qty > 1;
                        @endphp
                        <li class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white">
                            <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:gap-5 sm:p-5">
                                <a href="{{ route('storefront.product', $product->id) }}" class="relative shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200/80 transition hover:ring-[#2A5C38]/25 sm:h-28 sm:w-28">
                                    @if ($product->image_path)
                                        <img
                                            src="{{ $product->image_url }}"
                                            alt="{{ $product->name }}"
                                            width="224"
                                            height="224"
                                            class="h-32 w-full object-cover transition duration-300 hover:scale-[1.02] sm:h-28 sm:w-28"
                                        >
                                    @else
                                        <div class="flex h-32 w-full items-center justify-center text-xs text-slate-500 sm:h-28 sm:w-28">No image</div>
                                    @endif
                                </a>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <a href="{{ route('storefront.product', $product->id) }}" class="text-lg font-semibold text-slate-900 transition hover:text-[#2A5C38]">{{ $product->name }}</a>
                                            @if (!empty($product->type))
                                                <p class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">{{ $product->type }}</p>
                                            @endif
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <p class="text-sm text-slate-500">Line total</p>
                                            <p class="text-xl font-bold text-[#2A5C38] tabular-nums">RWF {{ number_format($line, 2) }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Quantity</span>
                                            <div class="inline-flex items-stretch rounded-xl border border-slate-200 bg-slate-50/80 shadow-sm">
                                                <form method="POST" action="{{ route('storefront.cart.update', $product->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ max(1, $qty - 1) }}">
                                                    <button
                                                        type="submit"
                                                        class="flex h-10 w-10 items-center justify-center rounded-l-xl border-r border-slate-200 text-slate-600 transition hover:bg-white hover:text-[#2A5C38] disabled:cursor-not-allowed disabled:opacity-40"
                                                        aria-label="Decrease quantity"
                                                        @disabled(!$canDec)
                                                    >
                                                        −
                                                    </button>
                                                </form>
                                                <span class="flex min-w-[2.75rem] items-center justify-center px-2 text-sm font-semibold tabular-nums text-slate-900" aria-live="polite">{{ $qty }}</span>
                                                <form method="POST" action="{{ route('storefront.cart.update', $product->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $qty + 1 }}">
                                                    <button
                                                        type="submit"
                                                        class="flex h-10 w-10 items-center justify-center rounded-r-xl border-l border-slate-200 text-slate-600 transition hover:bg-white hover:text-[#2A5C38]"
                                                        aria-label="Increase quantity"
                                                    >
                                                        +
                                                    </button>
                                                </form>
                                            </div>
                                            <form method="POST" action="{{ route('storefront.cart.update', $product->id) }}" class="flex flex-wrap items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <label class="sr-only" for="qty-{{ $product->id }}">Set quantity directly</label>
                                                <input
                                                    id="qty-{{ $product->id }}"
                                                    type="number"
                                                    name="quantity"
                                                    min="1"
                                                    value="{{ $qty }}"
                                                    class="w-16 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-center text-sm font-medium tabular-nums focus:border-[#2A5C38]/40 focus:outline-none focus:ring-2 focus:ring-[#2A5C38]/20 sm:w-[4.25rem]"
                                                >
                                                <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-[#2A5C38]/30 hover:bg-[#2A5C38]/5">
                                                    Update
                                                </button>
                                            </form>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                            <div class="text-sm text-slate-600 tabular-nums">
                                                @ RWF {{ number_format($unit, 2) }} <span class="text-slate-400">each</span>
                                            </div>
                                            <form method="POST" action="{{ route('storefront.cart.update', $product->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="0">
                                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-700 underline decoration-rose-200 underline-offset-2 transition hover:bg-rose-50 hover:text-rose-800">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    @if ($exceedsStock)
                                        <div class="mt-3 rounded-lg border border-[#CA9636]/30 bg-[#F7EFD4] px-3 py-2.5">
                                            <p class="text-xs font-medium text-slate-700">
                                                For this quantity, submit an inquiry — our team will confirm availability and get back to you within 24 hours.
                                            </p>
                                            <a
                                                href="{{ route('storefront.contact') }}?subject={{ urlencode('Order Inquiry: ' . $product->name . ' (' . $qty . ' units)') }}"
                                                class="mt-2 inline-flex items-center gap-1.5 rounded-md bg-[#CA9636] px-3 py-1.5 text-xs font-semibold text-[#3E3C38] shadow-sm transition hover:bg-[#B07F28]"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                                Inquire
                                            </a>
                                        </div>
                                    @endif

                                    @if ($qty < $minQty && isset($partners) && $partners->isNotEmpty())
                                        <div class="mt-3 rounded-lg border border-[#2A5C38]/10 bg-[#E8F0EA] px-3 py-2.5">
                                            <p class="text-xs font-medium text-slate-700">
                                                For orders under <strong>{{ $minQty }} units</strong>, find this product at our partner supermarkets:
                                            </p>
                                            <div class="mt-2 flex flex-wrap gap-1.5">
                                                @foreach ($partners as $partner)
                                                    @if ($partner->website_url)
                                                        <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="rounded-md border border-white/80 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 shadow-sm transition hover:border-[#2A5C38]/30 hover:shadow">{{ $partner->name }}</a>
                                                    @else
                                                        <span class="rounded-md border border-white/80 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 shadow-sm">{{ $partner->name }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm">
                    <a href="{{ route('storefront.shop') }}" class="font-semibold text-[#2A5C38] underline-offset-2 transition hover:underline">&larr; Continue shopping</a>
                    @if ($rows->count() > 0)
                        <p class="text-slate-600">{{ $rows->count() }} {{ \Illuminate\Support\Str::plural('item', $rows->count()) }} in cart</p>
                    @endif
                </div>
            @endif
        </div>

        @if ($rows->isNotEmpty())
            <aside class="mt-10 lg:mt-0 lg:w-[340px] lg:shrink-0">
                <div class="lg:sticky lg:top-[5.25rem]">
                    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white">
                        <div class="border-b border-slate-100 px-5 py-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Summary</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900">Order totals</p>
                        </div>
                        <div class="space-y-3 px-5 py-5">
                            @foreach ($rows as $row)
                                <div class="flex gap-3 text-sm">
                                    <span class="min-w-0 flex-1 truncate text-slate-700">{{ $row['product']->name }}</span>
                                    <span class="shrink-0 tabular-nums text-slate-600">× {{ (int) $row['quantity'] }}</span>
                                    <span class="shrink-0 font-medium tabular-nums text-slate-900">RWF {{ number_format((float) $row['line_total'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="my-4 border-t border-dashed border-slate-200"></div>
                            <div class="flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Subtotal (excl. delivery)</p>
                                    <p class="mt-1 text-xs text-slate-500">Taxes &amp; delivery confirmed at checkout</p>
                                </div>
                                <p class="text-2xl font-bold tracking-tight text-[#2A5C38] tabular-nums">RWF {{ number_format((float) $subtotal, 2) }}</p>
                            </div>
                            <a
                                href="{{ route('storefront.checkout') }}"
                                class="mt-4 flex w-full items-center justify-center rounded-xl bg-[#CA9636] px-5 py-3.5 text-sm font-semibold text-[#3E3C38] hover:bg-[#B07F28]"
                            >
                                Proceed to checkout
                            </a>
                            <a href="{{ route('storefront.shop') }}" class="mt-3 block text-center text-sm font-medium text-[#2A5C38] hover:underline">
                                Add more items
                            </a>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/80 px-5 py-3 text-[11px] leading-relaxed text-slate-500">
                            Checkout opens a secure form for delivery details. Our team confirms payment before dispatch.
                        </div>
                    </div>
                </div>
            </aside>
        @endif
    </div>
</x-layouts.storefront>
