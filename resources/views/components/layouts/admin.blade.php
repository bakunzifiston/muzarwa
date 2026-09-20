@php
    $topbarLabel = match(true) {
        request()->routeIs('admin.dashboard')                                            => 'Dashboard',
        request()->routeIs('admin.sales.*')                                              => 'Sales',
        request()->routeIs('admin.customers.*')                                          => 'Customers',
        request()->routeIs('admin.suppliers.*')                                          => 'Suppliers',
        request()->routeIs('admin.inventory-records.*')                                  => 'Inventory',
        request()->routeIs('admin.productions.*')                                        => 'Production',
        request()->routeIs('admin.products.*')                                           => 'Products',
        request()->routeIs('admin.employees.*')                                          => 'Employees',
        request()->routeIs('admin.users.*')                                              => 'Users',
        request()->routeIs('admin.expenses.*') || request()->routeIs('admin.expense-categories.*') => 'Expenses',
        request()->routeIs('admin.reports.*')                                            => 'Reports',
        request()->routeIs('admin.team-performance')                                     => 'Team Performance',
        request()->routeIs('admin.data-health')                                          => 'Data Health',
        request()->routeIs('admin.contact-submissions.*')                                => 'Contact Messages',
        request()->routeIs('admin.settings.*')                                           => 'Settings',
        request()->routeIs('admin.ecommerce.*')                                          => 'E-Commerce',
        default                                                                          => 'Admin',
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} - {{ config('app.name', 'muzarwa') }}</title>
    @vite(['resources/css/admin/app.css', 'resources/js/admin/app.js'])
</head>
<body class="h-full text-ink antialiased">
    <div class="admin-shell">
        <div class="admin-frame">
        <aside id="adminSidebar" class="admin-sidebar">
            <div class="mb-2 rounded-2xl bg-cream-light p-3 ring-1 ring-white/15">
                <x-brand-logo class="mx-auto h-auto w-full max-w-[11.5rem] object-contain" alt="muzarwa" />
            </div>

            <nav class="p-3 space-y-2">
                @php($coreOpen = request()->routeIs('admin.dashboard') || request()->routeIs('admin.users.*') || request()->routeIs('admin.employees.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.inventory-records.*') || request()->routeIs('admin.productions.*') || request()->routeIs('admin.sales.*') || request()->routeIs('admin.customers.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.expense-categories.*') || request()->routeIs('admin.reports.*') || request()->routeIs('admin.contact-submissions.*'))
                @php($settingsOpen = request()->routeIs('admin.settings.*'))
                <details class="admin-nav-group" @if($coreOpen) open @endif>
                    <summary class="admin-nav-group-summary">
                        <span>Core ERP</span>
                        <span class="admin-nav-group-chevron">▾</span>
                    </summary>
                    <div class="mt-1 space-y-1">
                        @can('view-analytics')
                            <x-admin.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-admin.nav-link>
                        @endcan
                        @can('manage-catalog')
                            <x-admin.nav-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')">
                                Employees
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                                Products
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.inventory-records.index')" :active="request()->routeIs('admin.inventory-records.*')">
                                Inventory
                            </x-admin.nav-link>
                        @endcan
                        @can('record-production')
                            <x-admin.nav-link :href="route('admin.productions.index')" :active="request()->routeIs('admin.productions.*')">
                                {{ auth()->user()->isProduction() ? 'My Production' : 'Production' }}
                            </x-admin.nav-link>
                        @endcan
                        @can('record-sales')
                            <x-admin.nav-link :href="route('admin.sales.index')" :active="request()->routeIs('admin.sales.*')">
                                {{ auth()->user()->isSales() ? 'My Sales' : 'Sales' }}
                            </x-admin.nav-link>
                        @endcan
                        @can('manage-catalog')
                            <x-admin.nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                                Customers
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.suppliers.index')" :active="request()->routeIs('admin.suppliers.*')">
                                Suppliers
                            </x-admin.nav-link>
                            <x-admin.nav-link
                                :href="route('admin.expenses.index')"
                                :active="request()->routeIs('admin.expenses.*') || request()->routeIs('admin.expense-categories.*')"
                            >
                                <span class="inline-flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 fill-none stroke-current" stroke-width="1.8" aria-hidden="true">
                                        <path d="M9 3h6l1 2h4v16H4V5h5l1-2Z" />
                                        <path d="M8 10h8M8 14h5" />
                                    </svg>
                                    Expenses
                                </span>
                            </x-admin.nav-link>
                        @endcan
                        @can('view-analytics')
                            <x-admin.nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                                Reports
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.reports.financial.index')" :active="request()->routeIs('admin.reports.financial.*')">
                                Financial Statements
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.reports.profitability')" :active="request()->routeIs('admin.reports.profitability*')">
                                Profitability
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.finance.opening-balances.index')" :active="request()->routeIs('admin.finance.*')">
                                Finance Ledgers
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.team-performance')" :active="request()->routeIs('admin.team-performance')">
                                Team Performance
                            </x-admin.nav-link>
                            <x-admin.nav-link :href="route('admin.data-health')" :active="request()->routeIs('admin.data-health')">
                                Data Health
                            </x-admin.nav-link>
                        @endcan
                        @can('manage-users')
                            <x-admin.nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                Users
                            </x-admin.nav-link>
                        @endcan
                        @can('manage-catalog')
                            <x-admin.nav-link :href="route('admin.contact-submissions.index')" :active="request()->routeIs('admin.contact-submissions.*')">
                                Contact Messages
                            </x-admin.nav-link>
                        @endcan
                    </div>
                </details>

                @can('manage-catalog')
                <details class="admin-nav-group" @if($settingsOpen) open @endif>
                    <summary class="admin-nav-group-summary">
                        <span>Settings</span>
                        <span class="admin-nav-group-chevron">▾</span>
                    </summary>
                    <div class="mt-1 space-y-1">
                        <x-admin.nav-link :href="route('admin.settings.general')" :active="request()->routeIs('admin.settings.general*')">
                            General
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.settings.contacts.index')" :active="request()->routeIs('admin.settings.contacts.*')">
                            Contact Channels
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.settings.email-routing.index')" :active="request()->routeIs('admin.settings.email-routing.*')">
                            Email Routing
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.settings.images.index')" :active="request()->routeIs('admin.settings.images.*')">
                            Site Images
                        </x-admin.nav-link>
                    </div>
                </details>
                @endcan

                @can('manage-catalog')
                @php($ecommerceOpen = request()->routeIs('admin.ecommerce.*'))
                <details class="admin-nav-group" @if($ecommerceOpen) open @endif>
                    <summary class="admin-nav-group-summary">
                        <span>E-Commerce</span>
                        <span class="admin-nav-group-chevron">▾</span>
                    </summary>
                    <div class="mt-1 space-y-1">
                        <x-admin.nav-link :href="route('admin.ecommerce.catalog')" :active="request()->routeIs('admin.ecommerce.catalog')">
                            Catalog
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.ecommerce.customers')" :active="request()->routeIs('admin.ecommerce.customers')">
                            Customers
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.ecommerce.sales', ['module' => 'orders', 'channel' => 'online'])" :active="request()->routeIs('admin.ecommerce.sales')">
                            Orders
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.ecommerce.fulfillment')" :active="request()->routeIs('admin.ecommerce.fulfillment')">
                            Fulfillment
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.ecommerce.analytics')" :active="request()->routeIs('admin.ecommerce.analytics')">
                            Analytics
                        </x-admin.nav-link>
                    </div>
                </details>
                @endcan
            </nav>

            @if (auth()->check() && auth()->user())
                <div class="mt-6 rounded-2xl border border-white/15 bg-white/8 p-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-base font-semibold text-white ring-1 ring-white/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-200/70">{{ auth()->user()->roleLabel() }}</p>
                        </div>
                    </div>

                    @can('manage-users')
                        <a
                            href="{{ route('admin.users.edit', auth()->user()) }}"
                            class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-medium text-white transition hover:bg-white/20"
                        >
                            Edit Profile
                        </a>
                    @endcan
                </div>
            @endif
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand via-mango to-chili"></div>
                <div class="flex items-center gap-2">
                    <button id="sidebarToggle" type="button" class="admin-icon-button lg:hidden" aria-label="Toggle sidebar">
                        <span class="block h-0.5 w-5 bg-slate-700"></span>
                        <span class="mt-1 block h-0.5 w-5 bg-slate-700"></span>
                        <span class="mt-1 block h-0.5 w-5 bg-slate-700"></span>
                    </button>
                    <p class="text-sm font-medium text-slate-600">{{ $topbarLabel }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="admin-icon-button">Logout</button>
                    </form>
                </div>
            </header>

            <main class="p-0">
                <div class="admin-canvas">
                    {{ $slot }}
                </div>
            </main>
        </div>
        </div>
    </div>
</body>
</html>
