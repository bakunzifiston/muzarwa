<x-layouts.admin title="Supplier payments">
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold">Supplier payments</h2>
            <p class="mt-2 text-sm text-slate-600">
                Money actually paid out to suppliers. Until a payment is recorded here the purchase counts as
                an unpaid payable, and nothing shows as leaving the business on the cash flow statement.
            </p>
        </div>

        @include('admin.finance._nav')

        <form method="POST" action="{{ route('admin.finance.supplier-payments.store') }}" class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-3">
            @csrf
            <label class="text-sm md:col-span-2">Settle a purchase
                <select name="inventory_record_id" class="mt-1 w-full rounded-lg border p-2">
                    <option value="">Not linked to a specific purchase</option>
                    @foreach($unpaid as $record)
                        <option value="{{ $record->id }}" @selected(old('inventory_record_id') == $record->id)>
                            {{ $record->record_date?->format('d M Y') }} · {{ $record->item_name }} · {{ $record->supplier_name }} · outstanding {{ number_format((float) $record->total_amount - (float) $record->amount_paid, 2) }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm">Supplier
                <select name="supplier_id" class="mt-1 w-full rounded-lg border p-2">
                    <option value="">—</option>
                    @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach
                </select>
            </label>
            <label class="text-sm">Amount (RWF)<input required type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
            <label class="text-sm">Paid on<input required type="date" name="paid_at" max="{{ now()->toDateString() }}" value="{{ old('paid_at', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Method<select name="method" class="mt-1 w-full rounded-lg border p-2">@foreach($methods as $method)<option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>@endforeach</select></label>
            <label class="text-sm">Paid from
                <select name="cash_account_id" class="mt-1 w-full rounded-lg border p-2">
                    <option value="">—</option>
                    @foreach($accounts as $account)<option value="{{ $account->id }}" @selected(old('cash_account_id') == $account->id)>{{ $account->name }}</option>@endforeach
                </select>
            </label>
            <label class="text-sm">Reference<input name="reference" maxlength="150" value="{{ old('reference') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <div class="flex items-end"><button class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white">Record payment</button></div>
        </form>

        <div class="overflow-x-auto rounded-xl border bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100"><tr><th class="p-3">Paid on</th><th class="p-3">Supplier</th><th class="p-3">Purchase</th><th class="p-3">Method</th><th class="p-3">Account</th><th class="p-3 text-right">Amount</th><th class="p-3"></th></tr></thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr class="border-t">
                        <td class="p-3">{{ $payment->paid_at?->format('d M Y') }}</td>
                        <td class="p-3">{{ $payment->supplier?->name ?? $payment->supplier_name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $payment->inventoryRecord?->item_name ?? 'Not linked' }}</td>
                        <td class="p-3">{{ $payment->method }}</td>
                        <td class="p-3">{{ $payment->cashAccount?->name ?? '—' }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.finance.supplier-payments.destroy', $payment) }}" onsubmit="return confirm('Remove this payment?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-700 underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-slate-500">No supplier payments recorded. Operating cash outflow will read as zero until some are.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.table-pagination :paginator="$payments" label="payments" />
    </section>
</x-layouts.admin>
