<x-layouts.admin title="Fixed assets">
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold">Fixed assets</h2>
            <p class="mt-2 text-sm text-slate-600">
                Machinery, equipment and vehicles, with the depreciation schedule derived from cost and useful
                life. This single schedule feeds the income statement, the balance sheet and the cash flow
                add-back, so the charge can no longer differ between reports.
            </p>
        </div>

        @include('admin.finance._nav')

        <form method="POST" action="{{ route('admin.finance.fixed-assets.store') }}" class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-3">
            @csrf
            <label class="text-sm">Asset name<input required name="name" maxlength="150" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Category<input name="category" maxlength="100" value="{{ old('category') }}" placeholder="Machinery" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Acquired on<input required type="date" name="acquisition_date" value="{{ old('acquisition_date') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Cost (RWF)<input required type="number" step="0.01" min="0" name="cost" value="{{ old('cost') }}" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
            <label class="text-sm">Residual value (RWF)<input required type="number" step="0.01" min="0" name="salvage_value" value="{{ old('salvage_value', 0) }}" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
            <label class="text-sm">Useful life (months)<input required type="number" min="1" max="1200" name="useful_life_months" value="{{ old('useful_life_months', 120) }}" class="mt-1 w-full rounded-lg border p-2 text-right"></label>
            <label class="text-sm">Supplier
                <select name="supplier_id" class="mt-1 w-full rounded-lg border p-2"><option value="">—</option>
                    @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach
                </select>
            </label>
            <label class="text-sm">Paid from
                <select name="cash_account_id" class="mt-1 w-full rounded-lg border p-2"><option value="">—</option>
                    @foreach($accounts as $account)<option value="{{ $account->id }}" @selected(old('cash_account_id') == $account->id)>{{ $account->name }}</option>@endforeach
                </select>
            </label>
            <div class="flex items-end"><button class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white">Register asset</button></div>
            <p class="text-sm text-slate-600 md:col-span-3">
                Straight line only. Depreciation starts in the month of acquisition and the final month absorbs
                any rounding, so the asset depreciates to exactly its residual value and never past it.
            </p>
        </form>

        <div class="overflow-x-auto rounded-xl border bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100"><tr><th class="p-3">Asset</th><th class="p-3">Acquired</th><th class="p-3 text-right">Cost</th><th class="p-3 text-right">Monthly charge</th><th class="p-3 text-right">Accumulated</th><th class="p-3 text-right">Net book value</th><th class="p-3"></th></tr></thead>
                <tbody>
                @forelse($assets as $asset)
                    <tr class="border-t">
                        <td class="p-3"><span class="font-medium">{{ $asset->name }}</span>@if($asset->category)<span class="block text-slate-500">{{ $asset->category }}</span>@endif</td>
                        <td class="p-3">{{ $asset->acquisition_date?->format('d M Y') }}<span class="block text-slate-500">{{ $asset->useful_life_months }} months</span></td>
                        <td class="p-3 text-right tabular-nums">{{ number_format((float) $asset->cost, 2) }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format($asset->monthlyCharge(), 2) }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format($asset->accumulatedDepreciationAt($asOf), 2) }}</td>
                        <td class="p-3 text-right font-semibold tabular-nums">{{ number_format($asset->netBookValueAt($asOf), 2) }}</td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.finance.fixed-assets.destroy', $asset) }}" onsubmit="return confirm('Remove {{ $asset->name }} from the register?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-700 underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-slate-500">No assets registered, so no depreciation is charged anywhere. Register the machinery to start the schedule.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-sm text-slate-600">Accumulated and net book value shown as at {{ $asOf->format('d M Y') }}.</p>
        <x-admin.table-pagination :paginator="$assets" label="assets" />
    </section>
</x-layouts.admin>
