<x-layouts.admin title="Opening balances">
    <section class="space-y-5">
        <div>
            <h2 class="text-2xl font-semibold">Opening balances</h2>
            <p class="mt-2 text-sm text-slate-600">
                The cut-over position the balance sheet starts from. Enter what the business owned, owed and
                had invested on the day before your first reporting period. History stays in the workbooks;
                this one dated set is the bridge to it, so the two 2025 spreadsheets never have to be reconciled.
            </p>
        </div>

        @include('admin.finance._nav')

        @if($check !== null)
            <div class="rounded-lg border p-4 text-sm {{ abs($check) < 0.01 ? 'border-teal-200 bg-teal-50 text-teal-900' : 'border-rose-300 bg-rose-50 text-rose-900' }}">
                @if(abs($check) < 0.01)
                    These figures balance. Statements starting after {{ $selected }} will use them.
                @else
                    These figures are out by RWF {{ number_format($check, 2) }}. Every column of every statement built on them will be out by the same amount.
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('admin.finance.opening-balances.store') }}" class="space-y-5 rounded-xl border bg-white p-5">
            @csrf
            <div class="flex flex-wrap items-end gap-4">
                <label class="text-sm">Position as at
                    <input required type="date" name="as_of_date" value="{{ old('as_of_date', $selected) }}" class="mt-1 block rounded-lg border p-2">
                </label>
                @if($dates->isNotEmpty())
                    <label class="text-sm">Load an existing set
                        <select name="load" onchange="window.location = '{{ route('admin.finance.opening-balances.index') }}?as_of_date=' + this.value" class="mt-1 block rounded-lg border p-2">
                            @foreach($dates as $date)<option value="{{ $date }}" @selected($date === $selected)>{{ $date }}</option>@endforeach
                        </select>
                    </label>
                @endif
            </div>

            @php($group = function ($keys, $heading) use ($lines, $amounts) {
                return ['heading' => $heading, 'keys' => $keys];
            })

            <div class="grid gap-6 md:grid-cols-2">
                @foreach([
                    ['Assets', array_merge($assetLines, $contraLines)],
                    ['Liabilities', $liabilityLines],
                    ['Equity', $equityLines],
                ] as [$heading, $keys])
                    <div class="space-y-3">
                        <h3 class="font-semibold">{{ $heading }}</h3>
                        @foreach($keys as $key)
                            <label class="block text-sm">
                                {{ $lines[$key] }}
                                @if(in_array($key, $contraLines, true))<span class="text-slate-500">(enter as a positive figure; it is subtracted)</span>@endif
                                <input type="number" step="0.01" name="amounts[{{ $key }}]"
                                       value="{{ old('amounts.'.$key, $amounts[$key] ?? 0) }}"
                                       class="mt-1 w-full rounded-lg border p-2 text-right tabular-nums">
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <label class="block text-sm">Notes
                <textarea name="notes" rows="2" maxlength="2000" class="mt-1 w-full rounded-lg border p-2">{{ old('notes') }}</textarea>
            </label>

            <p class="text-sm text-slate-600">
                Assets less liabilities less equity must be zero. Retained earnings is normally the balancing
                figure: it is the accumulated profit the business has kept since it started.
            </p>

            <button class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white">Save opening balances</button>
        </form>

        @if($dates->isNotEmpty())
            <form method="POST" action="{{ route('admin.finance.opening-balances.destroy') }}"
                  onsubmit="return confirm('Delete the opening balance set for {{ $selected }}? Statements will fall back to assuming the business started with nothing.')">
                @csrf @method('DELETE')
                <input type="hidden" name="as_of_date" value="{{ $selected }}">
                <button class="text-sm text-rose-700 underline">Delete the set dated {{ $selected }}</button>
            </form>
        @endif
    </section>
</x-layouts.admin>
