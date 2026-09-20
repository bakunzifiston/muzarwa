<x-layouts.admin title="Equity">
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold">Equity</h2>
            <p class="mt-2 text-sm text-slate-600">
                Capital put into the business and taken out of it. Contributions and share capital increase
                equity and cash; drawings reduce both. Drawings are not an expense and never reach the
                income statement.
            </p>
        </div>

        @include('admin.finance._nav')

        <form method="POST" action="{{ route('admin.finance.equity.store') }}" class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-3">
            @csrf
            <label class="text-sm">Type<select name="type" class="mt-1 w-full rounded-lg border p-2">@foreach($types as $value => $label)<option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm">Amount (RWF)<input required type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
            <label class="text-sm">Date<input required type="date" name="occurred_at" max="{{ now()->toDateString() }}" value="{{ old('occurred_at', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm md:col-span-2">Description<input name="description" maxlength="255" value="{{ old('description') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Account
                <select name="cash_account_id" class="mt-1 w-full rounded-lg border p-2"><option value="">—</option>
                    @foreach($accounts as $account)<option value="{{ $account->id }}" @selected(old('cash_account_id') == $account->id)>{{ $account->name }}</option>@endforeach
                </select>
            </label>
            <div class="flex items-end md:col-span-3"><button class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white">Record movement</button></div>
        </form>

        <div class="overflow-x-auto rounded-xl border bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100"><tr><th class="p-3">Date</th><th class="p-3">Type</th><th class="p-3">Description</th><th class="p-3">Account</th><th class="p-3 text-right">Effect on equity</th><th class="p-3"></th></tr></thead>
                <tbody>
                @forelse($movements as $movement)
                    <tr class="border-t">
                        <td class="p-3">{{ $movement->occurred_at?->format('d M Y') }}</td>
                        <td class="p-3">{{ $types[$movement->type] ?? $movement->type }}</td>
                        <td class="p-3 text-slate-600">{{ $movement->description ?: '—' }}</td>
                        <td class="p-3">{{ $movement->cashAccount?->name ?? '—' }}</td>
                        <td class="p-3 text-right tabular-nums {{ $movement->signedAmount() < 0 ? 'text-rose-700' : '' }}">
                            {{ $movement->signedAmount() < 0 ? '('.number_format(abs($movement->signedAmount()), 2).')' : number_format($movement->signedAmount(), 2) }}
                        </td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.finance.equity.destroy', $movement) }}" onsubmit="return confirm('Remove this movement?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-700 underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-slate-500">No equity movements recorded.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.table-pagination :paginator="$movements" label="movements" />
    </section>
</x-layouts.admin>
