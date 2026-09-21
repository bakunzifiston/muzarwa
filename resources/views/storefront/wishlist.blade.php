<x-layouts.storefront title="Wishlist">
    <section class="border-b border-slate-200/80 bg-[#f8fafc]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:py-12 lg:px-8">
            <nav class="text-xs font-medium text-[#2A5C38]" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <li><a href="{{ route('storefront.home') }}" class="text-slate-600 transition hover:text-[#2A5C38]">Home</a></li>
                    <li class="text-slate-400" aria-hidden="true">/</li>
                    <li><a href="{{ route('storefront.shop') }}" class="text-slate-600 transition hover:text-[#2A5C38]">Shop</a></li>
                    <li class="text-slate-400" aria-hidden="true">/</li>
                    <li class="font-semibold text-slate-900" aria-current="page">Wishlist</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Wishlist</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Saved products for future purchase.
            </p>
            <x-storefront.shop-subnav />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 lg:px-8 lg:py-14">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-16 text-center">
                <p class="text-lg font-semibold text-slate-900">Your wishlist is empty</p>
                <p class="mt-2 text-sm text-slate-600">Save products from the shop and they will appear here.</p>
                <a href="{{ route('storefront.shop') }}" class="mt-8 inline-flex rounded-xl bg-[#2A5C38] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Browse products</a>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($items as $product)
                    <article class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white">
                        @if ($product->image_path)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-cover">
                        @else
                            <div class="flex aspect-[4/3] w-full items-center justify-center bg-slate-100 text-sm text-slate-500">No image</div>
                        @endif
                        <div class="p-5">
                            <p class="text-xs uppercase tracking-wide text-slate-500">{{ $product->type }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $product->name }}</h2>
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('storefront.product', $product->id) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">Details</a>
                                <form method="POST" action="{{ route('storefront.cart.add', $product->id) }}">
                                    @csrf
                                    <button class="rounded-lg bg-[#2A5C38] px-3 py-2 text-xs font-medium text-white hover:bg-[#1F4A2C]">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.storefront>
