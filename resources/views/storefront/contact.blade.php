@php
    $mapsEmbedUrl = $siteSettings['maps_embed_url'] ?? null;
    $mapsOpenUrl  = $siteSettings['maps_open_url']  ?? null;

    $primaryPhoneVal = $primaryPhone?->value ?? null;
    $primaryWaVal    = $primaryWhatsapp?->value ?? null;
@endphp

<x-layouts.storefront title="Contact" seo-description="Get in touch with muzarwa in Rwamagana, Rwanda. Call +250 784 421 127 or email info@muzarwa.com.">
    <section class="relative overflow-hidden border-b border-[#1F4A2C]/30 bg-[#2A5C38] text-white">
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#CA9636]/15 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-white/5 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#163824]/40 via-transparent to-[#1F4A2C]/50" aria-hidden="true"></div>
        <x-storefront.banner-spice />
        <div class="js-hero-animate relative mx-auto max-w-7xl px-4 py-10 sm:py-14 lg:px-8">
            <nav class="text-xs font-medium text-[#CA9636]/95" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <li><a href="{{ route('storefront.home') }}" class="transition hover:text-white">Home</a></li>
                    <li class="text-white/50" aria-hidden="true">/</li>
                    <li class="text-white/90" aria-current="page">Contact</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Get In Touch</h1>
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-slate-100/90 sm:text-base">
                We would love to hear from you.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 lg:px-8 lg:py-14">
        @if (session('status'))
            <div class="mb-10 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- Contact cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-stagger>

            <div class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#2A5C38]/10 text-[#2A5C38]" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Website</p>
                    <a href="{{ config('brand.website_url') }}" class="mt-1 block font-semibold text-[#2A5C38] hover:underline">{{ config('brand.website') }}</a>
                </div>
            </div>

            {{-- Phone numbers --}}
            @forelse($sitePhones as $phone)
                <a href="tel:+{{ preg_replace('/\D/', '', $phone->value) }}" class="group flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5 transition hover:border-[#2A5C38]/25">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#2A5C38]/10 text-[#2A5C38] transition group-hover:bg-[#2A5C38]/15" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $phone->label }}</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $phone->value }}</p>
                        <p class="mt-1 text-xs text-[#2A5C38] underline-offset-2 group-hover:underline">Tap to call</p>
                    </div>
                </a>
            @empty
                <a href="tel:+{{ config('brand.phone_digits') }}" class="group flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5 transition hover:border-[#2A5C38]/25">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#2A5C38]/10 text-[#2A5C38]" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ config('brand.phone') }}</p>
                        <p class="mt-1 text-xs text-[#2A5C38] underline-offset-2 group-hover:underline">Tap to call</p>
                    </div>
                </a>
            @endforelse

            {{-- Email addresses --}}
            <div class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#CA9636]/30 text-[#3E3C38]" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Email</p>
                    @forelse($siteEmails as $email)
                        <a href="mailto:{{ $email->value }}" class="mt-1 block break-all {{ $loop->first ? 'font-semibold text-[#2A5C38]' : 'text-xs font-medium text-slate-600 mt-2' }} underline-offset-2 hover:underline hover:text-[#2A5C38]">
                            {{ $email->value }}
                        </a>
                    @empty
                        <a href="mailto:{{ config('brand.email') }}" class="mt-1 block break-all font-semibold text-[#2A5C38] underline-offset-2 hover:underline">
                            {{ config('brand.email') }}
                        </a>
                    @endforelse
                </div>
            </div>

            {{-- Address --}}
            @forelse($siteAddresses as $address)
                <div class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $address->label }}</p>
                        <p class="mt-1 text-sm font-semibold leading-snug text-slate-900">{{ $siteSettings['company_name'] ?? config('brand.name') }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $address->value }}</p>
                    </div>
                </div>
            @empty
                <div class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Location</p>
                        <p class="mt-1 text-sm font-semibold leading-snug text-slate-900">{{ config('brand.name') }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ config('brand.location') }}</p>
                    </div>
                </div>
            @endforelse

            {{-- Business hours --}}
            @if(!empty($siteSettings['hours_weekday']))
                <div class="flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-800" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hours</p>
                        @if(!empty($siteSettings['hours_weekday']))
                            <p class="mt-1 text-sm font-medium text-slate-900">{{ $siteSettings['hours_weekday'] }}</p>
                        @endif
                        @if(!empty($siteSettings['hours_saturday']))
                            <p class="mt-1 text-sm text-slate-600">{{ $siteSettings['hours_saturday'] }}</p>
                        @endif
                        @if(!empty($siteSettings['hours_sunday']))
                            <p class="mt-1 text-xs text-slate-500">{{ $siteSettings['hours_sunday'] }}</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- WhatsApp channels --}}
            @forelse($siteWhatsapps as $wa)
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $wa->value) }}" target="_blank" rel="noopener noreferrer" class="group flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5 transition hover:border-emerald-300/80">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-700 transition group-hover:bg-emerald-500/20" aria-hidden="true">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $wa->label }}</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $wa->value }}</p>
                        <p class="mt-1 text-xs text-emerald-700 underline-offset-2 group-hover:underline">Opens WhatsApp Chat</p>
                    </div>
                </a>
            @empty
                <a href="https://wa.me/{{ config('brand.phone_digits') }}" target="_blank" rel="noopener noreferrer" class="group flex gap-4 rounded-2xl border border-slate-200/90 bg-white p-5 transition hover:border-emerald-300/80">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-700" aria-hidden="true">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">WhatsApp</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ config('brand.phone') }}</p>
                        <p class="mt-1 text-xs text-emerald-700 underline-offset-2 group-hover:underline">Opens WhatsApp Chat</p>
                    </div>
                </a>
            @endforelse

        </div>

        <div class="mt-12 grid gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] lg:gap-12 xl:grid-cols-[minmax(0,1fr)_28rem]" data-stagger>
            {{-- Form --}}
            <div>
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-950 shadow-sm">
                        <p class="font-semibold">Please review the highlighted fields.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white">
                    <div class="border-b border-slate-100 px-6 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A5C38]">Write to us</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">Send a message</h2>
                        <p class="mt-2 text-sm text-slate-600">Partnerships, wholesale, product questions, or support—we read every submission.</p>
                    </div>
                    <form action="{{ route('storefront.contact.submit') }}" method="POST" class="space-y-5 px-6 py-6">
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="contact-name" class="mb-1.5 block text-sm font-medium text-slate-800">Full name <span class="text-rose-600">*</span></label>
                                <input id="contact-name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required maxlength="120"
                                    class="w-full rounded-xl border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2 {{ $errors->has('name') ? 'border-rose-300 bg-rose-50 focus:ring-rose-500/30' : 'border-slate-200 bg-white focus:border-[#2A5C38]/35 focus:ring-[#2A5C38]/20' }}"
                                    placeholder="Your full name">
                                @error('name')<p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="contact-email" class="mb-1.5 block text-sm font-medium text-slate-800">Email address <span class="text-rose-600">*</span></label>
                                <input id="contact-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="120"
                                    class="w-full rounded-xl border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-rose-300 bg-rose-50 focus:ring-rose-500/30' : 'border-slate-200 bg-white focus:border-[#2A5C38]/35 focus:ring-[#2A5C38]/20' }}"
                                    placeholder="you@example.com">
                                @error('email')<p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="contact-phone" class="mb-1.5 block text-sm font-medium text-slate-800">Phone number <span class="text-slate-400 font-normal">(optional)</span></label>
                                <input id="contact-phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" maxlength="50"
                                    class="w-full rounded-xl border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2 {{ $errors->has('phone') ? 'border-rose-300 bg-rose-50 focus:ring-rose-500/30' : 'border-slate-200 bg-white focus:border-[#2A5C38]/35 focus:ring-[#2A5C38]/20' }}"
                                    placeholder="+250 …">
                                @error('phone')<p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="contact-subject" class="mb-1.5 block text-sm font-medium text-slate-800">Subject</label>
                                <input id="contact-subject" name="subject" type="text" value="{{ old('subject', request()->query('subject', '')) }}" maxlength="160"
                                    class="w-full rounded-xl border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2 {{ $errors->has('subject') ? 'border-rose-300 bg-rose-50 focus:ring-rose-500/30' : 'border-slate-200 bg-white focus:border-[#2A5C38]/35 focus:ring-[#2A5C38]/20' }}"
                                    placeholder="e.g. Wholesale inquiry — Kigali">
                                @error('subject')<p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="contact-message" class="mb-1.5 block text-sm font-medium text-slate-800">Message <span class="text-rose-600">*</span></label>
                                <textarea id="contact-message" name="message" rows="6" required maxlength="4000"
                                    class="w-full resize-y rounded-xl border px-4 py-2.5 text-sm leading-relaxed transition focus:outline-none focus:ring-2 {{ $errors->has('message') ? 'border-rose-300 bg-rose-50 focus:ring-rose-500/30' : 'border-slate-200 bg-white focus:border-[#2A5C38]/35 focus:ring-[#2A5C38]/20' }}"
                                    placeholder="How can we help?">{{ old('message') }}</textarea>
                                @error('message')<p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-slate-500">By sending this form you agree we may store your details to respond to your inquiry.</p>
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#CA9636] px-6 py-3 text-sm font-semibold text-[#3E3C38] shadow-sm transition hover:bg-[#B07F28] active:scale-[0.99] sm:w-auto">
                                Send message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Map + social --}}
            <aside class="space-y-6">
                @if($mapsEmbedUrl)
                    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h2 class="text-lg font-semibold text-slate-900">Find us</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $primaryAddress?->value ?? 'Our location' }}</p>
                        </div>
                        <div class="aspect-[4/3] w-full bg-slate-200 sm:aspect-video">
                            <iframe title="Map" src="{{ $mapsEmbedUrl }}" class="h-full w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        </div>
                        @if($mapsOpenUrl)
                            <div class="px-5 py-4">
                                <a href="{{ $mapsOpenUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex text-sm font-semibold text-[#2A5C38] underline-offset-2 hover:underline">
                                    Open in Google Maps →
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                @if($siteSocials->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200/90 bg-white p-5">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Social</h3>
                        <p class="mt-3 text-sm text-slate-600">Follow {{ $siteSettings['company_name'] ?? config('brand.name') }} for launches, tastings, and farm stories.</p>
                        <ul class="mt-4 flex flex-wrap gap-3 text-sm font-medium">
                            @foreach($siteSocials as $social)
                                <li>
                                    <a href="{{ $social->value }}" target="_blank" rel="noopener noreferrer"
                                        class="rounded-lg bg-slate-50 px-3 py-2 text-slate-800 ring-1 ring-slate-200 transition hover:bg-slate-100">
                                        {{ $social->label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-2xl border border-slate-200/90 bg-[#f8fafc] px-5 py-4 text-sm text-slate-700">
                    <p class="flex gap-3">
                        <svg class="h-10 w-10 shrink-0 text-[#2A5C38]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <span>Prefer the shop? Browse <a href="{{ route('storefront.shop') }}" class="font-semibold text-[#2A5C38] underline-offset-2 hover:underline">muzarwa products</a> anytime.</span>
                    </p>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.storefront>
