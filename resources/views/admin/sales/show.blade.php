<x-layouts.admin title="View Sale">
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Sale</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ $sale->sales_id }}</h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d M Y') }} ·
                    @if ($sale->customer)
                        <a href="{{ route('admin.customers.show', $sale->customer) }}" class="text-teal-700 hover:underline">{{ $sale->customer_name }}</a>
                    @else
                        {{ $sale->customer_name }}
                    @endif
                    @if ($sale->sales_channel === 'Online Store')
                        · <span class="rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-800 ring-1 ring-sky-200">Online Store</span>
                    @else
                        · {{ $sale->sales_channel }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.sales.edit', $sale) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Edit</a>
                @can('delete-records')
                    <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale?')">@csrf @method('DELETE')<button class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white">Delete</button></form>
                @endcan
            </div>
        </div>

        @if (session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>@endif
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Payment position at a glance --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.kpi-card label="Invoice Total" :value="number_format($sale->total_revenue ?? 0, 0) . ' RWF'" icon="banknotes" tone="teal" />
            <x-admin.kpi-card label="Amount Paid" :value="number_format($sale->amount_paid ?? 0, 0) . ' RWF'" icon="banknotes" tone="emerald" />
            <x-admin.kpi-card label="Balance Due" :value="number_format($sale->balance_due, 0) . ' RWF'" icon="clock" :tone="$sale->balance_due > 0 ? 'rose' : 'emerald'" />
            <x-admin.kpi-card
                label="Due Date"
                :value="$sale->due_date ? $sale->due_date->format('d M Y') . ($sale->is_overdue ? ' · OVERDUE' : '') : 'Not set'"
                icon="clock"
                :tone="$sale->is_overdue ? 'rose' : 'slate'"
            />
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                {{-- Sale details --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-800">Details</h3>
                    <dl class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div><dt class="text-sm text-slate-500">Phone</dt><dd class="mt-1 text-sm font-medium">{{ $sale->customer_Phone ?? '—' }}</dd></div>
                        <div><dt class="text-sm text-slate-500">Email</dt><dd class="mt-1 text-sm font-medium">{{ $sale->customer_email ?? $sale->barcode ?? '—' }}</dd></div>
                        <div><dt class="text-sm text-slate-500">Invoice No.</dt><dd class="mt-1 text-sm font-medium">{{ $sale->invoice_number ?? '—' }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-sm text-slate-500">Delivery address</dt><dd class="mt-1 text-sm font-medium">{{ $sale->delivery_address ?? '—' }}</dd></div>
                        <div>
                            <dt class="text-sm text-slate-500">Status</dt>
                            <dd class="mt-1 flex gap-2">
                                <x-admin.sale-status-badge :status="$sale->payment_status" type="payment" />
                                <x-admin.sale-status-badge :status="$sale->delivery_status" type="delivery" />
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-slate-800">Items</h3>
                        <table class="mt-2 min-w-full text-sm">
                            <thead class="bg-slate-50"><tr><th class="px-2 py-1.5 text-left">Product</th><th class="px-2 py-1.5 text-left">Batch</th><th class="px-2 py-1.5 text-right">Qty</th><th class="px-2 py-1.5 text-right">Price</th><th class="px-2 py-1.5 text-right">Total</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($sale->items as $item)
                                    <tr><td class="px-2 py-1.5">{{ $item->product?->type }}</td><td class="px-2 py-1.5">{{ $item->production?->batch_id }}</td><td class="px-2 py-1.5 text-right">{{ number_format($item->quantity_sold,2) }}</td><td class="px-2 py-1.5 text-right">{{ number_format($item->unit_price,0) }} RWF</td><td class="px-2 py-1.5 text-right">{{ number_format($item->line_total,0) }} RWF</td></tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-slate-200 font-semibold"><td colspan="4" class="px-2 py-2 text-right">Invoice Total</td><td class="px-2 py-2 text-right">{{ number_format($sale->total_revenue ?? 0, 0) }} RWF</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Payments ledger --}}
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-3">
                        <h3 class="text-sm font-semibold text-slate-800">Payment History</h3>
                        <span class="text-xs text-slate-400">{{ $sale->payments->count() }} payment(s)</span>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Date</th><th class="px-4 py-2 text-right">Amount</th><th class="px-4 py-2 text-left">Method</th><th class="px-4 py-2 text-left">Reference</th><th class="px-4 py-2 text-left">Recorded By</th><th class="px-4 py-2"></th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($sale->payments->sortByDesc('paid_at') as $payment)
                                <tr>
                                    <td class="px-4 py-2">
                                        {{ $payment->paid_at?->format('Y-m-d') }}
                                        @if ($payment->is_backfilled)<span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">estimated</span>@endif
                                    </td>
                                    <td class="px-4 py-2 text-right font-medium text-emerald-700">{{ number_format($payment->amount, 0) }} RWF</td>
                                    <td class="px-4 py-2">{{ $payment->method }}</td>
                                    <td class="px-4 py-2 text-slate-500">{{ $payment->reference ?? '—' }}</td>
                                    <td class="px-4 py-2 text-slate-500">{{ $payment->recordedBy?->name ?? 'System' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        @can('delete-records')
                                            <form method="POST" action="{{ route('admin.sales.payments.destroy', [$sale, $payment]) }}" onsubmit="return confirm('Remove this payment? The balance will be recalculated.')">
                                                @csrf @method('DELETE')
                                                <button class="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 hover:bg-rose-100">Remove</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-300">—</span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No payments recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Record payment --}}
                @if ($sale->balance_due > 0)
                    <div class="rounded-xl border border-teal-200 bg-teal-50/50 p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-teal-900">Record a Payment</h3>
                        <p class="mt-1 text-xs text-teal-800/80">Outstanding balance: <strong>{{ number_format($sale->balance_due, 0) }} RWF</strong></p>
                        <form method="POST" action="{{ route('admin.sales.payments.store', $sale) }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Amount (RWF)</label>
                                <input name="amount" type="number" step="0.01" min="0.01" max="{{ $sale->balance_due }}" required value="{{ old('amount', $sale->balance_due) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600">Method</label>
                                    <select name="method" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        @foreach (\App\Models\SalePayment::METHODS as $method)
                                            <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600">Date</label>
                                    <input name="paid_at" type="date" required value="{{ old('paid_at', now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Reference (optional)</label>
                                <input name="reference" value="{{ old('reference') }}" placeholder="MoMo TX ID, receipt no..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <button class="w-full rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800">Record Payment</button>
                        </form>
                    </div>
                @else
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-800 shadow-sm">
                        <p class="font-semibold">Fully paid ✓</p>
                        <p class="mt-1 text-xs">Settled {{ $sale->paid_at?->format('d M Y') ?? '' }}</p>
                    </div>
                @endif

                {{-- Status timeline --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-800">Timeline</h3>
                    <ol class="mt-4 space-y-4 border-l border-slate-200 pl-4">
                        @foreach ($sale->statusEvents->sortByDesc('created_at') as $event)
                            <li class="relative">
                                <span class="absolute -left-[1.30rem] top-1 h-2.5 w-2.5 rounded-full {{ $event->field === 'payment_status' ? 'bg-teal-500' : 'bg-sky-500' }}"></span>
                                <p class="text-sm text-slate-700">
                                    <span class="font-medium">{{ $event->field === 'payment_status' ? 'Payment' : 'Delivery' }}</span>
                                    @if ($event->from_status)
                                        : {{ $event->from_status }} → <span class="font-semibold">{{ $event->to_status }}</span>
                                    @else
                                        set to <span class="font-semibold">{{ $event->to_status }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $event->created_at->format('d M Y H:i') }}
                                    @if ($event->changedBy) · by {{ $event->changedBy->name }}@endif
                                </p>
                            </li>
                        @endforeach
                        <li class="relative">
                            <span class="absolute -left-[1.30rem] top-1 h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                            <p class="text-sm text-slate-700"><span class="font-medium">Sale created</span></p>
                            <p class="text-xs text-slate-400">{{ $sale->created_at?->format('d M Y H:i') }}</p>
                        </li>
                    </ol>
                    @if ($sale->delivered_at)
                        <p class="mt-4 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">Delivered on {{ $sale->delivered_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts.admin>
