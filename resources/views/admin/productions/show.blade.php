<x-layouts.admin title="View Production">
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-slate-900">Production Details</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.productions.edit', $production) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Edit</a>
                <form method="POST" action="{{ route('admin.productions.destroy', $production) }}" onsubmit="return confirm('Delete this production?')">@csrf @method('DELETE')<button class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white">Delete</button></form>
            </div>
        </div>
        @if (session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>@endif
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div><dt class="text-sm text-slate-500">Batch</dt><dd class="mt-1 text-sm font-medium">{{ $production->batch_id }}</dd></div>
                <div><dt class="text-sm text-slate-500">Product</dt><dd class="mt-1 text-sm font-medium">{{ $production->product?->type }}</dd></div>
                <div><dt class="text-sm text-slate-500">Produced</dt><dd class="mt-1 text-sm font-medium">{{ number_format($production->quantity_produced, 2) }} bottles</dd></div>
                <div><dt class="text-sm text-slate-500">Damaged</dt><dd class="mt-1 text-sm font-medium">{{ number_format($production->damaged, 2) }}</dd></div>
                <div><dt class="text-sm text-slate-500">Date</dt><dd class="mt-1 text-sm font-medium">{{ \Illuminate\Support\Carbon::parse($production->production_date)->format('Y-m-d') }}</dd></div>
                <div><dt class="text-sm text-slate-500">Staff</dt><dd class="mt-1 text-sm font-medium">{{ $production->responsible_staff ?: '—' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Barcode</dt><dd class="mt-1 text-sm font-medium">{{ $production->barcode }}</dd></div>
            </dl>
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-slate-800">Raw Materials Consumed</h3>
                <p class="text-xs text-slate-400">Each material links to its delivery (lot), where you can see its full usage history.</p>
                <table class="mt-2 min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-1.5 text-left">Material (lot)</th>
                            <th class="px-3 py-1.5 text-right">Qty Used</th>
                            <th class="px-3 py-1.5 text-right">Unit Cost</th>
                            <th class="px-3 py-1.5 text-right">Line Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($materials as $material)
                            <tr>
                                <td class="px-3 py-1.5">
                                    @if ($material['record'])
                                        <a href="{{ route('admin.inventory-records.show', $material['record']) }}" class="text-teal-700 hover:underline">{{ $material['name'] }}</a>
                                        <span class="text-xs text-slate-400">· {{ $material['record']->record_date?->format('Y-m-d') }}@if ($material['record']->invoice_number) · Inv. {{ $material['record']->invoice_number }}@endif</span>
                                    @else
                                        {{ $material['name'] }}
                                    @endif
                                </td>
                                <td class="px-3 py-1.5 text-right">{{ number_format($material['quantity'], 2) }}</td>
                                <td class="px-3 py-1.5 text-right text-slate-600">{{ $material['unit_cost'] !== null ? number_format($material['unit_cost'], 0) . ' RWF' : '—' }}</td>
                                <td class="px-3 py-1.5 text-right font-medium">{{ $material['line_cost'] !== null ? number_format($material['line_cost'], 0) . ' RWF' : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">No raw materials recorded for this batch.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($materialsCost > 0)
                        <tfoot>
                            <tr class="border-t border-slate-200 font-semibold">
                                <td colspan="3" class="px-3 py-2 text-right">Material cost of this batch</td>
                                <td class="px-3 py-2 text-right">{{ number_format($materialsCost, 0) }} RWF</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @can('view-analytics')
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Sales from this batch</h3>
                    <p class="text-xs text-slate-400">Every sale that drew finished goods from {{ $production->batch_id }}.</p>
                </div>
                @unless ($salesReconciles)
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                        Ledger mismatch: {{ number_format($unitsLeftBatch, 2) }} left the batch, but sales total {{ number_format($batchSalesQty, 2) }}
                    </span>
                @endunless
            </div>
            <table class="mt-3 min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-1.5 text-left">Sale</th>
                        <th class="px-3 py-1.5 text-left">Date</th>
                        <th class="px-3 py-1.5 text-left">Customer</th>
                        <th class="px-3 py-1.5 text-right">Qty</th>
                        <th class="px-3 py-1.5 text-right">Unit Price</th>
                        <th class="px-3 py-1.5 text-right">Line Total</th>
                        <th class="px-3 py-1.5 text-left">Payment</th>
                        <th class="px-3 py-1.5 text-left">Recorded by</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($batchSales as $row)
                        <tr>
                            <td class="px-3 py-1.5">
                                <a href="{{ route('admin.sales.show', $row['sale']) }}" class="text-teal-700 hover:underline">{{ $row['sales_id'] }}</a>
                            </td>
                            <td class="px-3 py-1.5 text-slate-600">{{ $row['sale_date'] ? \Illuminate\Support\Carbon::parse($row['sale_date'])->format('Y-m-d') : '—' }}</td>
                            <td class="px-3 py-1.5">{{ $row['customer'] ?: '—' }}</td>
                            <td class="px-3 py-1.5 text-right">{{ number_format($row['quantity'], 2) }}</td>
                            <td class="px-3 py-1.5 text-right text-slate-600">{{ number_format($row['unit_price'], 0) }} RWF</td>
                            <td class="px-3 py-1.5 text-right font-medium">{{ number_format($row['line_total'], 0) }} RWF</td>
                            <td class="px-3 py-1.5"><x-admin.sale-status-badge :status="$row['payment_status']" type="payment" /></td>
                            <td class="px-3 py-1.5 text-slate-600">{{ $row['recorded_by'] ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">No sales recorded from this batch yet.</td></tr>
                    @endforelse
                </tbody>
                @if ($batchSales->isNotEmpty())
                    <tfoot>
                        <tr class="border-t border-slate-200 font-semibold">
                            <td colspan="3" class="px-3 py-2 text-right">Total sold from this batch</td>
                            <td class="px-3 py-2 text-right">{{ number_format($batchSalesQty, 2) }}</td>
                            <td></td>
                            <td class="px-3 py-2 text-right">{{ number_format($batchSalesRevenue, 0) }} RWF</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @endcan
    </section>
</x-layouts.admin>
