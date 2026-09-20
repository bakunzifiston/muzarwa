<x-layouts.admin title="Loans">
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold">Loans</h2>
            <p class="mt-2 text-sm text-slate-600">
                Borrowings and their repayments. Each repayment is split into principal and interest: interest
                is an expense on the income statement, principal is a financing outflow that reduces the debt.
                The workbook template put both under operating expenses, which overstated costs.
            </p>
        </div>

        @include('admin.finance._nav')

        <form method="POST" action="{{ route('admin.finance.loans.store') }}" class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-3">
            @csrf
            <label class="text-sm">Lender<input required name="lender" maxlength="150" value="{{ old('lender') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Reference<input name="reference" maxlength="150" value="{{ old('reference') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Principal (RWF)<input required type="number" step="0.01" min="0.01" name="principal" value="{{ old('principal') }}" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
            <label class="text-sm">Interest rate (%)<input required type="number" step="0.0001" min="0" name="interest_rate" value="{{ old('interest_rate', 0) }}" class="mt-1 w-full rounded-lg border p-2 text-right"></label>
            <label class="text-sm">Rate basis<select name="rate_basis" class="mt-1 w-full rounded-lg border p-2">@foreach(['annual' => 'Per year', 'monthly' => 'Per month', 'flat' => 'Flat'] as $v => $l)<option value="{{ $v }}" @selected(old('rate_basis') === $v)>{{ $l }}</option>@endforeach</select></label>
            <label class="text-sm">Drawn on<input required type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Term (months)<input required type="number" min="1" max="600" name="term_months" value="{{ old('term_months', 24) }}" class="mt-1 w-full rounded-lg border p-2 text-right"></label>
            <label class="text-sm">Repayment frequency<select name="repayment_frequency" class="mt-1 w-full rounded-lg border p-2">@foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'annual' => 'Annual', 'irregular' => 'Irregular'] as $v => $l)<option value="{{ $v }}" @selected(old('repayment_frequency') === $v)>{{ $l }}</option>@endforeach</select></label>
            <label class="text-sm">Status<select name="status" class="mt-1 w-full rounded-lg border p-2">@foreach($statuses as $v => $l)<option value="{{ $v }}" @selected(old('status') === $v)>{{ $l }}</option>@endforeach</select></label>
            <label class="text-sm">Paid into
                <select name="cash_account_id" class="mt-1 w-full rounded-lg border p-2"><option value="">—</option>
                    @foreach($accounts as $account)<option value="{{ $account->id }}" @selected(old('cash_account_id') == $account->id)>{{ $account->name }}</option>@endforeach
                </select>
            </label>
            <div class="flex items-end"><button class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white">Record loan</button></div>
        </form>

        <div class="space-y-4">
            @forelse($loans as $loan)
                <div class="rounded-xl border bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold">{{ $loan->lender }} @if($loan->reference)<span class="font-normal text-slate-500">· {{ $loan->reference }}</span>@endif</h3>
                            <p class="text-sm text-slate-600">
                                Drawn {{ $loan->start_date?->format('d M Y') }} · {{ number_format((float) $loan->principal, 2) }} RWF over {{ $loan->term_months }} months ·
                                {{ $loan->interest_rate }}% {{ $loan->rate_basis }} · {{ $statuses[$loan->status] ?? $loan->status }}
                            </p>
                            <p class="mt-2 text-sm">
                                Outstanding at {{ $asOf->format('d M Y') }}: <span class="font-semibold tabular-nums">{{ number_format($loan->outstandingAt($asOf), 2) }} RWF</span>
                                <span class="text-slate-500">(due within a year {{ number_format($loan->currentPortionAt($asOf), 2) }}, after {{ number_format($loan->nonCurrentPortionAt($asOf), 2) }})</span>
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.finance.loans.destroy', $loan) }}" onsubmit="return confirm('Remove this loan and its repayments?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-rose-700 underline">Remove</button>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('admin.finance.loans.repayments.store', $loan) }}" class="mt-4 grid gap-3 border-t pt-4 md:grid-cols-5">
                        @csrf
                        <label class="text-xs">Paid on<input required type="date" name="paid_at" max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-lg border p-2"></label>
                        <label class="text-xs">Total paid<input required type="number" step="0.01" min="0.01" name="total_amount" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
                        <label class="text-xs">Of which principal<input required type="number" step="0.01" min="0" name="principal_portion" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
                        <label class="text-xs">Of which interest<input required type="number" step="0.01" min="0" name="interest_portion" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
                        <div class="flex items-end"><button class="w-full rounded-lg border border-teal-700 px-3 py-2 text-sm font-semibold text-teal-700">Add repayment</button></div>
                    </form>
                </div>
            @empty
                <p class="rounded-xl border bg-white p-6 text-slate-500">No loans recorded.</p>
            @endforelse
        </div>
        <x-admin.table-pagination :paginator="$loans" label="loans" />

        @if($repayments->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border bg-white">
                <table class="w-full text-left text-sm">
                    <caption class="p-3 text-left font-semibold">Recent repayments</caption>
                    <thead class="bg-slate-100"><tr><th class="p-3">Paid on</th><th class="p-3">Lender</th><th class="p-3 text-right">Total</th><th class="p-3 text-right">Principal</th><th class="p-3 text-right">Interest</th><th class="p-3"></th></tr></thead>
                    <tbody>
                    @foreach($repayments as $repayment)
                        <tr class="border-t">
                            <td class="p-3">{{ $repayment->paid_at?->format('d M Y') }}</td>
                            <td class="p-3">{{ $repayment->loan?->lender }}</td>
                            <td class="p-3 text-right tabular-nums">{{ number_format((float) $repayment->total_amount, 2) }}</td>
                            <td class="p-3 text-right tabular-nums">{{ number_format((float) $repayment->principal_portion, 2) }}</td>
                            <td class="p-3 text-right tabular-nums">{{ number_format((float) $repayment->interest_portion, 2) }}</td>
                            <td class="p-3 text-right">
                                <form method="POST" action="{{ route('admin.finance.loans.repayments.destroy', $repayment) }}" onsubmit="return confirm('Remove this repayment?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-700 underline">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.admin>
