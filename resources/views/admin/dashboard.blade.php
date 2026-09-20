<x-layouts.admin title="Dashboard">
    @php
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $fmt = fn ($n) => number_format($n, 0, '.', ',');
        $card = 'rounded-xl border border-slate-200/90 bg-white p-5';
        $input = 'rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-[#2A5C38]/40 focus:outline-none focus:ring-2 focus:ring-[#2A5C38]/20';
        $alerts = collect([
            ($low_stock_count ?? 0) > 0 ? ['href' => route('admin.inventory-records.stock'), 'text' => $low_stock_count . ' low stock'] : null,
            ($expiring_count ?? 0) > 0 ? ['href' => route('admin.inventory-records.stock'), 'text' => $expiring_count . ' expiring'] : null,
            ($overdue_ar_count ?? 0) > 0 ? ['href' => route('admin.customers.index'), 'text' => $overdue_ar_count . ' overdue AR'] : null,
            ($overdue_ap_count ?? 0) > 0 ? ['href' => route('admin.suppliers.index'), 'text' => $overdue_ap_count . ' overdue AP'] : null,
        ])->filter();
    @endphp

    <section class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
                <p class="mt-1 text-sm text-slate-600">Orders, cash, and stock for the selected period.</p>
            </div>
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Period</label>
                    <select name="period" class="{{ $input }}">
                        @foreach (['today'=>'Today','yesterday'=>'Yesterday','last_7_days'=>'Last 7 days','last_30_days'=>'Last 30 days','this_month'=>'This month','last_month'=>'Last month','this_year'=>'This year','all_time'=>'All time'] as $k => $labelOption)
                            <option value="{{ $k }}" @selected($period === $k)>{{ $labelOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">From</label>
                    <input name="start_date" type="date" value="{{ $start_date }}" class="{{ $input }}">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">To</label>
                    <input name="end_date" type="date" value="{{ $end_date }}" class="{{ $input }}">
                </div>
                <button type="submit" class="rounded-lg bg-[#2A5C38] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1F4A2C]">Apply</button>
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </form>
        </div>

        @if ($alerts->isNotEmpty())
            <p class="text-sm text-slate-600">
                Attention:
                @foreach ($alerts as $alert)
                    <a href="{{ $alert['href'] }}" class="font-semibold text-[#2A5C38] hover:underline">{{ $alert['text'] }}</a>@if (!$loop->last)<span class="text-slate-300"> · </span>@endif
                @endforeach
            </p>
        @endif

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-6">
            <x-admin.kpi-card
                :href="route('admin.sales.index')"
                label="Orders"
                :value="$fmt($total_orders)"
                icon="cart"
                tone="teal"
            />
            <x-admin.kpi-card
                :href="route('admin.sales.index', ['delivery_status' => 'Pending'])"
                label="Pending"
                :value="$fmt($pending_delivery_count)"
                icon="clock"
                tone="gold"
            />
            <x-admin.kpi-card
                :href="route('admin.sales.index', ['delivery_status' => 'Delivered'])"
                label="Revenue"
                :value="$fmt($revenue_recognised)"
                unit="RWF"
                icon="chart"
                tone="emerald"
            />
            <x-admin.kpi-card
                :href="route('admin.sales.index', ['payment_status' => 'Paid'])"
                label="Collected"
                :value="$fmt($cash_collected)"
                unit="RWF"
                icon="banknotes"
                tone="teal"
            />
            <x-admin.kpi-card
                label="Net profit"
                :value="$fmt($net_profit)"
                unit="RWF"
                icon="trend"
                :tone="$net_profit >= 0 ? 'emerald' : 'rose'"
            />
            <x-admin.kpi-card
                :href="route('admin.customers.index', ['balance' => 'due', 'sort' => 'outstanding', 'direction' => 'desc'])"
                label="Receivable"
                :value="$fmt($ar_balance)"
                unit="RWF"
                icon="wallet"
                :tone="$ar_balance > 0 ? 'gold' : 'slate'"
            />
        </div>

        <dl class="grid grid-cols-2 gap-x-8 gap-y-5 rounded-2xl border border-slate-200/90 bg-white px-5 py-5 sm:grid-cols-4">
            @foreach ([
                ['Total revenue', $fmt($total_revenue_all), 'RWF', 'text-slate-900'],
                ['Avg order', $fmt($avg_order_value), 'RWF', 'text-slate-900'],
                ['Units sold', $fmt($total_units_sold), null, 'text-slate-900'],
                ['Expenses', $fmt($cogs + $operating_expenses), 'RWF', 'text-[#BD4B2D]'],
                ['COGS', $fmt($cogs), 'RWF', 'text-slate-900'],
                ['Operating', $fmt($operating_expenses), 'RWF', 'text-slate-900'],
                ['Gross profit', $fmt($gross_profit), 'RWF', $gross_profit >= 0 ? 'text-[#2A5C38]' : 'text-[#BD4B2D]'],
                ['Payable', $fmt($ap_balance), 'RWF', $ap_balance > 0 ? 'text-[#BD4B2D]' : 'text-slate-900'],
            ] as [$statLabel, $statValue, $statUnit, $statColor])
                <div>
                    <dt class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">{{ $statLabel }}</dt>
                    <dd class="mt-1.5 flex items-baseline gap-1.5 {{ $statColor }}">
                        <span class="text-sm font-semibold tabular-nums">{{ $statValue }}</span>
                        @if ($statUnit)
                            <span class="text-[10px] font-semibold tracking-wide text-slate-400">{{ $statUnit }}</span>
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>

        <div class="{{ $card }} p-6">
            <p class="text-sm font-semibold text-slate-900">Revenue vs expenses</p>
            <p class="mt-0.5 text-xs text-slate-500">Monthly comparison</p>
            <div class="mt-4 h-64"><canvas id="chartTrend"></canvas></div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="{{ $card }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-900">Revenue by channel</p>
                    <a href="{{ route('admin.sales.index') }}" class="text-xs font-semibold text-[#2A5C38] hover:underline">View</a>
                </div>
                <div class="mt-4 h-48"><canvas id="chartRevenueChannel"></canvas></div>
            </div>
            <div class="{{ $card }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-900">Payment status</p>
                    <a href="{{ route('admin.sales.index') }}" class="text-xs font-semibold text-[#2A5C38] hover:underline">View</a>
                </div>
                <div class="mt-4 h-48"><canvas id="chartPaymentStatus"></canvas></div>
            </div>
            <div class="{{ $card }} p-5">
                <p class="text-sm font-semibold text-slate-900">Top products</p>
                <div class="mt-4 h-48"><canvas id="chartTopProducts"></canvas></div>
            </div>
            <div class="{{ $card }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-900">Stock on hand</p>
                    <a href="{{ route('admin.inventory-records.stock') }}" class="text-xs font-semibold text-[#2A5C38] hover:underline">View</a>
                </div>
                <div class="mt-4 h-48"><canvas id="chartStockOnHand"></canvas></div>
            </div>
            <div class="{{ $card }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-900">Production</p>
                    <a href="{{ route('admin.productions.index') }}" class="text-xs font-semibold text-[#2A5C38] hover:underline">View</a>
                </div>
                <div class="mt-4 h-48"><canvas id="chartProductionProduct"></canvas></div>
            </div>
            <div class="{{ $card }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-900">Top customers</p>
                    <a href="{{ route('admin.customers.index', ['sort' => 'lifetime_value', 'direction' => 'desc']) }}" class="text-xs font-semibold text-[#2A5C38] hover:underline">View</a>
                </div>
                <div class="mt-4 h-48"><canvas id="chartTopCustomers"></canvas></div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            const brand = '#2A5C38';
            const mango = '#CA9636';
            const chili = '#BD4B2D';
            const leaf = '#4A7A55';
            const cream = '#d3cab9';
            const palette = [brand, mango, chili, leaf, cream];

            const doughnutOpts = {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11 }, padding: 12, usePointStyle: true }
                    }
                }
            };

            const channelData = @json($by_channel);
            new Chart(document.getElementById('chartRevenueChannel'), {
                type: 'doughnut',
                data: {
                    labels: channelData.map(d => d.channel),
                    datasets: [{ data: channelData.map(d => d.revenue), backgroundColor: palette, borderWidth: 2, borderColor: '#fff' }]
                },
                options: doughnutOpts
            });

            const paymentData = @json($payment_status_breakdown);
            new Chart(document.getElementById('chartPaymentStatus'), {
                type: 'doughnut',
                data: {
                    labels: paymentData.map(d => d.status),
                    datasets: [{ data: paymentData.map(d => d.count), backgroundColor: [brand, mango, '#94a3b8'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: doughnutOpts
            });

            const barOpts = () => ({
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: v => v.toLocaleString() } }
                }
            });

            const hBarOpts = () => ({
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: v => v.toLocaleString(), font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            });

            const topProducts = @json($top_products_by_revenue);
            new Chart(document.getElementById('chartTopProducts'), {
                type: 'bar',
                data: { labels: topProducts.map(d => d.name), datasets: [{ data: topProducts.map(d => d.revenue), backgroundColor: brand, borderRadius: 4 }] },
                options: hBarOpts()
            });

            const stockData = @json($finished_goods_by_product);
            new Chart(document.getElementById('chartStockOnHand'), {
                type: 'bar',
                data: { labels: stockData.map(d => d.name), datasets: [{ data: stockData.map(d => d.remaining), backgroundColor: leaf, borderRadius: 4 }] },
                options: barOpts()
            });

            const prodData = @json($production_by_product);
            new Chart(document.getElementById('chartProductionProduct'), {
                type: 'bar',
                data: { labels: prodData.map(d => d.label), datasets: [{ data: prodData.map(d => d.value), backgroundColor: brand, borderRadius: 4 }] },
                options: barOpts()
            });

            const custData = @json($top_customers);
            new Chart(document.getElementById('chartTopCustomers'), {
                type: 'bar',
                data: { labels: custData.map(d => d.name), datasets: [{ data: custData.map(d => d.revenue), backgroundColor: mango, borderRadius: 4 }] },
                options: hBarOpts()
            });

            new Chart(document.getElementById('chartTrend'), {
                type: 'line',
                data: {
                    labels: @json($months),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($monthly_revenue),
                        borderColor: brand,
                        backgroundColor: 'rgba(42, 92, 56, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    }, {
                        label: 'Expenses',
                        data: @json($monthly_expenses),
                        borderColor: mango,
                        backgroundColor: 'rgba(202, 150, 54, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 16, usePointStyle: true } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 12 } } },
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: v => v.toLocaleString(), font: { size: 12 } } }
                    }
                }
            });
        })();
    </script>
</x-layouts.admin>
