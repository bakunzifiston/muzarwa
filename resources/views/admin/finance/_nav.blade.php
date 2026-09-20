@php($tabs = [
    'admin.finance.opening-balances.index' => 'Opening balances',
    'admin.finance.cash-accounts.index' => 'Cash accounts',
    'admin.finance.supplier-payments.index' => 'Supplier payments',
    'admin.finance.fixed-assets.index' => 'Fixed assets',
    'admin.finance.loans.index' => 'Loans',
    'admin.finance.equity.index' => 'Equity',
])
<nav class="flex flex-wrap gap-2 border-b pb-3 text-sm">
    @foreach($tabs as $route => $label)
        <a href="{{ route($route) }}"
           class="rounded-lg px-3 py-1.5 {{ request()->routeIs(str_replace('.index', '.*', $route)) ? 'bg-teal-700 font-semibold text-white' : 'border text-slate-700 hover:bg-slate-50' }}">{{ $label }}</a>
    @endforeach
    <a href="{{ route('admin.reports.financial.index') }}" class="ml-auto rounded-lg border px-3 py-1.5 text-teal-700">Financial statements →</a>
</nav>
@if(session('status'))<p class="rounded-lg bg-teal-50 p-3 text-sm text-teal-800">{{ session('status') }}</p>@endif
@if($errors->any())
    <div class="rounded-lg bg-rose-50 p-3 text-sm text-rose-800">
        <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
