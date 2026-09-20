@php
    $coverUrl = $album->coverImageUrl();
    $photoCount = (int) ($album->photos_count ?? $album->photos?->count() ?? 0);
    $albumUrl = $album->exists
        ? route('storefront.gallery.album', $album)
        : route('storefront.gallery');
@endphp

<a
    href="{{ $albumUrl }}"
    class="group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2A5C38] focus-visible:ring-offset-2"
>
    @if ($coverUrl)
        <img
            src="{{ $coverUrl }}"
            alt=""
            width="640"
            height="480"
            loading="lazy"
            decoding="async"
            class="absolute inset-0 h-full w-full object-cover"
        >
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent" aria-hidden="true"></div>
    <div class="absolute inset-x-0 bottom-0 p-5">
        <p class="text-lg font-semibold text-white">{{ $album->title }}</p>
        <p class="mt-1 text-sm text-white/80">{{ $photoCount }} {{ Str::plural('photo', $photoCount) }}</p>
    </div>
</a>
