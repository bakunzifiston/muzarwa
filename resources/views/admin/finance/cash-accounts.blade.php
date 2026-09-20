<x-layouts.admin title="Cash accounts">
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold">Cash accounts</h2>
            <p class="mt-2 text-sm text-slate-600">
                The tills, bank accounts and mobile-money wallets money actually sits in. Payments are
                attributed to an account so the cash flow statement can be reconciled against real balances.
            </p>
        </div>

        @include('admin.finance._nav')

        <form method="POST" action="{{ route('admin.finance.cash-accounts.store') }}" class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-3">
            @csrf
            <label class="text-sm">Account name<input required name="name" maxlength="150" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Type<select name="type" class="mt-1 w-full rounded-lg border p-2">@foreach($types as $value => $label)<option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm">Account / wallet number<input name="account_number" maxlength="100" value="{{ old('account_number') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <label class="text-sm">Opening balance (RWF)<input required type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', 0) }}" class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums"></label>
            <label class="text-sm">Opening balance date<input type="date" name="opening_balance_date" value="{{ old('opening_balance_date') }}" class="mt-1 w-full rounded-lg border p-2"></label>
            <div class="flex items-end"><button class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white">Add account</button></div>
            <p class="text-sm text-slate-600 md:col-span-3">
                These opening balances should add up to the cash figure on the
                <a class="text-teal-700 underline" href="{{ route('admin.finance.opening-balances.index') }}">opening balances</a> page.
                The balance sheet uses the cut-over figure and warns when the two disagree.
            </p>
        </form>

        <div class="overflow-x-auto rounded-xl border bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100"><tr><th class="p-3">Account</th><th class="p-3">Type</th><th class="p-3">Number</th><th class="p-3 text-right">Opening balance</th><th class="p-3">As at</th><th class="p-3">Active</th><th class="p-3"></th></tr></thead>
                <tbody>
                @forelse($accounts as $account)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $account->name }}</td>
                        <td class="p-3">{{ $account->typeLabel() }}</td>
                        <td class="p-3 text-slate-600">{{ $account->account_number ?: '—' }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format((float) $account->opening_balance, 2) }}</td>
                        <td class="p-3">{{ $account->opening_balance_date?->format('d M Y') ?: '—' }}</td>
                        <td class="p-3">{{ $account->is_active ? 'Yes' : 'No' }}</td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.finance.cash-accounts.destroy', $account) }}" onsubmit="return confirm('Remove {{ $account->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-700 underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-slate-500">No cash accounts yet. Add the till and any bank or MoMo accounts.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.table-pagination :paginator="$accounts" label="accounts" />
    </section>
</x-layouts.admin>
