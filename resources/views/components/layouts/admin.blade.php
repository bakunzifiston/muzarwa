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
        request()->routeIs('admin.finance.*')                                            => 'Finance',
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
            <a href="{{ route('storefront.home') }}" class="admin-brand">
                <img
                    src="{{ asset('images/muzarwa-logo-sidebar.png') }}"
                    alt="muzarwa"
                    width="897"
                    height="739"
                    class="admin-brand-mark"
                >
            </a>

            <nav class="space-y-5">
                @can('view-analytics')
                    <div class="space-y-1">
                        <x-admin.nav-link icon="home" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-admin.nav-link>
                    </div>
                @endcan

                @if (auth()->user()?->can('manage-catalog') || auth()->user()?->can('record-production') || auth()->user()?->can('record-sales'))
                    <div>
                        <p class="admin-nav-heading">Operations</p>
                        <div class="space-y-0.5">
                            @can('manage-catalog')
                                <x-admin.nav-link icon="cube" :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">Products</x-admin.nav-link>
                                <x-admin.nav-link icon="archive" :href="route('admin.inventory-records.index')" :active="request()->routeIs('admin.inventory-records.*')">Inventory</x-admin.nav-link>
                            @endcan
                            @can('record-production')
                                <x-admin.nav-link icon="building" :href="route('admin.productions.index')" :active="request()->routeIs('admin.productions.*')">
                                    {{ auth()->user()->isProduction() ? 'My Production' : 'Production' }}
                                </x-admin.nav-link>
                            @endcan
                            @can('record-sales')
                                <x-admin.nav-link icon="cart" :href="route('admin.sales.index')" :active="request()->routeIs('admin.sales.*')">
                                    {{ auth()->user()->isSales() ? 'My Sales' : 'Sales' }}
                                </x-admin.nav-link>
                            @endcan
                            @can('manage-catalog')
                                <x-admin.nav-link icon="users" :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">Customers</x-admin.nav-link>
                                <x-admin.nav-link icon="truck" :href="route('admin.suppliers.index')" :active="request()->routeIs('admin.suppliers.*')">Suppliers</x-admin.nav-link>
                                <x-admin.nav-link icon="badge" :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')">Employees</x-admin.nav-link>
                                <x-admin.nav-link
                                    icon="cash"
                                    :href="route('admin.expenses.index')"
                                    :active="request()->routeIs('admin.expenses.*') || request()->routeIs('admin.expense-categories.*')"
                                >Expenses</x-admin.nav-link>
                            @endcan
                        </div>
                    </div>
                @endif

                @can('view-analytics')
                    <div>
                        <p class="admin-nav-heading">Finance</p>
                        <div class="space-y-0.5">
                            <x-admin.nav-link icon="chart" :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">Reports</x-admin.nav-link>
                            <x-admin.nav-link icon="document" :href="route('admin.reports.financial.index')" :active="request()->routeIs('admin.reports.financial.*')">Statements</x-admin.nav-link>
                            <x-admin.nav-link icon="trend" :href="route('admin.reports.profitability')" :active="request()->routeIs('admin.reports.profitability*')">Profitability</x-admin.nav-link>
                            <x-admin.nav-link icon="book" :href="route('admin.finance.opening-balances.index')" :active="request()->routeIs('admin.finance.*')">Ledgers</x-admin.nav-link>
                            <x-admin.nav-link icon="users" :href="route('admin.team-performance')" :active="request()->routeIs('admin.team-performance')">Team</x-admin.nav-link>
                            <x-admin.nav-link icon="shield" :href="route('admin.data-health')" :active="request()->routeIs('admin.data-health')">Data health</x-admin.nav-link>
                        </div>
                    </div>
                @endcan

                @can('manage-catalog')
                    <div>
                        <p class="admin-nav-heading">Shop</p>
                        <div class="space-y-0.5">
                            <x-admin.nav-link icon="grid" :href="route('admin.ecommerce.catalog')" :active="request()->routeIs('admin.ecommerce.catalog')">Catalog</x-admin.nav-link>
                            <x-admin.nav-link icon="user" :href="route('admin.ecommerce.customers')" :active="request()->routeIs('admin.ecommerce.customers')">Shop customers</x-admin.nav-link>
                            <x-admin.nav-link icon="bag" :href="route('admin.ecommerce.sales', ['module' => 'orders', 'channel' => 'online'])" :active="request()->routeIs('admin.ecommerce.sales')">Orders</x-admin.nav-link>
                            <x-admin.nav-link icon="clipboard" :href="route('admin.ecommerce.fulfillment')" :active="request()->routeIs('admin.ecommerce.fulfillment')">Fulfillment</x-admin.nav-link>
                            <x-admin.nav-link icon="pie" :href="route('admin.ecommerce.analytics')" :active="request()->routeIs('admin.ecommerce.analytics')">Analytics</x-admin.nav-link>
                        </div>
                    </div>
                @endcan

                @can('manage-catalog')
                    <div>
                        <p class="admin-nav-heading">Settings</p>
                        <div class="space-y-0.5">
                            <x-admin.nav-link icon="cog" :href="route('admin.settings.general')" :active="request()->routeIs('admin.settings.general*')">General</x-admin.nav-link>
                            <x-admin.nav-link icon="phone" :href="route('admin.settings.contacts.index')" :active="request()->routeIs('admin.settings.contacts.*')">Contacts</x-admin.nav-link>
                            <x-admin.nav-link icon="mail" :href="route('admin.settings.email-routing.index')" :active="request()->routeIs('admin.settings.email-routing.*')">Email</x-admin.nav-link>
                            <x-admin.nav-link icon="photo" :href="route('admin.settings.images.index')" :active="request()->routeIs('admin.settings.images.*')">Images</x-admin.nav-link>
                            <x-admin.nav-link icon="chat" :href="route('admin.contact-submissions.index')" :active="request()->routeIs('admin.contact-submissions.*')">Messages</x-admin.nav-link>
                        </div>
                    </div>
                @endcan

                @can('manage-users')
                    <div class="space-y-0.5">
                        <x-admin.nav-link icon="users" :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Users</x-admin.nav-link>
                    </div>
                @endcan
            </nav>

            @if (auth()->check() && auth()->user())
                <div class="mt-auto pt-6">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-0.5 text-xs text-white/60">{{ auth()->user()->roleLabel() }}</p>
                    @can('manage-users')
                        <a href="{{ route('admin.users.edit', auth()->user()) }}" class="mt-3 inline-block text-xs font-medium text-white/80 hover:text-white">Edit profile</a>
                    @endcan
                </div>
            @endif
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="flex items-center gap-3">
                    <button id="sidebarToggle" type="button" class="admin-icon-button lg:hidden" aria-label="Toggle sidebar">
                        <span class="block h-0.5 w-5 bg-slate-700"></span>
                        <span class="mt-1 block h-0.5 w-5 bg-slate-700"></span>
                        <span class="mt-1 block h-0.5 w-5 bg-slate-700"></span>
                    </button>
                    <p class="text-sm font-semibold text-slate-800">{{ $topbarLabel }}</p>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-icon-button">Log out</button>
                </form>
            </header>

            <main>
                <div class="admin-canvas">
                    {{ $slot }}
                </div>
            </main>
        </div>
        </div>
    </div>
</body>
</html>
